<?php

namespace App\Http\Controllers;

use App\Models\Historico;
use App\Models\OrdemServico;
use App\Models\PermissaoUnidadeOperacional;
use App\Models\UnidadeOperacional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LaboratorioAceitarAmostraController extends Controller
{
    // Status atribuído à solicitação, às ordens de serviço e às frações
    // quando o laboratório aceita a amostra
    private const STATUS_RECEBIDO_LABORATORIO = 'RECEBIDO PELO LABORATORIO';

    // Status atribuído à solicitação, às ordens de serviço e às frações
    // quando o laboratório rejeita a amostra
    private const STATUS_REJEITADO_LABORATORIO = 'REJEITADO_LABORATORIO';

    // Unidades operacionais em que o usuário logado tem permissão de
    // laboratório (permissao_unidade_operacional.usuario_matricula)
    private function unidadesOperacionaisPermitidas(): array
    {
        $matricula = auth()->user()?->empregado?->matricula;

        if (!$matricula) {
            return [];
        }

        return PermissaoUnidadeOperacional::whereNotNull('unidade_operacional_id')
            ->where('usuario_matricula', $matricula)
            ->pluck('unidade_operacional_id')
            ->all();
    }

    // Tela de filtro (data inicial/final e unidade operacional) e listagem
    // de ordens de serviço para o gerenciamento do cadastro de análises
    public function index(Request $request)
    {
        $validated = $request->validate([
            'data_inicial' => ['nullable', 'date'],
            'data_final' => ['nullable', 'date'],
            'unidade_operacional_id' => ['nullable', 'integer'],
        ]);

        $unidadesPermitidas = $this->unidadesOperacionaisPermitidas();

        $unidadesOperacionais = UnidadeOperacional::whereIn('unidade_operacional_id', $unidadesPermitidas)
            ->orderBy('nome')
            ->get(['unidade_operacional_id', 'nome']);

        $unidadeOperacionalId = $validated['unidade_operacional_id'] ?? null;

        abort_if($unidadeOperacionalId && !in_array($unidadeOperacionalId, $unidadesPermitidas), 403);

        $ordens = $unidadeOperacionalId
            ? OrdemServico::query()
                ->with([
                    'solicitacaoServico.atividade.responsavel',
                    'unidadeOperacional',
                    'fracoesAmostra.execucoes_analises.servico',
                ])
                ->where('status_atual', 'ENVIADO_LABORATORIO')
                ->where('unidade_operacional_id', $unidadeOperacionalId)
                ->when($validated['data_inicial'] ?? null, fn ($query, $data) => $query->whereDate('data_status_atual', '>=', $data))
                ->when($validated['data_final'] ?? null, fn ($query, $data) => $query->whereDate('data_status_atual', '<=', $data))
                ->orderByDesc('ordem_servico_id')
                ->get()
                ->map(fn ($ordem) => [
                    'ordem_servico_id' => $ordem->ordem_servico_id,
                    'material' => $ordem->solicitacaoServico->descricao ?? null,
                    'servicos' => $ordem->fracoesAmostra
                        ->flatMap(fn ($fracao) => $fracao->execucoes_analises->map(fn ($execucao) => $execucao->servico->descricao ?? null))
                        ->filter()
                        ->unique()
                        ->values()
                        ->implode(', ') ?: null,
                    'responsavel_atividade' => $ordem->solicitacaoServico->atividade->responsavel->nome ?? null,
                    'unidade_operacional' => $ordem->unidadeOperacional->nome ?? null,
                    'status' => $ordem->status_atual,
                    'data_aceite_laboratorio' => $ordem->data_aceite_laboratorio,
                ])
                ->values()
            : collect();

        return Inertia::render('Laboratorio/AceitarAmostra', [
            'unidadesOperacionais' => $unidadesOperacionais,
            'filtros' => $request->only(['data_inicial', 'data_final', 'unidade_operacional_id']),
            'ordens' => $ordens,
        ]);
    }

    // Detalhes de uma ordem de serviço específica
    public function show(OrdemServico $ordem_servico)
    {
        abort_unless(in_array($ordem_servico->unidade_operacional_id, $this->unidadesOperacionaisPermitidas()), 403);

        $ordem_servico->load([
            'solicitacaoServico.atividade.responsavel',
            'unidadeOperacional',
            'fracoesAmostra.amostra',
            'fracoesAmostra.execucoes_analises.servico',
        ]);

        $fracoes = $ordem_servico->fracoesAmostra->flatMap(function ($fracao) {
            if ($fracao->execucoes_analises->isEmpty()) {
                return [[
                    'amostra_descricao' => $fracao->amostra->descricao ?? null,
                    'servico_descricao' => null,
                    'fracao_status_atual' => $fracao->status_atual,
                ]];
            }

            return $fracao->execucoes_analises->map(fn ($execucao) => [
                'amostra_descricao' => $fracao->amostra->descricao ?? null,
                'servico_descricao' => $execucao->servico->descricao ?? null,
                'fracao_status_atual' => $fracao->status_atual,
            ]);
        })->values();

        return Inertia::render('Laboratorio/AceitarAmostraShow', [
            'ordem' => [
                'ordem_servico_id' => $ordem_servico->ordem_servico_id,
                'material' => $ordem_servico->solicitacaoServico->descricao ?? null,
                'responsavel_atividade' => $ordem_servico->solicitacaoServico->atividade->responsavel->nome ?? null,
                'unidade_operacional' => $ordem_servico->unidadeOperacional->nome ?? null,
                'status' => $ordem_servico->status_atual,
                'data_aceite_laboratorio' => $ordem_servico->data_aceite_laboratorio,
                'observacao' => $ordem_servico->observacao,
            ],
            'fracoes' => $fracoes,
        ]);
    }

    // Aceita a amostra: a solicitação de serviço, todas as suas ordens de
    // serviço e todas as frações dessas ordens são marcadas como recebidas
    // pelo laboratório, com o respectivo registro de histórico para cada uma.
    public function aceitar(OrdemServico $ordem_servico)
    {
        abort_unless(in_array($ordem_servico->unidade_operacional_id, $this->unidadesOperacionaisPermitidas()), 403);

        $matricula = auth()->user()?->empregado?->matricula;

        if (!$matricula) {
            return back()->withErrors([
                'erro' => 'Sua conta de usuário não está vinculada a um empregado, então não é possível registrar o aceite.',
            ]);
        }

        $now = now();

        DB::transaction(function () use ($ordem_servico, $now, $matricula) {
            $solicitacao = $ordem_servico->solicitacaoServico;

            $solicitacao->update([
                'status' => self::STATUS_RECEBIDO_LABORATORIO,
                'data_solicitacao' => $now,
            ]);

            Historico::create([
                'escopo' => 'solicitacao_servico',
                'escopo_id' => $solicitacao->solicitacao_servico_id,
                'status' => self::STATUS_RECEBIDO_LABORATORIO,
                'data' => $now,
                'usuario_matricula' => $matricula,
            ]);

            foreach ($solicitacao->ordemServico as $ordem) {
                $ordem->update([
                    'status_atual' => self::STATUS_RECEBIDO_LABORATORIO,
                    'data_status_atual' => $now,
                    'data_aceite_laboratorio' => $now,
                ]);

                Historico::create([
                    'escopo' => 'ordem_servico',
                    'escopo_id' => $ordem->ordem_servico_id,
                    'status' => self::STATUS_RECEBIDO_LABORATORIO,
                    'data' => $now,
                    'usuario_matricula' => $matricula,
                ]);

                foreach ($ordem->fracoesAmostra as $fracao) {
                    $fracao->update(['status_atual' => self::STATUS_RECEBIDO_LABORATORIO]);

                    Historico::create([
                        'escopo' => 'fracao_amostra',
                        'escopo_id' => $fracao->fracao_amostra_id,
                        'status' => self::STATUS_RECEBIDO_LABORATORIO,
                        'data' => $now,
                        'usuario_matricula' => $matricula,
                    ]);
                }
            }
        });

        return redirect()
            ->route('laboratorio.aceitar-amostra')
            ->with('success', 'Amostra aceita com sucesso!');
    }

    // Rejeita a amostra: a solicitação de serviço, todas as suas ordens de
    // serviço e todas as frações dessas ordens são marcadas como rejeitadas
    // pelo laboratório, com o respectivo registro de histórico para cada uma.
    public function rejeitar(Request $request, OrdemServico $ordem_servico)
    {
        abort_unless(in_array($ordem_servico->unidade_operacional_id, $this->unidadesOperacionaisPermitidas()), 403);

        $validated = $request->validate([
            'motivo' => ['required', 'string', 'max:255'],
        ]);

        $matricula = auth()->user()?->empregado?->matricula;

        if (!$matricula) {
            return back()->withErrors([
                'erro' => 'Sua conta de usuário não está vinculada a um empregado, então não é possível registrar a rejeição.',
            ]);
        }

        $now = now();

        DB::transaction(function () use ($ordem_servico, $now, $matricula, $validated) {
            $solicitacao = $ordem_servico->solicitacaoServico;

            $solicitacao->update([
                'status' => self::STATUS_REJEITADO_LABORATORIO,
                'data_solicitacao' => $now,
            ]);

            Historico::create([
                'escopo' => 'solicitacao_servico',
                'escopo_id' => $solicitacao->solicitacao_servico_id,
                'status' => self::STATUS_REJEITADO_LABORATORIO,
                'data' => $now,
                'usuario_matricula' => $matricula,
            ]);

            foreach ($solicitacao->ordemServico as $ordem) {
                $ordem->update([
                    'status_atual' => self::STATUS_REJEITADO_LABORATORIO,
                    'data_status_atual' => $now,
                    'data_aceite_laboratorio' => $now,
                    'observacao' => $validated['motivo'],
                ]);

                Historico::create([
                    'escopo' => 'ordem_servico',
                    'escopo_id' => $ordem->ordem_servico_id,
                    'status' => self::STATUS_REJEITADO_LABORATORIO,
                    'data' => $now,
                    'usuario_matricula' => $matricula,
                ]);

                foreach ($ordem->fracoesAmostra as $fracao) {
                    $fracao->update(['status_atual' => self::STATUS_REJEITADO_LABORATORIO]);

                    Historico::create([
                        'escopo' => 'fracao_amostra',
                        'escopo_id' => $fracao->fracao_amostra_id,
                        'status' => self::STATUS_REJEITADO_LABORATORIO,
                        'data' => $now,
                        'usuario_matricula' => $matricula,
                    ]);
                }
            }
        });

        return redirect()
            ->route('laboratorio.aceitar-amostra')
            ->with('success', 'Amostra rejeitada com sucesso!');
    }
}
