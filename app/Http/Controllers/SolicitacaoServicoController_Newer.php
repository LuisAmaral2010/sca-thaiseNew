<?php

namespace App\Http\Controllers;
use App\Models\SolicitacaoServico;
use App\Models\Atividade;
use App\Models\Amostra;
use App\Models\UnidadeOperacional;
use App\Models\OrdemServico;
use App\Models\FracaoAmostra;
use App\Models\Servico; // se você tiver um modelo Servico para buscar unidade por serviço
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

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
        return view('solicitacoes_servicos.show', ['solicitacao' => $solicitacao_servico]);
    }

    // Carregar o formulário cadastrar nova Solicitação de Serviçoo
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

    
    public function store(Request $request)
    {
        //$message = "Cheguei Aqui!";
        //dd($message);
        // 1) VALIDAÇÃO
        $request->validate([
            'atividade_id' => ['required', 'integer'],
            'descricao' => ['required', 'string'],
            'amostras' => ['nullable', 'array'],
            'amostras..descricao' => ['nullable', 'string'],
            'amostras..validade_dias' => ['nullable', 'integer'],
            'amostras..condicao_armazenamento' => ['nullable', 'string'],
            // Serviços selecionados (inputs hidden gerados pelo JS)
            'servicos' => ['nullable', 'array'],
            'servicos.' => ['integer'],
            // Unidades de Atendimentos selecionados (inputs hidden gerados pelo JS)
            'unidades_operacionais' => ['nullable', 'array'],
            'unidades_operacionais.' => ['integer'],
        ]);


        $usuario = auth()->user();
        $matricula = optional($usuario->empregado)->matricula;

        DB::beginTransaction();

        try {
            $now = now();

            // 2) CRIA A SOLICITAÇÃO
            $solicitacao = SolicitacaoServico::create([
                'descricao' => $request->input('descricao'),
                'data_solicitacao' => $now,
                'atividade_id' => $request->input('atividade_id'),
                'solicitante_matricula' => $matricula,
                //'created_at' => $now,
                //'updated_at' => $now,
            ]);

            $solicitacaoId = $solicitacao->solicitacao_servico_id ?? $solicitacao->id;

            // 3) CRIA AS AMOSTRAS (IGNORANDO LINHAS VAZIAS)
            $amostras = $request->input('amostras', []);
            $amostrasCriadas = [];

            foreach ($amostras as $a) {
                $descricao = trim((string) ($a['descricao'] ?? ''));
                $validade = $a['validade_dias'] ?? null;
                $condicao = $a['condicao_armazenamento'] ?? null;

                if ($descricao === '' && ($validade === null || $validade === '') && ($condicao === null || $condicao === '')) {
                    continue;
                }

                $amostra = Amostra::create([
                    'descricao' => $descricao,
                    'solicitacao_id' => $solicitacaoId,
                    'validade_dias' => $validade,
                    'condicao_armazenamento' => $condicao,
                    'numero_cra' => null,
                ]);

                $amostrasCriadas[] = $amostra;
            }

            // 4) CRIA AS ORDENS DE SERVIÇO (uma por serviço selecionado)
            // Observação: a view envia apenas servicos[] com os IDs dos serviços.
            // Aqui assumimos que existe um model Servico que relaciona serviço -> unidade_operacional_id.
            $ordensCriadas = [];
            $servicos = $request->input('servicos', []);
            $servicoParaOrdem = []; // map servico_id => ordem_servico_id (ou id)

            $unidades_operacionais = $request->input('unidades_operacionais', []);
            $unidades_operacionaisParaOrdem = []; // map servico_id => ordem_servico_id (ou id)

if (!empty($unidades_operacionais)) {
    echo"<script>alerta('Passei Aqui !!');</script>";
    //@dd('Passei Aqui');
}
else{
//echo"<script>alerta('Não Passei Aqui !!');</script>";
}


            if (!empty($servicos)) {
                //echo"<script>alerta('Passei Aqui !!');</script>";
            
                foreach ($servicos as $servicoId) {
                    
                    // tenta recuperar unidade_operacional_id a partir do serviço
                    $unidadeId = null;
                    try {
                        $servicoModel = Servico::find($servicoId);
                        if ($servicoModel) {
                            // ajuste o nome do atributo conforme seu model (ex: unidade_operacional_id)
                            $unidadeId = $servicoModel->unidade_operacional_id ?? null;
                            //@dd('Aqui !!!');
                        }
                    } catch (\Throwable $e) {
                        // se model Servico não existir, prossegue com unidade null
                        $unidadeId = null;
                    }
                    
                    // Cria a ordem de serviço. Não atribuímos explicitamente ordem_servico_id
                    // para que o DB gere o valor automático. Caso precise atribuir algum
                    // valor específico, altere aqui.
                    
                    //$amostras = $request->input('amostras', []);
                    
                    $ordem = OrdemServico::create([
                        // 'ordem_servico_id' => 1, //<se necessário definir manualmente>,
                        'status_atual' => 'ENVIADO_LABORATORIO',
                        'data_status_atual' => $now,
                        'observacao' => null,
                        'recebedor_matricula' => null,
                        'solicitacao_servico_id' => $solicitacaoId,
                        'unidade_operacional_id' => $unidadeId, //$unidadeId,
                        //'created_at' => $now,
                        //'updated_at' => $now,
                    ]);

                    $ordemId = $ordem->ordem_servico_id ?? $ordem->id ?? null;
                    $servicoParaOrdem[$servicoId] = $ordemId;

                    $ordensCriadas[] = [
                        'servico_id' => $servicoId, //retirar?
                        'ordem_servico_id' => $ordemId, //retirar?
                        //'ordem_servico_id' => $ordem->ordem_servico_id ?? $ordem->id ?? null,
                        'status_atual' => 'ENVIADO_LABORATORIO', //$ordem->status_atual,
                        'data_status_atual' => $now, //$ordem->data_status_atual,
                        'unidade_operacional_id' => $unidadeId, //$ordem->unidade_operacional_id,
                    ];
                    
                }
            }


            // 5) CRIA AS FRAÇÕES (FracaoAmostra) para cada combinação amostra x serviço
            // Campos conforme solicitado:
            // 'fracao_amostra_id' => não informado (é PK/autoincrement)
            // 'status_atual' => 'ENVIADO_LABORATORIO'
            // 'data_status_atual' => now()
            // 'observacao' => null
            // 'amostra_id' => id da amostra criada
            // 'servico_id' => id do serviço (do request)
            // 'ordem_servico_id' => id da ordem correspondente (se existente)
            // 'responsavel_execucao_matricula' => null

            //if (!empty($amostrasCriadas) && !empty($servicos)) {
            if (!empty($servicos)) {
                foreach ($amostrasCriadas as $amostraModel) {
                    $amostraId = $amostraModel->amostra_id ?? $amostraModel->id ?? 1;

                    foreach ($servicos as $servicoId) {
                        $ordemId = $servicoParaOrdem[$servicoId] ?? null;

                        FracaoAmostra::create([
                            // fracao_amostra_id => omitido para autoincrement/PK
                            'data_recebido_cra' => null,
                            'numero' => null,
                            'observacao' => null,
                            'observacao_cra' => null,
                            'recebido_cra' => null,
                            'amostra_id' => $amostraId,
                        ]);                        
                   }
                }
            }

            DB::commit();

            // Monta array de amostras para view/retorno
            $amostrasRet = $solicitacao->amostras()->get()->map(function($a){
                return [
                    'descricao' => $a->descricao,
                    'validade_dias' => $a->validade_dias,
                    'condicao_armazenamento' => $a->condicao_armazenamento,
                ];
            })->toArray();

        // Se precisar montar serviços por unidade (dependendo de como enviou), implemente aqui.
        $servicosPorUnidade = []; // ou construir conforme necessidade

        // 6) RESPOSTA PARA AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'id' => $solicitacaoId,
                'redirect_url' => route('solicitacoes_servicos.show', $solicitacao),
                
                'solicitacao' => [
                    'descricao' => $solicitacao->descricao,
                    'atividade_id' => $solicitacao->atividade_id,
                    'solicitante_matricula' => $solicitacao->solicitante_matricula,
                    'data_solicitacao' => $solicitacao->data_solicitacao,
                ],
                'amostras' => $amostrasRet,
                'ordens' => $ordensCriadas,
                'servicosPorUnidade' => $servicosPorUnidade,
            ]);
        }

        // 7) RESPOSTA NORMAL (NÃO AJAX)
        return redirect()
            ->route('solicitacoes_servicos.show', $solicitacao)
            ->with('success', 'Solicitação cadastrada com sucesso!');

        } catch (\Throwable $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Falha ao salvar: ' . $e->getMessage()], 500);
            }
            return back()->withInput()->withErrors(['erro' => 'Falha ao salvar: ' . $e->getMessage()]);
        }

    }
}