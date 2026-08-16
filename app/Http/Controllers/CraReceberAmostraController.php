<?php

namespace App\Http\Controllers;

use App\Models\OrdemServico;
use App\Models\SolicitacaoServico;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CraReceberAmostraController extends Controller
{
    // Lista as solicitações de serviço com ordens aguardando ou rejeitadas pelo CRA
    public function index()
    {
        $solicitacoes = SolicitacaoServico::whereHas('ordemServico', function ($query) {
                $query->whereIn('status_atual', ['ENVIADO_CRA', 'REJEITADO_CRA']);
            })
            ->with(['atividade', 'empregado'])
            ->withCount(['ordemServico as ordens_pendentes_count' => function ($query) {
                $query->where('status_atual', 'ENVIADO_CRA');
            }])
            ->withCount(['ordemServico as ordens_rejeitadas_count' => function ($query) {
                $query->where('status_atual', 'REJEITADO_CRA');
            }])
            ->orderBy('data_solicitacao')
            ->get();

        return Inertia::render('Cra/ReceberAmostra/Index', compact('solicitacoes'));
    }

    // Lista, na forma de Amostra x Unidade Operacional / Serviço, as ordens de
    // serviço aguardando CRA de uma solicitação específica — mesma relação
    // exibida no painel de resumo do formulário de criação da solicitação.
    public function ordens(SolicitacaoServico $solicitacao_servico)
    {
        $ordens = $solicitacao_servico->ordemServico()
            ->where('status_atual', 'ENVIADO_CRA')
            ->with(['unidadeOperacional', 'fracoesAmostra.amostra', 'fracoesAmostra.servico'])
            ->get();

        abort_if($ordens->isEmpty(), 404);

        $linhas = $ordens->flatMap(function ($ordem) {
            if ($ordem->fracoesAmostra->isEmpty()) {
                return [[
                    'ordem_servico_id' => $ordem->ordem_servico_id,
                    'unidade_operacional' => $ordem->unidadeOperacional->nome ?? '—',
                    'amostra_descricao' => null,
                    'amostra_validade_dias' => null,
                    'amostra_condicao_armazenamento' => null,
                    'servico_descricao' => null,
                    'servico_tipo_servico' => null,
                ]];
            }

            return $ordem->fracoesAmostra->map(fn ($fracao) => [
                'ordem_servico_id' => $ordem->ordem_servico_id,
                'unidade_operacional' => $ordem->unidadeOperacional->nome ?? '—',
                'amostra_descricao' => $fracao->amostra->descricao ?? null,
                'amostra_validade_dias' => $fracao->amostra->validade_dias ?? null,
                'amostra_condicao_armazenamento' => $fracao->amostra->condicao_armazenamento ?? null,
                'servico_descricao' => $fracao->servico->descricao ?? null,
                'servico_tipo_servico' => $fracao->servico->tipo_servico ?? null,
            ]);
        })->values();

        return Inertia::render('Cra/ReceberAmostra/Ordens', [
            'solicitacao' => $solicitacao_servico,
            'linhas' => $linhas,
        ]);
    }

    // Ordens de uma solicitação ainda aguardando recebimento pelo CRA
    private function ordensPendentes(SolicitacaoServico $solicitacao_servico)
    {
        $ordens = $solicitacao_servico->ordemServico()
            ->where('status_atual', 'ENVIADO_CRA')
            ->with('unidadeOperacional')
            ->get();

        abort_if($ordens->isEmpty(), 404);

        return $ordens;
    }

    // Formulário de confirmação de recebimento de todas as ordens pendentes de uma solicitação
    public function receberTodasForm(SolicitacaoServico $solicitacao_servico)
    {
        $ordens = $this->ordensPendentes($solicitacao_servico);

        return Inertia::render('Cra/ReceberAmostra/ReceberTodas', [
            'solicitacao' => $solicitacao_servico,
            'ordens' => $ordens->map(fn ($ordem) => [
                'ordem_servico_id' => $ordem->ordem_servico_id,
                'unidade_operacional' => $ordem->unidadeOperacional->nome ?? '—',
            ])->values(),
        ]);
    }

    // Confirma o recebimento de todas as ordens pendentes de uma solicitação
    public function receberTodas(Request $request, SolicitacaoServico $solicitacao_servico)
    {
        $validated = $request->validate([
            'data_recebimento' => ['required', 'date'],
            'observacao' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($this->ordensPendentes($solicitacao_servico) as $ordem) {
            $ordem->update([
                'status_atual' => 'RECEBIDO_CRA',
                'data_status_atual' => $validated['data_recebimento'],
                'observacao' => $validated['observacao'] ?? $ordem->observacao,
                'recebedor_matricula' => auth()->user()?->empregado?->matricula,
            ]);
        }

        return redirect()
            ->route('cra.receber-amostra.index')
            ->with('success', 'Todas as amostras foram recebidas com sucesso!');
    }

    // Formulário de confirmação de rejeição de todas as ordens pendentes de uma solicitação
    public function rejeitarTodasForm(SolicitacaoServico $solicitacao_servico)
    {
        $ordens = $this->ordensPendentes($solicitacao_servico);

        return Inertia::render('Cra/ReceberAmostra/RejeitarTodas', [
            'solicitacao' => $solicitacao_servico,
            'ordens' => $ordens->map(fn ($ordem) => [
                'ordem_servico_id' => $ordem->ordem_servico_id,
                'unidade_operacional' => $ordem->unidadeOperacional->nome ?? '—',
            ])->values(),
        ]);
    }

    // Confirma a rejeição de todas as ordens pendentes de uma solicitação
    public function rejeitarTodas(Request $request, SolicitacaoServico $solicitacao_servico)
    {
        $validated = $request->validate([
            'data_rejeicao' => ['required', 'date'],
            'motivo' => ['required', 'string', 'max:255'],
        ]);

        foreach ($this->ordensPendentes($solicitacao_servico) as $ordem) {
            $ordem->update([
                'status_atual' => 'REJEITADO_CRA',
                'data_status_atual' => $validated['data_rejeicao'],
                'observacao' => $validated['motivo'],
                'recebedor_matricula' => auth()->user()?->empregado?->matricula,
            ]);
        }

        return redirect()
            ->route('cra.receber-amostra.index')
            ->with('success', 'Todas as amostras foram rejeitadas com sucesso!');
    }

    // Formulário de confirmação de recebimento de uma ordem específica
    public function show(OrdemServico $ordem_servico)
    {
        abort_unless($ordem_servico->status_atual === 'ENVIADO_CRA', 404);

        $ordem_servico->load(['solicitacaoServico', 'unidadeOperacional']);

        return Inertia::render('Cra/ReceberAmostra/Show', ['ordem' => $ordem_servico]);
    }

    // Confirma o recebimento da amostra pelo CRA
    public function store(Request $request, OrdemServico $ordem_servico)
    {
        abort_unless($ordem_servico->status_atual === 'ENVIADO_CRA', 404);

        $validated = $request->validate([
            'data_recebimento' => ['required', 'date'],
            'observacao' => ['nullable', 'string', 'max:255'],
        ]);

        $ordem_servico->update([
            'status_atual' => 'RECEBIDO_CRA',
            'data_status_atual' => $validated['data_recebimento'],
            'observacao' => $validated['observacao'] ?? $ordem_servico->observacao,
            'recebedor_matricula' => auth()->user()?->empregado?->matricula,
        ]);

        return redirect()
            ->route('cra.receber-amostra.index')
            ->with('success', 'Amostra recebida com sucesso!');
    }

    // Formulário de confirmação de rejeição de uma ordem específica
    public function rejeitarForm(OrdemServico $ordem_servico)
    {
        abort_unless($ordem_servico->status_atual === 'ENVIADO_CRA', 404);

        $ordem_servico->load(['solicitacaoServico', 'unidadeOperacional']);

        return Inertia::render('Cra/ReceberAmostra/Rejeitar', ['ordem' => $ordem_servico]);
    }

    // Confirma a rejeição da amostra pelo CRA
    public function rejeitar(Request $request, OrdemServico $ordem_servico)
    {
        abort_unless($ordem_servico->status_atual === 'ENVIADO_CRA', 404);

        $validated = $request->validate([
            'data_rejeicao' => ['required', 'date'],
            'motivo' => ['required', 'string', 'max:255'],
        ]);

        $ordem_servico->update([
            'status_atual' => 'REJEITADO_CRA',
            'data_status_atual' => $validated['data_rejeicao'],
            'observacao' => $validated['motivo'],
            'recebedor_matricula' => auth()->user()?->empregado?->matricula,
        ]);

        return redirect()
            ->route('cra.receber-amostra.index')
            ->with('success', 'Amostra rejeitada com sucesso!');
    }
}
