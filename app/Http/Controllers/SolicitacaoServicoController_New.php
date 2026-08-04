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

 /*
    public function store(Request $request)
    {
        $request->validate([
            'atividade_id' => ['required', 'integer'],
            'descricao' => ['required', 'string'],
            'amostras' => ['nullable', 'array'],
            'amostras.*.descricao' => ['nullable', 'string'],
            'amostras.*.validade_dias' => ['nullable', 'integer'],
            'amostras.*.condicao_armazenamento' => ['nullable', 'string'],
        ]);

        $usuario = auth()->user();
        $matricula = optional($usuario->empregado)->matricula;

        DB::beginTransaction();

        try {
            // Criar Solicitação de Serviço
            $now = now();

            $solicitacao = SolicitacaoServico::create([
                // 'solicitacao_servico_id' é auto-increment, não setamos
                'descricao' => $request->input('descricao'),
                'data_solicitacao' => $now,
                'atividade_id' => $request->input('atividade_id'),
                'solicitante_matricula' => $matricula,
                'created_at' => $now,
                'updated_at' => $now, // se sua coluna for updated_at, ajuste para 'updated_at'
            ]);

            // Criar Amostras (se houver)
            $amostras = $request->input('amostras', []);
            foreach ($amostras as $a) {
                //dd($amostras);
                //dd($a);
                // Ignora linhas totalmente vazias
                $descricao = trim((string) ($a['descricao'] ?? ''));
                $validade = $a['validade_dias'] ?? null;
                $condicao = $a['condicao_armazenamento'] ?? null;
                //dd($validade);
                if ($descricao === '' && ($validade === null || $validade === '') && ($condicao === null || $condicao === '')) {
                    continue;
                }

                Amostra::create([
                    // 'amostra_id' é auto-increment
                    'descricao' => $descricao,
                    'solicitacao_id' => $solicitacao->solicitacao_servico_id ?? $solicitacao->id,
                    'validade_dias' => $validade,
                    'condicao_armazenamento' => $condicao,
                    'numero_cra' => null,
                ]);
            }

            DB::commit();


            // Após commit da transação, monte dados para a view:
            $amostras = $solicitacao->amostras()->get()->map(function($a){
                return [
                    'descricao' => $a->descricao,
                    'validade_dias' => $a->validade_dias,
                    'condicao_armazenamento' => $a->condicao_armazenamento,
                ];
            })->toArray();

            // Exemplo de montagem de serviços por unidade se você salvou relacionamentos:
            // $servicosPorUnidade = [ 'Unidade A' => [ ['servico_id'=>1,'descricao'=>'X','tipo_servico'=>'Y'], ... ], ... ];
            // Ajuste conforme seu modelo/estrutura real.

            return view('solicitacoes_servicos.resumo', [
                'solicitacao' => $solicitacao,
                'amostras' => $amostras,
                'servicosPorUnidade' => $servicosPorUnidade ?? []
            ]);
                        
            } catch (\Throwable $e) {
                DB::rollBack();
                return back()->withInput()->withErrors(['erro' => 'Falha ao salvar: ' . $e->getMessage()]);
            }
        }

    }
*/

public function store(Request $request)
{
    // ... validação, criação de registro, etc ...

    // Exemplo:
    // $solicitacao = SolicitacaoServico::create([...]);

    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'id' => $solicitacao->id ?? null,
            'redirect_url' => route('solicitacoes_servicos.show', $solicitacao ?? 1), // ajuste conforme sua rota
        ]);
    }

    // Comportamento normal (se NÃO for AJAX)
    return redirect()
        ->route('solicitacoes_servicos.show', $solicitacao)
        ->with('success', 'Solicitação cadastrada com sucesso!');
}



}