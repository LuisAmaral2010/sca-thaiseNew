<?php

namespace App\Http\Controllers;
use App\Models\SolicitacaoServico;
use App\Models\Atividade;
use App\Models\Amostra;
use App\Models\UnidadeOperacional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SolicitacaoServicoController extends Controller
{
    // Listar as solicitacao_servicos
    public function index()
    {
        $solicitacoes_servicos = SolicitacaoServico::with('atividade')->paginate(10);
        return view('solicitacoes_servicos.index', compact('solicitacoes_servicos'));
    }

    // Visualizar a solicitacao_servico
    public function show(SolicitacaoServico $solicitacao_servico)
    {
        // Carregar a view
        return view('solicitacoes_servicos.show', ['solicitacoes_servico' => $solicitacao_servico]);
    }

    // Carregar o formulário cadastrar novo curso
    public function create()
    {
        // Captura o valor da matricula do usuário logado 
        $matricula = auth()->user()->empregado->matricula;

        // Filtra dados exatos ou usa LIKE para correspondência parcial
        $atividades = Atividade::query()
            ->when($matricula, function ($query, $matricula) {
            // WHERE nome_produto LIKE '%valor%'
            return $query->where('matricula', 'like', '%' . $matricula . '%');
            })
            ->get();

        // Separar os campos para o select
        $atividades = $atividades->pluck('titulo', 'atividade_id');

        $unidadesOperacionais = UnidadeOperacional::orderBy('nome')->get();
        
        // Carregar a view
        return view('solicitacoes_servicos.create', compact('atividades', 'unidadesOperacionais'));
    }

    
    // Cadastrar no banco de dados o novo curso
    public function store(Request $request)
    {
 //dd($request);
        $data = $request->validate([
            'descricao'              => 'required|string|max:255',
            'data_solicitacao'       => now(),
            //'atividade_id'           => 'required|integer|exists:atividades,atividade_id',
            //'solicitante_matricula'  => 'required|string|max:50',

            'amostras'                              => 'array',
            'amostras.*.descricao'                  => 'nullable|string|max:255',
            //'amostras.*.validade_dias'              => 'nullable|integer',
            'amostras.*.validade_dias'              => 'nullable|max:100',
            'amostras.*.condicao_armazenamento'     => 'nullable|string|max:255',
            'amostras.*.numero_cra'                 => 'nullable|string|max:100',
        ]);
        //dd($data);
        DB::beginTransaction();
        try {
            //dd("Passei Aqui !!!");
            // criar solicitação (ajuste o Model e campos conforme o seu)
            // 1) Cria a solicitação
            $solicitacao = \App\Models\SolicitacaoServico::create([
                'descricao'             => $data['descricao'],
                'data_solicitacao'      => now(),
                //'atividade_id' => $data['atividade_id'],
                'atividade_id' => $request->atividade_id,
                //'descricao' => $data['descricao'],
                'solicitante_matricula' => auth()->user()->empregado->matricula,
            ]);
            //dd($solicitacao->solicitante_matricula);
            // amostras
        /* 
            $n=0;
            if (!empty($data['amostras'])) {
                //dd($data['amostras']);
                foreach ($data['amostras'] as $data[$amostra]) {
                    // ignorar totalmente vazias
                    if (empty($amostra['descricao']) && empty($amostra['validade_dias']) && empty($amostra['condicao_armazenamento'])) {
                        continue;
                    }
                    //dd($amostra['descricao']);
                    $solicitacao->amostras()->create([
                        'descricao' => 'descricao',
                        //'descricao' => $amostra['descricao'] ?? null,
                        //'validade_dias' => $amostra['validade_dias'] ?? null,
                        //'condicao_armazenamento' => $amostra['condicao_armazenamento'] ?? null,
                    ]);
                    $n = $n + 1;
                }
                //dd($n);
            }
       */
        $amostra = array(  
                            "descricao" => "Teste", 
                            "validade_dias" => "2", 
                            "condicao_armazenamento" => "0° celsius",
                            "numero_cra" => ""
                            );

//"frutas"  => array("a" => "laranja", "b" => "banana", "c" => "maçã"),
        // 2) Cria as amostras (se vieram)
        // dd($data['amostras']);
        if (!empty($data['amostras'])) {
            foreach ($data['amostras'] as $amostra) {
                /*
        $amostra = array(  
                            "descricao" => "", 
                            "validade_dias" => "", 
                            "condicao_armazenamento" => "",
                            "numero_cra" => ""
                            );
*/
//                dd($data['amostras']);
                //dd($amostra['descricao']);
                //dd($amostra['validade_dias']);
                // ignorar linhas totalmente vazias
                if (empty($amostra['descricao']) &&
                    empty($amostra['validade_dias']) &&
                    empty($amostra['condicao_armazenamento']) &&
                    empty($amostra['numero_cra'])) {
                        dd('Passei aqui !!');
                    continue;
                }
 dd($data[$amostra]["validade_dias"]);

                Amostra::create([
                    'descricao'              => $data[$amostra]["descricao"] ?? null,
                    //'solicitacao_id'         => $solicitacao->solicitacao_servico_id,
                    'validade_dias'          => $data[$amostra]["validade_dias"] ?? null,
                    'condicao_armazenamento' => $data[$amostra]["condicao_armazenamento"] ?? null,
                    'numero_cra'             => $data[$amostra]["numero_cra"] ?? null,
                ]);
            }
        }            
            // serviços: vincular via pivot (ajuste o relacionamento)
            if (!empty($data['servicos'])) {
                // se for array de ids simples
                //$solicitacao->servicos()->sync(array_values($data['servicos']));
                dd("Passei Aqui!!");
            }

            DB::commit();

            return redirect()->route('solicitacoes_servicos.index')->with('success', 'Solicitação criada com sucesso.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors('Erro ao salvar: ' . $e->getMessage())->withInput();
        }
    /*

        // 1) Cria a solicitação
        $solicitacao = SolicitacaoServico::create([
            'descricao'             => $validated['descricao'],
            'data_solicitacao'      => now(),
            'atividade_id'          => $request->atividade_id,	       
            //'atividade_id' => $validated['atividade_id'],         
            'solicitante_matricula' => auth()->user()->empregado->matricula,
        ]);

        // 2) Cria as amostras (se vieram)
        if (!empty($validated['amostras'])) {
            foreach ($validated['amostras'] as $amostra) {
                //dd($amostra);
                // ignorar linhas totalmente vazias
                if (empty($amostra['descricao']) &&
                    empty($amostra['validade_dias']) &&
                    empty($amostra['condicao_armazenamento']) &&
                    empty($amostra['numero_cra'])) {
                    continue;
                }

                Amostra::create([
                    'descricao'              => $amostra['descricao'] ?? null,
                    'solicitacao_id'         => $solicitacao->solicitacao_servico_id,
                    'validade_dias'          => $amostra['validade_dias'] ?? null,
                    'condicao_armazenamento' => $amostra['condicao_armazenamento'] ?? null,
                    'numero_cra'             => $amostra['numero_cra'] ?? null,
                ]);
            }
        }

        return redirect()
            ->route('solicitacao-servico.index')
            ->with('success', 'Solicitação e amostras cadastradas com sucesso.');
    }       


    public function storeAmostra(Request $request, $solicitacaoId)
    {
        $data = $request->validate([
            'descricao'               => 'required|string|max:255',
            'validade_dias'           => 'nullable|integer',
            'condicao_armazenamento'  => 'nullable|string|max:255',
            'numero_cra'              => 'nullable|string|max:100',
        ]);

        $data['solicitacao_id'] = $solicitacaoId;

        Amostra::create($data);

        return redirect()
            ->route('solicitacao-servico.index')
            ->with('success', 'Amostra incluída com sucesso.');
    }
*/

}
}