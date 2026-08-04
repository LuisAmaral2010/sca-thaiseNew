<?php

namespace App\Http\Controllers;
use App\Models\SolicitacaoServico;

use Illuminate\Http\Request;
use Inertia\Inertia;

class SolicitanteController extends Controller
{
    // Listar as solicitacao_servicos
    public function index()
    {
        $solicitacoes_servicos = SolicitacaoServico::OrderBy('solicitacao_servico_id', 'DESC')->paginate(3);

        return Inertia::render('Solicitante', ['solicitacoes_servicos' => $solicitacoes_servicos]);
    }

    // Visualizar a solicitacao_servico
    public function show(SolicitacaoServico $solicitacao_servico)
    {
        //dd($solicitacao_servico);
        // Carregar a view
        return view('solicitacao_servicos.show', ['solicitacao_servico' => $solicitacao_servico]);
    }

        // Carregar o formulário cadastrar novo curso
    public function create()
    {
        // Carregar a view
        return view('solicitacao_servicos.create');
    }

    // Cadastrar no banco de dados o novo curso
    public function store(Request $request)
    {
        // dd($request);
        // Cadastrar no banco de dados na tabela cursos
        SolicitacaoServico::create([
            'solicitacao_servico_id' =>	$request->solicitacao_servico_id,				
            'descricao' =>	$request->descricao,				
            'data_solicitacao' =>	$request->data_solicitacao,
            'atividade_id' =>	$request->atividade_id,				
            'solicitante_matricula' =>	$request->solicitante_matricula,		
            'created_at' =>	$request->created_at,
            'update_at' =>	$request->update_at,
        ]);


        // Redirecionar o usuário, enviar a mensagem de sucesso
        return redirect()->route('solicitacao_servicos.index')->with('success', 'SolicitacaoServico cadastrada com sucesso!');
    }
}
