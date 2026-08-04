<?php

namespace App\Http\Controllers;
use App\Models\PermissaoAtividade;
use Illuminate\Http\Request;

class PermissaoAtividadeController extends Controller
{
    // Listar os dados da tabela perfil_acesso
    public function index()
    {
        //dd('Aqui');
        // Recuperar os registros do Banco de Dados
        $permissoes_atividades= PermissaoAtividade::OrderBy('permissao_atividade_id', 'DESC')->paginate(3);
        // Carregar a view
        return view('permissoes_atividades.index', ['permissoes_atividades' => $permissoes_atividades]);
    }

    // Visualizar a solicitacao_servico
    public function show(PermissaoAtividade $permissao_atividade)
    {
        //dd($solicitacao_servico);
        // Carregar a view
        return view('permissoes_atividades.show', ['permissao_atividade' => $permissao_atividade]);
    }
}
