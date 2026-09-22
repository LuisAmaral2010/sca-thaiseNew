<?php

namespace App\Http\Controllers;

use App\Models\Laudo;
use App\Models\OrdemServico;
use App\Models\PermissaoUnidadeOperacional;
use App\Models\UnidadeOperacional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use PhpOffice\PhpWord\TemplateProcessor;

class LaboratorioEmitirLaudoController extends Controller
{
    // Unidades operacionais em que o usuário logado tem permissão de
    // laboratório (permissao_unidade_operacional.usuario_matricula)
    private function unidadesOperacionaisPermitidas(): array
    {
        $matricula = auth()->user()?->empregado?->matricula;

        if (!$matricula) {
            return [];
        }

        return PermissaoUnidadeOperacional::whereNotNull('unidade_operacional_id')
            ->where('usuario_matricula', $matricula)
            ->pluck('unidade_operacional_id')
            ->all();
    }

    // Tela de filtro (data inicial/final e unidade operacional) e listagem
    // das ordens de serviço já recebidas pelo laboratório, disponíveis para
    // emissão de laudo
    public function index(Request $request)
    {
        $validated = $request->validate([
            'data_inicial' => ['nullable', 'date'],
            'data_final' => ['nullable', 'date'],
            'unidade_operacional_id' => ['nullable', 'integer'],
        ]);

        $unidadesPermitidas = $this->unidadesOperacionaisPermitidas();

        $unidadesOperacionais = UnidadeOperacional::whereIn('unidade_operacional_id', $unidadesPermitidas)
            ->orderBy('nome')
            ->get(['unidade_operacional_id', 'nome']);

        $unidadeOperacionalId = $validated['unidade_operacional_id'] ?? null;

        abort_if($unidadeOperacionalId && !in_array($unidadeOperacionalId, $unidadesPermitidas), 403);

        $ordens = $unidadeOperacionalId
            ? OrdemServico::query()
                ->with([
                    'solicitacaoServico.atividade.responsavel',
                    'unidadeOperacional',
                    'fracoesAmostra.execucoes_analises.servico',
                ])
                ->where('status_atual', 'RECEBIDO PELO LABORATORIO')
                ->where('unidade_operacional_id', $unidadeOperacionalId)
                ->when($validated['data_inicial'] ?? null, fn ($query, $data) => $query->whereDate('data_status_atual', '>=', $data))
                ->when($validated['data_final'] ?? null, fn ($query, $data) => $query->whereDate('data_status_atual', '<=', $data))
                ->orderByDesc('ordem_servico_id')
                ->get()
                ->map(fn ($ordem) => [
                    'ordem_servico_id' => $ordem->ordem_servico_id,
                    'material' => $ordem->solicitacaoServico->descricao ?? null,
                    'servicos' => $ordem->fracoesAmostra
                        ->flatMap(fn ($fracao) => $fracao->execucoes_analises->map(fn ($execucao) => $execucao->servico->descricao ?? null))
                        ->filter()
                        ->unique()
                        ->values()
                        ->implode(', ') ?: null,
                    'responsavel_atividade' => $ordem->solicitacaoServico->atividade->responsavel->nome ?? null,
                    'unidade_operacional' => $ordem->unidadeOperacional->nome ?? null,
                    'status' => $ordem->status_atual,
                    'data_aceite_laboratorio' => $ordem->data_aceite_laboratorio,
                ])
                ->values()
            : collect();

        return Inertia::render('Laboratorio/EmitirLaudo', [
            'unidadesOperacionais' => $unidadesOperacionais,
            'filtros' => $request->only(['data_inicial', 'data_final', 'unidade_operacional_id']),
            'ordens' => $ordens,
        ]);
    }

    // Gera a mala direta do laudo (Modelo_laudo.docx) preenchida com os dados
    // da ordem de serviço e devolve o arquivo .docx gerado para download.
    // A tabela "RESULTADOS OBTIDOS" (bloco ${bloco_resultado}...${/bloco_resultado}
    // no template) é clonada uma vez para cada amostra desta ordem de serviço.
    public function emitir(OrdemServico $ordem_servico)
    {
        abort_unless(in_array($ordem_servico->unidade_operacional_id, $this->unidadesOperacionaisPermitidas()), 403);

        $ordem_servico->load([
            'solicitacaoServico.atividade.responsavel',
            'solicitacaoServico.empregado',
            'unidadeOperacional',
            'fracoesAmostra.amostra',
            'fracoesAmostra.execucoes_analises.servico',
        ]);

        $laudo = $this->obterOuCriarLaudo($ordem_servico);

        $templateProcessor = new TemplateProcessor(storage_path('app/templates/Modelo_laudo.docx'));

        $templateProcessor->setValue('solicitante.nome', $ordem_servico->solicitacaoServico->empregado->nome ?? '');
        $templateProcessor->setValue('atividade.titulo', $ordem_servico->solicitacaoServico->atividade->titulo ?? '');
        $templateProcessor->setValue('solicitacao.descricao', $ordem_servico->solicitacaoServico->descricao ?? '');
        $templateProcessor->setValue('numero_solicitacao_servico', $ordem_servico->solicitacaoServico->numero_solicitacao_servico ?? '');
        $templateProcessor->setValue('data_aceite_laboratorio', $ordem_servico->data_aceite_laboratorio
            ? \Carbon\Carbon::parse($ordem_servico->data_aceite_laboratorio)->format('d/m/Y')
            : '');
        $templateProcessor->setValue('resp_tec.nome', auth()->user()?->empregado?->nome ?? '');
        $templateProcessor->setValue('unidade_operacional.nome', $ordem_servico->unidadeOperacional->nome ?? '');
        $templateProcessor->setValue('numero_laudo', $laudo->numero_laudo ?? '');

        $this->preencherResultadosPorAmostra($templateProcessor, $ordem_servico);

        $tempDir = storage_path('app/temp');

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $tempPath = $tempDir . '/' . $laudo->numero_laudo . '_' . uniqid() . '.docx';

        $templateProcessor->saveAs($tempPath);

        return response()
            ->download($tempPath, "{$laudo->numero_laudo}.docx")
            ->deleteFileAfterSend(true);
    }

    // Clona a tabela "RESULTADOS OBTIDOS" (bloco ${bloco_resultado} do
    // template) uma vez para cada amostra distinta desta ordem de serviço,
    // preenchendo identificação, número de CRA e as análises realizadas.
    // Sem resultado numérico registrado no sistema ainda, o campo
    // ${resultado} é deixado em branco.
    private function preencherResultadosPorAmostra(TemplateProcessor $templateProcessor, OrdemServico $ordem_servico): void
    {
        $fracoesPorAmostra = $ordem_servico->fracoesAmostra
            ->filter(fn ($fracao) => $fracao->amostra)
            ->groupBy('amostra_id');

        $totalAmostras = $fracoesPorAmostra->count();

        $templateProcessor->cloneBlock('bloco_resultado', $totalAmostras, true, true);

        $indice = 1;

        foreach ($fracoesPorAmostra as $fracoes) {
            $amostra = $fracoes->first()->amostra;

            $analises = $fracoes
                ->flatMap(fn ($fracao) => $fracao->execucoes_analises->pluck('servico.descricao'))
                ->filter()
                ->unique()
                ->implode(', ');

            $templateProcessor->setValue("amostra.descricao#{$indice}", $amostra->descricao ?? '');
            $templateProcessor->setValue("numero_cra#{$indice}", $amostra->numero_cra ?? '');
            $templateProcessor->setValue("analise#{$indice}", $analises);
            $templateProcessor->setValue("resultado#{$indice}", '');

            $indice++;
        }
    }

    // Reaproveita o laudo já emitido para esta ordem de serviço (idempotente:
    // reemitir o DOC não gera outro número), ou cria um novo com o próximo
    // número no formato RYY0009 (R + ano de 2 dígitos + contador de 4
    // dígitos, reiniciado a cada virada de ano). O bloqueio pessimista evita
    // que duas emissões simultâneas gerem o mesmo número.
    private function obterOuCriarLaudo(OrdemServico $ordem_servico): Laudo
    {
        return DB::transaction(function () use ($ordem_servico) {
            $laudo = Laudo::where('ordem_servico_id', $ordem_servico->ordem_servico_id)
                ->lockForUpdate()
                ->first();

            if ($laudo && !empty($laudo->numero_laudo)) {
                return $laudo;
            }

            $prefixo = 'R' . now()->format('y');

            $ultimoNumero = Laudo::where('numero_laudo', 'like', $prefixo . '%')
                ->lockForUpdate()
                ->orderByDesc('numero_laudo')
                ->value('numero_laudo');

            $contador = $ultimoNumero ? ((int) substr($ultimoNumero, 3)) + 1 : 1;

            $numeroLaudo = $prefixo . str_pad((string) $contador, 4, '0', STR_PAD_LEFT);

            if ($laudo) {
                $laudo->update(['numero_laudo' => $numeroLaudo]);

                return $laudo;
            }

            return Laudo::create([
                'numero_laudo' => $numeroLaudo,
                'data_emissao' => now(),
                'status_atual' => 'EMITIDO',
                'ordem_servico_id' => $ordem_servico->ordem_servico_id,
                'avaliador_matricula' => auth()->user()?->empregado?->matricula,
            ]);
        });
    }
}
