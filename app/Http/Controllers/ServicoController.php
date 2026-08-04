<?php

namespace App\Http\Controllers;
use App\Models\Servico;
use Illuminate\Http\Request;

class ServicoController extends Controller
{
    // Listar os serviços
    public function index()
    {
        // Recuperar os registros do Banco de Dados
        $servicos= Servico::OrderBy('servico_id', 'DESC')->paginate(10);
        // Carregar a view
        return view('servicos.index', ['servicos' => $servicos]);
    }

    public function porUnidadeOperacional($unidade_operacional_id)
    {
        $servicos = Servico::where('unidade_operacional_id', $unidade_operacional_id)
            ->where('is_ativo', 1)
            ->orderBy('descricao')
            ->get(['servico_id', 'descricao', 'tipo_servico']);

        return response()->json($servicos);
    }
}
