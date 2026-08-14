<?php

namespace App\Http\Controllers;

use App\Models\OrdemServico;
use App\Models\SolicitacaoServico;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CraReceberAmostraController extends Controller
{
    // Lista as solicitações de serviço com pelo menos uma ordem aguardando recebimento pelo CRA
    public function index()
    {
        $solicitacoes = SolicitacaoServico::whereHas('ordemServico', function ($query) {
                $query->where('status_atual', 'ENVIADO_CRA');
            })
            ->with(['atividade', 'empregado'])
            ->withCount(['ordemServico as ordens_pendentes_count' => function ($query) {
                $query->where('status_atual', 'ENVIADO_CRA');
            }])
            ->orderBy('data_solicitacao')
            ->get();

        return Inertia::render('Cra/ReceberAmostra/Index', compact('solicitacoes'));
    }

    // Lista as ordens de serviço aguardando CRA de uma solicitação específica
    public function ordens(SolicitacaoServico $solicitacao_servico)
    {
        $ordens = $solicitacao_servico->ordemServico()
            ->where('status_atual', 'ENVIADO_CRA')
            ->with(['unidadeOperacional', 'fracoesAmostra.amostra', 'fracoesAmostra.servico'])
            ->get()
            ->each(function ($ordem) {
                $ordem->amostras_descricao = $ordem->fracoesAmostra
                    ->pluck('amostra.descricao')
                    ->filter()
                    ->unique()
                    ->implode(', ');

                $ordem->analises_descricao = $ordem->fracoesAmostra
                    ->pluck('servico.descricao')
                    ->filter()
                    ->unique()
                    ->implode(', ');
            });

        abort_if($ordens->isEmpty(), 404);

        return Inertia::render('Cra/ReceberAmostra/Ordens', [
            'solicitacao' => $solicitacao_servico,
            'ordens' => $ordens,
        ]);
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
}
