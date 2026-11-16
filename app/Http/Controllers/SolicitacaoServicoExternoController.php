<?php

namespace App\Http\Controllers;

use App\Models\UnidadeOperacional;
use Inertia\Inertia;

// Requisição externa (CRA): mesmo cadastro de /solicitacoes_servicos/create,
// sem atividade e com o nome do solicitante externo. O salvamento usa
// SolicitacaoServicoController@store pela rota cra.solicitacoes_servicos_externos.store.
class SolicitacaoServicoExternoController extends Controller
{
    public function create()
    {
        return Inertia::render('Cra/SolicitacoesServicosExternos/Create', [
            'unidadesOperacionais' => UnidadeOperacional::orderBy('nome')
                ->get(['unidade_operacional_id', 'nome']),
            'cadastradoPor' => auth()->user()?->name,
            'dataSolicitacao' => now()->format('d/m/Y'),
        ]);
    }
}
