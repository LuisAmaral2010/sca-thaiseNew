<?php

namespace App\Http\Controllers;
use App\Models\SolicitacaoServico;
use App\Models\Atividade;
use App\Models\Amostra;
use App\Models\UnidadeOperacional;
use App\Models\OrdemServico;
use App\Models\FracaoAmostra;
use App\Models\FracaoAmostraServico;
use App\Models\Servico; // se você tiver um modelo Servico para buscar unidade por serviço
use App\Models\ExecucaoAnalise;
use App\Models\ClienteExterno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SolicitacaoServicoController extends Controller
{
    // Listar as solicitacao_servicos
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

        $solicitacoes_servicos = SolicitacaoServico::with('atividade')
            ->when($dataInicial, fn ($query) => $query->whereDate('data_solicitacao', '>=', $dataInicial))
            ->when($dataFinal, fn ($query) => $query->whereDate('data_solicitacao', '<=', $dataFinal))
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return view('solicitacoes_servicos.index', compact('solicitacoes_servicos', 'sort', 'direction', 'dataInicial', 'dataFinal'));
    }

    // Visualizar a solicitacao_servico
    public function show(SolicitacaoServico $solicitacao_servico)
    {
        $solicitacao_servico->load(['atividade', 'empregado', 'amostras', 'ordemServico.unidadeOperacional']);

        $ordens = $solicitacao_servico->ordemServico->keyBy('ordem_servico_id');

        // Frações de cada amostra (uma por ordem de serviço / unidade
        // operacional), com os serviços vinculados via execucao_analise
        $fracoesPorAmostra = FracaoAmostra::whereIn('amostra_id', $solicitacao_servico->amostras->pluck('amostra_id'))
            ->with('execucoes_analises.servico')
            ->orderBy('fracao_amostra_id')
            ->get()
            ->groupBy('amostra_id');

        // Mesma estrutura do resumo do modal #modalResumo (create.blade.php):
        // amostra -> frações (unidade operacional) -> serviços
        $resumo = $solicitacao_servico->amostras->sortBy('amostra_id')->map(function (Amostra $amostra) use ($fracoesPorAmostra, $ordens) {
            $fracoes = $fracoesPorAmostra->get($amostra->amostra_id, collect())->map(fn (FracaoAmostra $fracao) => [
                'status' => $fracao->status_atual,
                'unidade' => $ordens->get($fracao->ordem_servico_id)?->unidadeOperacional?->nome
                    ?? 'Unidade ' . ($ordens->get($fracao->ordem_servico_id)?->unidade_operacional_id ?? '-'),
                'servicos' => $fracao->execucoes_analises
                    ->filter(fn ($execucao) => $execucao->servico)
                    ->map(fn ($execucao) => [
                        'descricao' => $execucao->servico->descricao,
                        'tipo' => $execucao->servico->tipo_servico,
                    ])
                    ->values(),
            ])->values();

            return [
                'amostra' => $amostra,
                'fracoes' => $fracoes,
                // Cada fração ocupa ao menos uma linha, mesmo sem serviços
                'total_linhas' => max(1, $fracoes->sum(fn ($fracao) => max(1, $fracao['servicos']->count()))),
            ];
        })->values();

        // Histórico de status (ordem_servico_historico) de cada ordem de
        // serviço da solicitação, para o gráfico de trace
        $historico = DB::table('ordem_servico_historico')
            ->whereIn('ordem_servico_id', $ordens->keys())
            ->orderBy('data_status_atual')
            ->orderBy('ordem_servico_historico_id')
            ->get()
            ->groupBy('ordem_servico_id');

        $tracos = $ordens->sortKeys()->map(function (OrdemServico $ordem) use ($historico) {
            $nome = $ordem->unidadeOperacional?->nome ?? 'Unidade ' . $ordem->unidade_operacional_id;

            return [
                'ordem_servico_id' => $ordem->ordem_servico_id,
                'nome' => $nome,
                // Sigla entre parênteses no fim do nome (ex.: "LASI"), usada no rótulo do traço
                'sigla' => preg_match('/\(([^)]+)\)\s*$/', $nome, $m) ? $m[1] : $nome,
                'eventos' => $historico->get($ordem->ordem_servico_id, collect())
                    ->map(fn ($registro) => [
                        'status' => $registro->status_atual,
                        'data' => \Carbon\Carbon::parse($registro->data_status_atual)->toIso8601String(),
                    ])
                    ->values(),
            ];
        })->filter(fn ($traco) => $traco['eventos']->isNotEmpty())->values();

        // Carregar a view
        return view('solicitacoes_servicos.show', [
            'solicitacao' => $solicitacao_servico,
            'resumo' => $resumo,
            'tracos' => $tracos,
        ]);
    }

    // Carregar o formulário cadastrar nova Solicitação de Serviçoo
    public function create()
    {
        // Captura o valor da matricula do usuário logado
        $matricula = auth()->user()?->empregado?->matricula;

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

        // Requisição externa (formulário do CRA em
        // /cra/solicitacoes_servicos_externos/create): sem atividade e com um
        // cliente externo cadastrado (e ativo)
        $externo = $request->routeIs('cra.solicitacoes_servicos_externos.store');

        // 1) VALIDAÇÃO
        $request->validate([
            'atividade_id' => $externo ? ['prohibited'] : ['required', 'integer'],
            'cliente_externo_id' => $externo
                ? ['required', 'integer', Rule::exists('cliente_externo', 'cliente_externo_id')->where('is_ativo', true)]
                : ['prohibited'],
            'solicitante_externo_nome' => ['prohibited'],
            'descricao' => ['required', 'string'],
            'amostras' => ['nullable', 'array'],
            'amostras..descricao' => ['nullable', 'string'],
            'amostras..validade_dias' => ['nullable', 'integer'],
            'amostras..condicao_armazenamento' => ['nullable', 'string'],
            // Serviços selecionados (inputs hidden gerados pelo JS)
            'servicos' => ['nullable', 'array'],
            'servicos.' => ['integer'],
        ]);


        $usuario = auth()->user();
        $matricula = optional($usuario->empregado)->matricula;

        if (!$matricula) {
            $message = 'Sua conta de usuário não está vinculada a um empregado, então não é possível registrar o solicitante. Contate o administrador do sistema para associar seu usuário a um registro de empregado.';

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->withInput()->withErrors(['erro' => $message]);
        }

        DB::beginTransaction();

        try {
            $now = now();

            // 2) CRIA A SOLICITAÇÃO
            $solicitacao = SolicitacaoServico::create([
                'descricao' => $request->input('descricao'),
                'data_solicitacao' => $now,
                'atividade_id' => $externo ? null : $request->input('atividade_id'),
                'solicitante_matricula' => $matricula,
                // O nome do cliente fica gravado como estava no momento da requisição
                'cliente_externo_id' => $externo ? $request->integer('cliente_externo_id') : null,
                'solicitante_externo_nome' => $externo
                    ? ClienteExterno::whereKey($request->integer('cliente_externo_id'))->value('nome')
                    : null,
                'status' => 'CRIADO',
                'created_at' => $now,
                'updated_at' => $now,
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

            // 4) CRIA AS ORDENS DE SERVIÇO (uma por unidade operacional distinta entre os serviços selecionados)
            $ordensCriadas = [];
            $servicos = $request->input('servicos', []);
            $servicoParaOrdem = []; // servico_id => ordem_servico_id
            $ordemPorUnidade = [];  // unidade_operacional_id => ordem_servico_id

            $servicoModels = Servico::whereIn('servico_id', $servicos)->get()->keyBy('servico_id');

            foreach ($servicos as $servicoId) {
                $servicoModel = $servicoModels->get($servicoId);

                if (!$servicoModel) {
                    continue;
                }

                $unidadeId = $servicoModel->unidade_operacional_id;

                if (!isset($ordemPorUnidade[$unidadeId])) {
                    $ordem = OrdemServico::create([
                        'status_atual' => 'CRIADO',
                        'data_status_atual' => $now,
                        'observacao' => null,
                        'recebedor_matricula' => null,
                        'solicitacao_servico_id' => $solicitacaoId,
                        'unidade_operacional_id' => $unidadeId,
                    ]);

                    $ordemPorUnidade[$unidadeId] = $ordem->ordem_servico_id;

                    $ordem->registrarHistorico();

                    $ordensCriadas[] = [
                        'ordem_servico_id' => $ordem->ordem_servico_id,
                        'status_atual' => $ordem->status_atual,
                        'data_status_atual' => $now,
                        'unidade_operacional_id' => $unidadeId,
                    ];
                }

                $servicoParaOrdem[$servicoId] = $ordemPorUnidade[$unidadeId];
            }

            // 5) CRIA AS FRAÇÕES (FracaoAmostra), uma para cada combinação
            // amostra x ordem_servico (o produto cartesiano das amostras com
            // as ordens geradas no passo anterior), e a respectiva
            // ExecucaoAnalise para cada serviço selecionado dentro daquela
            // ordem, todas apontando para a mesma fração.
            $fracoesCriadas = [];

            foreach ($amostrasCriadas as $amostraModel) {
                $fracaoPorOrdem = [];

                foreach ($servicos as $servicoId) {
                    $ordemId = $servicoParaOrdem[$servicoId] ?? null;

                    if (!isset($fracaoPorOrdem[$ordemId])) {
                        $fracao = FracaoAmostra::create([
                            'status_atual' => 'CRIADO',
                            'data_status_atual' => $now,
                            'observacao' => null,
                            'amostra_id' => $amostraModel->amostra_id,
                            'servico_id' => null,
                            'ordem_servico_id' => $ordemId,
                            'responsavel_execucao_matricula' => null,
                        ]);

                        $fracaoPorOrdem[$ordemId] = $fracao;
                        $fracoesCriadas[] = $fracao;
                    }

                    ExecucaoAnalise::create([
                        'fracao_amostra_id' => $fracaoPorOrdem[$ordemId]->fracao_amostra_id,
                        'laudo_id' => null,
                        'ordem_servico_id' => $ordemId,
                        'servico_id' => $servicoId,
                        'is_concluido' => false,
                        'data_conclusao' => null,
                        'is_cancelado' => false,
                        'data_cancelamento' => null,
                        'observacao' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    // Serviço da fração, ainda sem laudo criado
                    FracaoAmostraServico::firstOrCreate([
                        'fracao_amostra_id' => $fracaoPorOrdem[$ordemId]->fracao_amostra_id,
                        'servico_id' => $servicoId,
                    ], [
                        'laudo_criado' => false,
                    ]);
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

    // Atribui à solicitação, ainda em rascunho (status CRIADO), o próximo
    // número de solicitação no formato 00001-yyyy (contador reiniciado a
    // cada virada de ano), chamado ao clicar em "Finalizar" no formulário
    // de criação. Idempotente: se a solicitação já tiver um número, mantém.
    public function finalizar(Request $request, SolicitacaoServico $solicitacao_servico)
    {
        if ($solicitacao_servico->status !== 'CRIADO') {
            $message = 'Esta solicitação não está mais disponível para finalização.';

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->withErrors(['erro' => $message]);
        }

        DB::transaction(function () use ($solicitacao_servico) {
            if (!empty($solicitacao_servico->numero_solicitacao_servico)) {
                return;
            }

            $sufixo = '-' . now()->year;

            $ultimoNumero = SolicitacaoServico::where('numero_solicitacao_servico', 'like', '%' . $sufixo)
                ->lockForUpdate()
                ->orderByDesc('numero_solicitacao_servico')
                ->value('numero_solicitacao_servico');

            $contador = $ultimoNumero ? ((int) substr($ultimoNumero, 0, 5)) + 1 : 1;

            $solicitacao_servico->update([
                'numero_solicitacao_servico' => str_pad((string) $contador, 5, '0', STR_PAD_LEFT) . $sufixo,
            ]);
        });

        $solicitacao_servico->refresh();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'numero_solicitacao_servico' => $solicitacao_servico->numero_solicitacao_servico,
            ]);
        }

        return back()->with('success', 'Solicitação finalizada com sucesso!');
    }

    // Envia para o CRA uma solicitação ainda em rascunho (status CRIADO),
    // atualizando o status dela, das ordens de serviço e das frações de amostra.
    public function enviarCra(Request $request, SolicitacaoServico $solicitacao_servico)
    {
        if ($solicitacao_servico->status !== 'CRIADO') {
            $message = 'Esta solicitação não está mais disponível para envio ao CRA.';

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->withErrors(['erro' => $message]);
        }

        $now = now();

        DB::transaction(function () use ($solicitacao_servico, $now) {
            $solicitacao_servico->update(['status' => 'ENVIADO_CRA']);

            $ordemIds = $solicitacao_servico->ordemServico()->pluck('ordem_servico_id');

            $solicitacao_servico->ordemServico()->update([
                'status_atual' => 'ENVIADO_CRA',
                'data_status_atual' => $now,
            ]);

            $solicitacao_servico->ordemServico()->get()
                ->each(fn (OrdemServico $ordem) => $ordem->registrarHistorico());

            FracaoAmostra::whereIn('ordem_servico_id', $ordemIds)->update([
                'status_atual' => 'ENVIADO_CRA',
                'data_status_atual' => $now,
            ]);
        });

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect_url' => route('solicitacoes_servicos.show', $solicitacao_servico),
            ]);
        }

        return redirect()
            ->route('solicitacoes_servicos.show', $solicitacao_servico)
            ->with('success', 'Solicitação enviada ao CRA com sucesso!');
    }

    // Cancela uma solicitação ainda em rascunho (status CRIADO), sem excluir
    // os registros — apenas marca solicitação, ordens e frações como CANCELADO_PELO_USUARIO.
    public function cancelar(Request $request, SolicitacaoServico $solicitacao_servico)
    {
        if ($solicitacao_servico->status !== 'CRIADO') {
            $message = 'Esta solicitação não está mais disponível para cancelamento.';

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->withErrors(['erro' => $message]);
        }

        $now = now();

        DB::transaction(function () use ($solicitacao_servico, $now) {
            $solicitacao_servico->update(['status' => 'CANCELADO_PELO_USUARIO']);

            $ordemIds = $solicitacao_servico->ordemServico()->pluck('ordem_servico_id');

            $solicitacao_servico->ordemServico()->update([
                'status_atual' => 'CANCELADO_PELO_USUARIO',
                'data_status_atual' => $now,
            ]);

            $solicitacao_servico->ordemServico()->get()
                ->each(fn (OrdemServico $ordem) => $ordem->registrarHistorico());

            FracaoAmostra::whereIn('ordem_servico_id', $ordemIds)->update([
                'status_atual' => 'CANCELADO_PELO_USUARIO',
                'data_status_atual' => $now,
            ]);
        });

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect_url' => route('solicitacoes_servicos.index'),
            ]);
        }

        return redirect()
            ->route('solicitacoes_servicos.index')
            ->with('success', 'Solicitação cancelada.');
    }
}