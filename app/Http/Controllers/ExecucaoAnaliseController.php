<?php

namespace App\Http\Controllers;

use App\Models\ExecucaoAnalise;
use App\Models\OrdemServico;
use App\Models\FracaoAmostra;
use App\Models\Servico;
use Illuminate\Http\Request;
use Exception;


class ExecucaoAnaliseController extends Controller
{
    // Listar os dados da tabela execucao_analise
    public function index()
    {
        //dd('Aqui');
        // Recuperar os registros do Banco de Dados
        $execucoes_analises= ExecucaoAnalise::OrderBy('execucao_analise_id', 'DESC')->paginate(3);
        // Carregar a view
        return view('execucoes_analises.index', ['execucoes_analises' => $execucoes_analises]);
    }

    // Carregar o formulário cadastrar nova execução analise
    public function create()
    {
        // Carregar a view
        $servicos = Servico::select('servico_id', 'descricao')->orderBy('servico_id')->get();                 
        $ordens_servicos = OrdemServico::select('ordem_servico_id', 'ordem_servico_id')->orderBy('ordem_servico_id')->get();                
        $fracoes_amostras = FracaoAmostra::select('fracao_amostra_id', 'fracao_amostra_id')->orderBy('fracao_amostra_id')->get();            
        return view('execucoes_analises.create') 
            ->with('fracoes_amostras', $fracoes_amostras)
            ->with('ordens_servicos',$ordens_servicos)
            ->with('servicos',$servicos);
        //return view('execucoes_analises.create');
    }    

        // Busca categorias do banco (id e nome)
        //$fracoes_amostras = FracaoAmostra::select('fracao_amostra_id', 'descricao')->orderBy('descricao')->get();
       // return view('produtos.create', compact('categorias'));

    // Cadastrar no banco de dados o nova execução analise
    public function store(Request $request)
    {
        // Capturar possíveis exceções durante a execução.
        try{

                // Validação
            $validated = $request->validate([
                'fracao_amostra_id' => 'required|integer|exists:categorias,id',
            ]);

            // Cadastrar no banco de dados na tabela amostras
            $execucao_analise = ExecucaoAnalise::create([
                'fracao_amostra_id' => $validated['fracao_amostra_id'],
                //'fracao_amostra_id' => $request->validate(['required|integer|exists:fracoes_amostras,fracao_amostra_id']),
                'laudo_id' => 1, //$request->laudo_id,
                //'ordem_servico_id' => $request->ordem_servico_id,
                'ordem_servico_id' => 1, ///'required|integer|exists:ordens_servicos,ordem_servico_id',

                'servico_id' => 1, //$request->servico_id,
                'is_concluido' => 1, //$request->is_concluido,
                'data_conclusao' => now(), //$request->data_conclusao,
                'is_cancelado' => 1, //$request->is_cancelado,
                'data_cancelamento' => now(), //$request->data_cancelamento,
                'observacao' => '11', //$request->observacao,    
                'created_at' => now(),  
                'updated_at' => now(),     

            ]);

            // Redirecionar o usuário, enviar a mensagem de sucesso
            //return redirect()->route('execucoes_analise.show', ['execucao_analise' => $execucao_analise->execucao_analise_id])->with('success', 'Execução Analise cadastrada com sucesso!');
            return redirect()->route('execucoes_analise.index');
            }catch (Exception $e){
            // Redirecionar o usuário, enviar a mendsagem de sucesso
            return back()->withInput()->with('error', 'Execução Análise não cadastrada!');
        }
    }    
}

/*
{{--
        'execucao_analise_id',
        'fracao_amostra_id',
        'laudo_id',
        'ordem_servico_id',
        'servico_id',
        'is_concluido',
        'data_conclusao',
        'is_cancelado',
        'data_cancelamento',
        'observacao',

        'created_at',
        'updated_at'
        --}}
        */