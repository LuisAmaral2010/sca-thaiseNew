<?php

namespace App\Http\Controllers;

use App\Models\Amostra;
use App\Models\FracaoAmostra;
use App\Models\OrdemServico;
use App\Models\SolicitacaoServico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CraReceberAmostraController extends Controller
{
    // Ordem de serviço já finalizada pelo CRA (recebida ou rejeitada); qualquer
    // outro valor de status_atual (incluindo vazio) é considerado pendente
    private const STATUS_FINALIZADOS = ['RECEBIDO_CRA', 'REJEITADO_CRA'];

    // Lista as solicitações de serviço aguardando recebimento pelo CRA
    public function index()
    {
        $solicitacoes = SolicitacaoServico::where('status', 'ENVIADO_CRA')
            ->with(['atividade', 'empregado'])
            ->withCount(['ordemServico as ordens_pendentes_count' => function ($query) {
                $query->whereNotIn('status_atual', self::STATUS_FINALIZADOS);
            }])
            ->withCount(['ordemServico as ordens_rejeitadas_count' => function ($query) {
                $query->where('status_atual', 'REJEITADO_CRA');
            }])
            ->orderBy('data_solicitacao')
            ->get();

        return Inertia::render('Cra/ReceberAmostra/Index', compact('solicitacoes'));
    }

    // Lista, na forma de Amostra x Unidade Operacional / Serviço, todas as
    // ordens de serviço de uma solicitação específica, independente do
    // status — mesma relação exibida no painel de resumo do formulário de
    // criação da solicitação.
    public function ordens(SolicitacaoServico $solicitacao_servico)
    {
        $ordens = $solicitacao_servico->ordemServico()
            ->with([
                'unidadeOperacional',
                'fracoesAmostra.amostra',
                'fracoesAmostra.execucoes_analises.servico',
            ])
            ->get();

        abort_if($ordens->isEmpty(), 404);

        $linhaBase = fn ($ordem) => [
            'ordem_servico_id' => $ordem->ordem_servico_id,
            'unidade_operacional' => $ordem->unidadeOperacional->nome ?? '—',
            'ordem_status_atual' => $ordem->status_atual,
        ];

        $linhas = $ordens->flatMap(function ($ordem) use ($linhaBase) {
            if ($ordem->fracoesAmostra->isEmpty()) {
                return [array_merge($linhaBase($ordem), [
                    'fracao_amostra_id' => null,
                    'fracao_status_atual' => null,
                    'amostra_descricao' => null,
                    'amostra_validade_dias' => null,
                    'amostra_condicao_armazenamento' => null,
                    'servico_descricao' => null,
                    'servico_tipo_servico' => null,
                ])];
            }

            return $ordem->fracoesAmostra->flatMap(function ($fracao) use ($ordem, $linhaBase) {
                $linhaFracao = array_merge($linhaBase($ordem), [
                    'fracao_amostra_id' => $fracao->fracao_amostra_id,
                    'fracao_status_atual' => $fracao->status_atual,
                    'amostra_descricao' => $fracao->amostra->descricao ?? null,
                    'amostra_validade_dias' => $fracao->amostra->validade_dias ?? null,
                    'amostra_condicao_armazenamento' => $fracao->amostra->condicao_armazenamento ?? null,
                ]);

                if ($fracao->execucoes_analises->isEmpty()) {
                    return [array_merge($linhaFracao, [
                        'servico_descricao' => null,
                        'servico_tipo_servico' => null,
                    ])];
                }

                return $fracao->execucoes_analises->map(fn ($execucao) => array_merge($linhaFracao, [
                    'servico_descricao' => $execucao->servico->descricao ?? null,
                    'servico_tipo_servico' => $execucao->servico->tipo_servico ?? null,
                ]));
            });
        })->values();

        return Inertia::render('Cra/ReceberAmostra/Ordens', [
            'solicitacao' => $solicitacao_servico,
            'linhas' => $linhas,
        ]);
    }

    // Todas as ordens de uma solicitação, independente do status — usado
    // pelas ações "todas" (Receber todas / Rejeitar todas), que se aplicam a
    // todas as linhas da página, não só às pendentes.
    private function todasOrdensDaSolicitacao(SolicitacaoServico $solicitacao_servico)
    {
        $ordens = $solicitacao_servico->ordemServico()
            ->with('unidadeOperacional')
            ->get();

        abort_if($ordens->isEmpty(), 404);

        return $ordens;
    }

    // Formulário de confirmação de recebimento de todas as ordens de uma solicitação
    public function receberTodasForm(SolicitacaoServico $solicitacao_servico)
    {
        $ordens = $this->todasOrdensDaSolicitacao($solicitacao_servico);

        return Inertia::render('Cra/ReceberAmostra/ReceberTodas', [
            'solicitacao' => $solicitacao_servico,
            'ordens' => $ordens->map(fn ($ordem) => [
                'ordem_servico_id' => $ordem->ordem_servico_id,
                'unidade_operacional' => $ordem->unidadeOperacional->nome ?? '—',
            ])->values(),
        ]);
    }

    // Confirma o recebimento de todas as ordens de uma solicitação, aplicando
    // ACEITO_CRA a todas as frações de todas as linhas da página
    public function receberTodas(Request $request, SolicitacaoServico $solicitacao_servico)
    {
        $validated = $request->validate([
            'data_recebimento' => ['required', 'date'],
            'observacao' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($this->todasOrdensDaSolicitacao($solicitacao_servico) as $ordem) {
            $ordem->update([
                'data_status_atual' => $validated['data_recebimento'],
                'observacao' => $validated['observacao'] ?? $ordem->observacao,
                'recebedor_matricula' => auth()->user()?->empregado?->matricula,
            ]);

            FracaoAmostra::where('ordem_servico_id', $ordem->ordem_servico_id)
                ->update(['status_atual' => 'ACEITO_CRA']);

            $this->sincronizarStatusOrdem($ordem);
        }

        return redirect()
            ->route('cra.receber-amostra.ordens', $solicitacao_servico->solicitacao_servico_id)
            ->with('success', 'Todas as amostras foram recebidas com sucesso!');
    }

    // Formulário de confirmação de rejeição de todas as ordens de uma solicitação
    public function rejeitarTodasForm(SolicitacaoServico $solicitacao_servico)
    {
        $ordens = $this->todasOrdensDaSolicitacao($solicitacao_servico);

        return Inertia::render('Cra/ReceberAmostra/RejeitarTodas', [
            'solicitacao' => $solicitacao_servico,
            'ordens' => $ordens->map(fn ($ordem) => [
                'ordem_servico_id' => $ordem->ordem_servico_id,
                'unidade_operacional' => $ordem->unidadeOperacional->nome ?? '—',
            ])->values(),
        ]);
    }

    // Confirma a rejeição de todas as ordens de uma solicitação, aplicando
    // REJEITADO_CRA a todas as frações de todas as linhas da página
    public function rejeitarTodas(Request $request, SolicitacaoServico $solicitacao_servico)
    {
        $validated = $request->validate([
            'data_rejeicao' => ['required', 'date'],
            'motivo' => ['required', 'string', 'max:255'],
        ]);

        foreach ($this->todasOrdensDaSolicitacao($solicitacao_servico) as $ordem) {
            $ordem->update([
                'data_status_atual' => $validated['data_rejeicao'],
                'observacao' => $validated['motivo'],
                'recebedor_matricula' => auth()->user()?->empregado?->matricula,
            ]);

            FracaoAmostra::where('ordem_servico_id', $ordem->ordem_servico_id)
                ->update(['status_atual' => 'REJEITADO_CRA']);

            $this->sincronizarStatusOrdem($ordem);
        }

        return redirect()
            ->route('cra.receber-amostra.ordens', $solicitacao_servico->solicitacao_servico_id)
            ->with('success', 'Todas as amostras foram rejeitadas com sucesso!');
    }

    // Cancela a requisição inteira (todas as frações já rejeitadas)
    public function rejeitarRequisicao(SolicitacaoServico $solicitacao_servico)
    {
        $solicitacao_servico->update(['status' => 'CANCELADO_CRA']);

        FracaoAmostra::whereIn('ordem_servico_id', $solicitacao_servico->ordemServico()->pluck('ordem_servico_id'))
            ->update(['status_atual' => 'CANCELADO_CRA']);

        return redirect()
            ->route('cra.receber-amostra.index')
            ->with('success', 'Requisição rejeitada com sucesso!');
    }

    // Envia para o(s) laboratório(s) as frações já aceitas pelo CRA
    public function enviarLaboratorio(SolicitacaoServico $solicitacao_servico)
    {
        DB::transaction(function () use ($solicitacao_servico) {
            $solicitacao_servico->update(['status' => 'ENVIADO_LABORATORIO']);

            $solicitacao_servico->ordemServico()->update(['status_atual' => 'ENVIADO_LABORATORIO']);

            FracaoAmostra::whereIn('ordem_servico_id', $solicitacao_servico->ordemServico()->pluck('ordem_servico_id'))
                ->where('status_atual', 'ACEITO_CRA')
                ->update(['status_atual' => 'ENVIADO_LABORATORIO']);

            $this->atribuirNumerosCra($solicitacao_servico);
        });

        return redirect()
            ->route('cra.receber-amostra.index')
            ->with('success', 'Amostras enviadas para o(s) laboratório(s) com sucesso!');
    }

    // Atribui a cada amostra da solicitação, que ainda não tenha um número de
    // CRA, o próximo número no formato 99999-yyyy (contador reiniciado a
    // cada virada de ano). O bloqueio pessimista evita que dois envios
    // simultâneos gerem o mesmo número.
    private function atribuirNumerosCra(SolicitacaoServico $solicitacao_servico): void
    {
        $amostras = Amostra::where('solicitacao_id', $solicitacao_servico->solicitacao_servico_id)
            ->where(function ($query) {
                $query->whereNull('numero_cra')
                    ->orWhere('numero_cra', '')
                    ->orWhere('numero_cra', 'not like', '%-%');
            })
            ->lockForUpdate()
            ->get();

        if ($amostras->isEmpty()) {
            return;
        }

        $sufixo = '-' . now()->year;

        $ultimoNumero = Amostra::where('numero_cra', 'like', '%' . $sufixo)
            ->lockForUpdate()
            ->orderByDesc('numero_cra')
            ->value('numero_cra');

        $contador = $ultimoNumero ? ((int) substr($ultimoNumero, 0, 5)) + 1 : 1;

        foreach ($amostras as $amostra) {
            $amostra->update(['numero_cra' => str_pad((string) $contador, 5, '0', STR_PAD_LEFT) . $sufixo]);
            $contador++;
        }
    }

    // Restringe a atualização de status à fração específica que originou a
    // ação, quando informada; caso contrário (ordem sem fração associada),
    // aplica a todas as frações da ordem. Em seguida sincroniza o status da
    // ordem com o consenso das frações.
    private function atualizarFracoes(OrdemServico $ordem_servico, ?int $fracaoAmostraId, string $status): void
    {
        FracaoAmostra::where('ordem_servico_id', $ordem_servico->ordem_servico_id)
            ->when($fracaoAmostraId, fn ($query) => $query->where('fracao_amostra_id', $fracaoAmostraId))
            ->update(['status_atual' => $status]);

        $this->sincronizarStatusOrdem($ordem_servico);
    }

    // A ordem só é marcada como RECEBIDO_CRA/REJEITADO_CRA quando TODAS as
    // suas frações concordam (todas aceitas ou todas rejeitadas). Se houver
    // frações ainda pendentes, ou um resultado misto (algumas aceitas e
    // outras rejeitadas), o status da ordem não é alterado.
    private function sincronizarStatusOrdem(OrdemServico $ordem_servico): void
    {
        $statusFracoes = FracaoAmostra::where('ordem_servico_id', $ordem_servico->ordem_servico_id)
            ->pluck('status_atual')
            ->unique();

        if ($statusFracoes->count() !== 1) {
            return;
        }

        $status = match ($statusFracoes->first()) {
            'ACEITO_CRA' => 'RECEBIDO_CRA',
            'REJEITADO_CRA' => 'REJEITADO_CRA',
            default => null,
        };

        if ($status !== null) {
            $ordem_servico->update(['status_atual' => $status]);
        }
    }

    // Formulário de confirmação de recebimento de uma ordem específica
    public function show(Request $request, OrdemServico $ordem_servico)
    {
        $ordem_servico->load(['solicitacaoServico', 'unidadeOperacional']);

        $fracaoAmostraId = $request->query('fracao_amostra');
        $fracao = $fracaoAmostraId ? FracaoAmostra::with('amostra')->find($fracaoAmostraId) : null;

        return Inertia::render('Cra/ReceberAmostra/Show', [
            'ordem' => $ordem_servico,
            'fracao_amostra_id' => $fracaoAmostraId,
            'amostra_descricao' => $fracao->amostra->descricao ?? null,
        ]);
    }

    // Confirma o recebimento da amostra pelo CRA
    public function store(Request $request, OrdemServico $ordem_servico)
    {
        $validated = $request->validate([
            'data_recebimento' => ['required', 'date'],
            'observacao' => ['nullable', 'string', 'max:255'],
            'fracao_amostra_id' => ['nullable', 'integer'],
        ]);

        $ordem_servico->update([
            'data_status_atual' => $validated['data_recebimento'],
            'observacao' => $validated['observacao'] ?? $ordem_servico->observacao,
            'recebedor_matricula' => auth()->user()?->empregado?->matricula,
        ]);

        $this->atualizarFracoes($ordem_servico, $validated['fracao_amostra_id'] ?? null, 'ACEITO_CRA');

        return redirect()
            ->route('cra.receber-amostra.ordens', $ordem_servico->solicitacao_servico_id)
            ->with('success', 'Amostra recebida com sucesso!');
    }

    // Formulário de confirmação de rejeição de uma ordem específica
    public function rejeitarForm(Request $request, OrdemServico $ordem_servico)
    {
        $ordem_servico->load(['solicitacaoServico', 'unidadeOperacional']);

        $fracaoAmostraId = $request->query('fracao_amostra');
        $fracao = $fracaoAmostraId ? FracaoAmostra::with('amostra')->find($fracaoAmostraId) : null;

        return Inertia::render('Cra/ReceberAmostra/Rejeitar', [
            'ordem' => $ordem_servico,
            'fracao_amostra_id' => $fracaoAmostraId,
            'amostra_descricao' => $fracao->amostra->descricao ?? null,
        ]);
    }

    // Confirma a rejeição da amostra pelo CRA
    public function rejeitar(Request $request, OrdemServico $ordem_servico)
    {
        $validated = $request->validate([
            'data_rejeicao' => ['required', 'date'],
            'motivo' => ['required', 'string', 'max:255'],
            'fracao_amostra_id' => ['nullable', 'integer'],
        ]);

        $ordem_servico->update([
            'data_status_atual' => $validated['data_rejeicao'],
            'observacao' => $validated['motivo'],
            'recebedor_matricula' => auth()->user()?->empregado?->matricula,
        ]);

        $this->atualizarFracoes($ordem_servico, $validated['fracao_amostra_id'] ?? null, 'REJEITADO_CRA');

        return redirect()
            ->route('cra.receber-amostra.ordens', $ordem_servico->solicitacao_servico_id)
            ->with('success', 'Amostra rejeitada com sucesso!');
    }
}
