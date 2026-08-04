<?php

namespace App\Http\Controllers;
use App\Models\PermissaoUnidadeOperacional;
use Illuminate\Http\Request;

class PermissaoUnidadeOperacionalController extends Controller
{
    // Listar os dados da tabela permissao_unidade_operacional
    public function index()
    {
        //dd('Aqui');
        // Recuperar os registros do Banco de Dados
        $permissoes_unidades_operacionais= PermissaoUnidadeOperacional::OrderBy('permissao_unidade_operacional_id', 'DESC')->paginate(3);
        // Carregar a view
        return view('permissoes_unidades_operacionais.index', ['permissoes_unidades_operacionais' => $permissoes_unidades_operacionais]);
    }

    // Visualizar a solicitacao_servico
    public function show(PermissaoUnidadeOperacional $permissao_unidade_operacional)
    {
        //dd($solicitacao_servico);
        // Carregar a view
        return view('permissoes_unidades_operacional.show', ['permissao_unidade_operacional' => $permissao_unidade_operacional]);
    }
}
