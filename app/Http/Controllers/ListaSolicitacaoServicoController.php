<?php

namespace App\Http\Controllers;

use App\Models\SolicitacaoServico;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ListaSolicitacaoServicoController extends Controller
{
    // Mesma consulta da tabela "Solicitações de Serviço" de /solicitacoes_servicos
    // (SolicitacaoServicoController@index): filtro por data, ordenação e paginação
    public function index(Request $request)
    {
        $request->validate([
            'data_inicial' => ['nullable', 'date'],
            'data_final' => ['nullable', 'date'],
        ]);

        $sortable = ['solicitacao_servico_id', 'data_solicitacao', 'descricao'];
        $sort = in_array($request->query('sort'), $sortable, true) ? $request->query('sort') : 'data_solicitacao';
        $direction = $request->query('direction') === 'asc' ? 'asc' : 'desc';

        $dataInicial = $request->query('data_inicial');
        $dataFinal = $request->query('data_final');

        $solicitacoes = SolicitacaoServico::with(['atividade', 'empregado'])
            ->when($dataInicial, fn ($query) => $query->whereDate('data_solicitacao', '>=', $dataInicial))
            ->when($dataFinal, fn ($query) => $query->whereDate('data_solicitacao', '<=', $dataFinal))
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString()
            ->through(fn (SolicitacaoServico $solicitacao) => [
                'solicitacao_servico_id' => $solicitacao->solicitacao_servico_id,
                'numero_solicitacao_servico' => $solicitacao->numero_solicitacao_servico,
                'data_solicitacao' => $solicitacao->data_solicitacao
                    ? \Carbon\Carbon::parse($solicitacao->data_solicitacao)->format('d/m/Y')
                    : null,
                'descricao' => $solicitacao->descricao,
                'atividade' => $solicitacao->atividade->titulo ?? null,
                'status' => $solicitacao->status,
                'solicitante' => $solicitacao->empregado->nome ?? null,
                'solicitante_matricula' => $solicitacao->solicitante_matricula,
                'solicitante_externo_nome' => $solicitacao->solicitante_externo_nome,            ]);

        return Inertia::render('ListaSolicitacaoServico/Index', [
            'solicitacoes' => $solicitacoes,
            'filtros' => [
                'sort' => $sort,
                'direction' => $direction,
                'data_inicial' => $dataInicial,
                'data_final' => $dataFinal,
            ],
        ]);
    }
}
