<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAmostraRequest;
use App\Http\Requests\UpdateAmostraRequest;
use App\Models\Amostra;
use App\Models\SolicitacaoServico;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AmostraController extends Controller
{
    // Listar as amostras
    public function index()
    {
        $this->authorize('viewAny', Amostra::class);

        $amostras = Amostra::orderBy('amostra_id', 'DESC')->paginate(10)->withQueryString();

        $amostras->getCollection()->transform(fn (Amostra $amostra) => [
            ...$amostra->toArray(),
            'can_update' => Auth::user()->can('update', $amostra),
            'can_delete' => Auth::user()->can('delete', $amostra),
        ]);

        return Inertia::render('Amostras/Index', ['amostras' => $amostras]);
    }

    // Visualizar a amostra
    public function show(Amostra $amostra)
    {
        $this->authorize('view', $amostra);

        return Inertia::render('Amostras/Show', [
            'amostra' => $this->amostraWithFormattedDates($amostra),
        ]);
    }

    // Carregar o formulário cadastrar nova amostra
    public function create()
    {
        $this->authorize('create', Amostra::class);

        return Inertia::render('Amostras/Create', [
            'solicitacoes' => $this->solicitacoesForSelect(),
        ]);
    }

    // Cadastrar no banco de dados a nova amostra
    public function store(StoreAmostraRequest $request)
    {
        $validated = $request->validated();

        $solicitacao = SolicitacaoServico::findOrFail($validated['solicitacao_id']);

        $this->authorize('create', [Amostra::class, $solicitacao->atividade_id]);

        try {
            $amostra = Amostra::create($validated);

            return redirect()
                ->route('amostras.show', ['amostra' => $amostra->amostra_id])
                ->with('success', 'Amostra cadastrada com sucesso!');
        } catch (Exception $e) {
            Log::error('Amostra não cadastrada.', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Amostra não cadastrada!');
        }
    }

    // Carregar o formulário editar amostra
    public function edit(Amostra $amostra)
    {
        $this->authorize('update', $amostra);

        return Inertia::render('Amostras/Edit', [
            'amostra' => $amostra,
            'solicitacoes' => $this->solicitacoesForSelect(),
        ]);
    }

    public function update(UpdateAmostraRequest $request, Amostra $amostra)
    {
        try {
            $amostra->update($request->validated());

            return redirect()
                ->route('amostras.show', ['amostra' => $amostra->amostra_id])
                ->with('success', 'Amostra editada com sucesso!');
        } catch (Exception $e) {
            Log::error('Amostra não editada.', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Amostra não editada!');
        }
    }

    // Excluir a amostra do Banco de Dados
    public function destroy(Amostra $amostra)
    {
        $this->authorize('delete', $amostra);

        try {
            $amostra->delete();

            return redirect()
                ->route('amostras.index')
                ->with('success', 'Amostra excluída com sucesso!');
        } catch (Exception $e) {
            Log::error('Amostra não excluída.', ['error' => $e->getMessage()]);

            return back()->with('error', 'Amostra não excluída!');
        }
    }

    private function solicitacoesForSelect()
    {
        return SolicitacaoServico::orderBy('solicitacao_servico_id', 'DESC')
            ->get(['solicitacao_servico_id', 'descricao']);
    }

    private function amostraWithFormattedDates(Amostra $amostra): array
    {
        return [
            ...$amostra->toArray(),
            'created_at_formatted' => optional($amostra->created_at)->format('d/m/Y H:i:s'),
            'updated_at_formatted' => optional($amostra->updated_at)->format('d/m/Y H:i:s'),
            'can_update' => Auth::user()->can('update', $amostra),
        ];
    }
}
