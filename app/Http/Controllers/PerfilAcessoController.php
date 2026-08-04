<?php

namespace App\Http\Controllers;
use App\Models\PerfilAcesso;
use Illuminate\Http\Request;

class PerfilAcessoController extends Controller
{
    // Listar os dados da tabela perfil_acesso
    public function index()
    {
        //dd('Aqui');
        // Recuperar os registros do Banco de Dados
        $perfis_acessos= PerfilAcesso::OrderBy('perfil_acesso_id', 'DESC')->paginate(3);
        // Carregar a view
        return view('perfis_acessos.index', ['perfis_acessos' => $perfis_acessos]);
    }

    // Visualizar a solicitacao_servico
    public function show(PerfilAcesso $perfil_acesso)
    {
        //dd($solicitacao_servico);
        // Carregar a view
        return view('perfis_acessos.show', ['perfil_acesso' => $perfil_acesso]);
    }
}
