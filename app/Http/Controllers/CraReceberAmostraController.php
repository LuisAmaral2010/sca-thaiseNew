<?php

namespace App\Http\Controllers;

use App\Models\OrdemServico;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CraReceberAmostraController extends Controller
{
    // Lista as ordens de serviço aguardando recebimento pelo CRA
    public function index()
    {
        $ordens = OrdemServico::with(['solicitacaoServico', 'unidadeOperacional'])
            ->where('status_atual', 'ENVIADO_CRA')
            ->orderBy('data_status_atual')
            ->get();

        return Inertia::render('Cra/ReceberAmostra/Index', compact('ordens'));
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
