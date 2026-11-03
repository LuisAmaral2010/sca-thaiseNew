<?php

namespace App\Http\Controllers;

use App\Models\ArquivoLaboratorio;
use App\Models\FracaoAmostra;
use App\Models\Laudo;
use App\Models\OrdemServico;
use App\Models\PermissaoUnidadeOperacional;
use App\Models\SolicitacaoServico;
use App\Models\UnidadeOperacional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ResptecAprovarLaudoController extends Controller
{
    // Status gravado em "Gravar Laudo PDF" (LaboratorioGravarLaudoController)
    private const STATUS_AGUARDANDO_RESPTEC = 'LAUDO ESPERANDO APROVAÇÃO DO RESPTEC';

    // Status gravado ao clicar em "Aprovar"
    private const STATUS_APROVADO_RESPTEC = 'LAUDO APROVADO PELO RESPTEC';

    // Status gravado ao clicar em "Rejeitar"
    private const STATUS_REJEITADO_RESPTEC = 'LAUDO REJEITADO PELO RESPTEC';

    // Unidades operacionais em que o usuário logado tem permissão
    // (permissao_unidade_operacional.usuario_matricula)
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
    // das ordens de serviço com laudo aguardando aprovação do RespTec. Como
    // em "Aceitar amostra", a página abre com a tabela vazia e só lista as
    // ordens depois que uma unidade operacional é selecionada.
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
                ->where('status_atual', self::STATUS_AGUARDANDO_RESPTEC)
                ->where('unidade_operacional_id', $unidadeOperacionalId)
                ->when($validated['data_inicial'] ?? null, fn ($query, $data) => $query->whereDate('data_status_atual', '>=', $data))
                ->when($validated['data_final'] ?? null, fn ($query, $data) => $query->whereDate('data_status_atual', '<=', $data))
                ->orderByDesc('ordem_servico_id')
                ->get()
            : collect();

        // Laudo de cada ordem e o nome do PDF gravado para ele
        $laudos = Laudo::whereIn('ordem_servico_id', $ordens->pluck('ordem_servico_id'))
            ->get(['laudo_id', 'ordem_servico_id', 'numero_laudo'])
            ->keyBy('ordem_servico_id');

        $arquivos = ArquivoLaboratorio::whereIn('laudo_id', $laudos->pluck('laudo_id'))
            ->pluck('nome', 'laudo_id');

        $ordens = $ordens->map(function (OrdemServico $ordem) use ($laudos, $arquivos) {
            $laudo = $laudos->get($ordem->ordem_servico_id);

            return [
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
                'laudo_id' => $laudo?->laudo_id,
                'numero_laudo' => $laudo?->numero_laudo,
                'arquivo_nome' => $laudo ? ($arquivos[$laudo->laudo_id] ?? null) : null,
                'data_envio' => $ordem->data_status_atual,
            ];
        })->values();

        return Inertia::render('Resptec/AprovarLaudo', [
            'unidadesOperacionais' => $unidadesOperacionais,
            'filtros' => $request->only(['data_inicial', 'data_final', 'unidade_operacional_id']),
            'ordens' => $ordens,
        ]);
    }

    // Aprova o laudo: grava "LAUDO APROVADO PELO RESPTEC" no laudo, na ordem
    // de serviço (com registro em ordem_servico_historico), nas suas frações
    // e na solicitação. Só aprova laudos que ainda esperam aprovação.
    public function aprovar(Laudo $laudo)
    {
        $ordem = $this->ordemDoLaudoPermitida($laudo);

        if ($ordem->status_atual !== self::STATUS_AGUARDANDO_RESPTEC) {
            return back()->with('error', "O laudo {$laudo->numero_laudo} não está esperando aprovação (status atual: {$ordem->status_atual}).");
        }

        $this->gravarStatus($laudo, $ordem, self::STATUS_APROVADO_RESPTEC);

        return back()->with('success', "Laudo {$laudo->numero_laudo} aprovado com sucesso.");
    }

    // Rejeita o laudo: grava "LAUDO REJEITADO PELO RESPTEC" nas mesmas
    // tabelas da aprovação e o motivo em ordem_servico.observacao. Só
    // rejeita laudos que ainda esperam aprovação.
    public function rejeitar(Request $request, Laudo $laudo)
    {
        $ordem = $this->ordemDoLaudoPermitida($laudo);

        $validated = $request->validate([
            'motivo' => ['required', 'string', 'max:255'],
        ], [
            'motivo.required' => 'Informe o motivo da rejeição.',
            'motivo.max' => 'O motivo deve ter no máximo 255 caracteres.',
        ]);

        if ($ordem->status_atual !== self::STATUS_AGUARDANDO_RESPTEC) {
            return back()->with('error', "O laudo {$laudo->numero_laudo} não está esperando aprovação (status atual: {$ordem->status_atual}).");
        }

        $this->gravarStatus($laudo, $ordem, self::STATUS_REJEITADO_RESPTEC, ['observacao' => $validated['motivo']]);

        return back()->with('success', "Laudo {$laudo->numero_laudo} rejeitado.");
    }

    // Ordem de serviço do laudo, desde que o usuário tenha permissão na unidade
    private function ordemDoLaudoPermitida(Laudo $laudo): OrdemServico
    {
        $ordem = OrdemServico::findOrFail($laudo->ordem_servico_id);

        abort_unless(in_array($ordem->unidade_operacional_id, $this->unidadesOperacionaisPermitidas()), 403);

        return $ordem;
    }

    // Grava o status no laudo, na ordem de serviço (com registro em
    // ordem_servico_historico), nas suas frações e na solicitação, numa
    // única transação. $dadosOrdem permite gravar campos extras na ordem.
    private function gravarStatus(Laudo $laudo, OrdemServico $ordem, string $status, array $dadosOrdem = []): void
    {
        DB::transaction(function () use ($laudo, $ordem, $status, $dadosOrdem) {
            $now = now();

            $laudo->update(['status_atual' => $status]);

            $ordem->update([
                'status_atual' => $status,
                'data_status_atual' => $now,
            ] + $dadosOrdem);

            $ordem->registrarHistorico();

            FracaoAmostra::where('ordem_servico_id', $ordem->ordem_servico_id)->update([
                'status_atual' => $status,
                'data_status_atual' => $now,
            ]);

            SolicitacaoServico::whereKey($ordem->solicitacao_servico_id)
                ->update(['status' => $status]);
        });
    }

    // Baixa o PDF do laudo gravado em arquivo_laboratorio, com o nome
    // original do arquivo. Só para unidades em que o usuário tem permissão.
    public function baixarArquivo(Laudo $laudo)
    {
        $unidadeOperacionalId = OrdemServico::whereKey($laudo->ordem_servico_id)->value('unidade_operacional_id');

        abort_unless(in_array($unidadeOperacionalId, $this->unidadesOperacionaisPermitidas()), 403);

        $arquivo = ArquivoLaboratorio::where('laudo_id', $laudo->laudo_id)->firstOrFail();

        $nome = $arquivo->nome ?: "{$laudo->numero_laudo}.pdf";

        return response()->streamDownload(
            fn () => print($arquivo->document_content),
            $nome,
            ['Content-Type' => $arquivo->content_type ?: 'application/pdf']
        );
    }
}
