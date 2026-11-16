<?php

namespace App\Http\Controllers;

use App\Models\ArquivoLaboratorio;
use App\Models\ExecucaoAnalise;
use App\Models\FracaoAmostra;
use App\Models\Laudo;
use App\Models\OrdemServico;
use App\Models\PermissaoUnidadeOperacional;
use App\Models\SolicitacaoServico;
use App\Models\UnidadeOperacional;
use App\Support\ResultadoLaudoHtml;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class LaboratorioCriarLaudoOnlineController extends Controller
{
    // Ordens de serviço cujos resultados podem ser preenchidos online:
    // já recebidas pelo laboratório, com ou sem prévia do laudo criada
    private const STATUS_EDITAVEIS = [
        'RECEBIDO PELO LABORATORIO',
        'PRÉVIA DO LAUDO CRIADA',
    ];

    // Imagens dos resultados: storage/app/laudos-online/{ordem_servico_id}/
    private const PASTA_IMAGENS = 'laudos-online';

    private const TAMANHO_MAXIMO_IMAGEM_KB = 5120;

    private const STATUS_AGUARDANDO_RESPTEC = 'LAUDO ESPERANDO APROVAÇÃO DO RESPTEC';

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

    private function autorizar(OrdemServico $ordem_servico): void
    {
        abort_unless(in_array($ordem_servico->unidade_operacional_id, $this->unidadesOperacionaisPermitidas()), 403);
    }

    // Tela de filtro (data inicial/final e unidade operacional) e listagem
    // das ordens de serviço disponíveis para preenchimento online do laudo
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
                ->whereIn('status_atual', self::STATUS_EDITAVEIS)
                ->where('unidade_operacional_id', $unidadeOperacionalId)
                ->when($validated['data_inicial'] ?? null, fn ($query, $data) => $query->whereDate('data_status_atual', '>=', $data))
                ->when($validated['data_final'] ?? null, fn ($query, $data) => $query->whereDate('data_status_atual', '<=', $data))
                ->orderByDesc('ordem_servico_id')
                ->get()
                ->map(function ($ordem) {
                    $execucoes = $ordem->fracoesAmostra
                        ->flatMap->execucoes_analises
                        ->reject(fn ($execucao) => (bool) $execucao->is_cancelado);

                    return [
                        'ordem_servico_id' => $ordem->ordem_servico_id,
                        'material' => $ordem->solicitacaoServico->descricao ?? null,
                        'servicos' => $execucoes
                            ->map(fn ($execucao) => $execucao->servico->descricao ?? null)
                            ->filter()
                            ->unique()
                            ->values()
                            ->implode(', ') ?: null,
                        'responsavel_atividade' => $ordem->solicitacaoServico->atividade->responsavel->nome ?? null,
                        'unidade_operacional' => $ordem->unidadeOperacional->nome ?? null,
                        'status' => $ordem->status_atual,
                        'data_aceite_laboratorio' => $ordem->data_aceite_laboratorio,
                        'total_analises' => $execucoes->count(),
                        'analises_preenchidas' => $execucoes->filter(fn ($execucao) => filled($execucao->resultado))->count(),
                    ];
                })
                ->values()
            : collect();

        return Inertia::render('Laboratorio/CriarLaudoOnline', [
            'unidadesOperacionais' => $unidadesOperacionais,
            'filtros' => $request->only(['data_inicial', 'data_final', 'unidade_operacional_id']),
            'ordens' => $ordens,
        ]);
    }

    // Formulário com os dados da ordem de serviço e um campo de resultado
    // para cada análise (execucao_analise) de cada amostra
    public function show(OrdemServico $ordem_servico)
    {
        $this->autorizar($ordem_servico);

        $ordem_servico->load([
            'solicitacaoServico.atividade.responsavel',
            'solicitacaoServico.empregado',
            'unidadeOperacional',
            'fracoesAmostra.amostra',
            'fracoesAmostra.execucoes_analises.servico',
        ]);

        $laudo = Laudo::where('ordem_servico_id', $ordem_servico->ordem_servico_id)
            ->first(['laudo_id', 'numero_laudo']);

        $numeroLaudo = $laudo?->numero_laudo;

        $temPdf = $laudo && ArquivoLaboratorio::where('laudo_id', $laudo->laudo_id)->exists();

        $amostras = $ordem_servico->fracoesAmostra
            ->groupBy('amostra_id')
            ->map(function ($fracoes) {
                $amostra = $fracoes->first()->amostra;

                return [
                    'amostra_id' => $amostra->amostra_id ?? null,
                    'descricao' => $amostra->descricao ?? null,
                    'numero_cra' => $amostra->numero_cra ?? null,
                    'analises' => $fracoes
                        ->flatMap->execucoes_analises
                        ->reject(fn ($execucao) => (bool) $execucao->is_cancelado)
                        ->map(fn ($execucao) => [
                            'execucao_analise_id' => $execucao->execucao_analise_id,
                            'servico' => $execucao->servico->descricao ?? null,
                            'resultado' => $execucao->resultado,
                        ])
                        ->values(),
                ];
            })
            ->values();

        return Inertia::render('Laboratorio/CriarLaudoOnlineShow', [
            'ordem' => [
                'ordem_servico_id' => $ordem_servico->ordem_servico_id,
                'numero_solicitacao_servico' => $ordem_servico->solicitacaoServico->numero_solicitacao_servico ?? null,
                'numero_laudo' => $numeroLaudo,
                'solicitante' => $ordem_servico->solicitacaoServico->empregado->nome ?? null,
                'atividade' => $ordem_servico->solicitacaoServico->atividade->titulo ?? null,
                'material' => $ordem_servico->solicitacaoServico->descricao ?? null,
                'unidade_operacional' => $ordem_servico->unidadeOperacional->nome ?? null,
                'status' => $ordem_servico->status_atual,
                'data_aceite_laboratorio' => $ordem_servico->data_aceite_laboratorio,
                'editavel' => in_array($ordem_servico->status_atual, self::STATUS_EDITAVEIS),
                'tem_pdf' => $temPdf,
            ],
            'amostras' => $amostras,
        ]);
    }

    // Grava o resultado de cada análise da ordem de serviço. Só aceita
    // execucao_analise pertencentes às frações desta ordem.
    public function gravar(Request $request, OrdemServico $ordem_servico)
    {
        $this->autorizar($ordem_servico);

        abort_unless(in_array($ordem_servico->status_atual, self::STATUS_EDITAVEIS), 403);

        $execucoesDaOrdem = ExecucaoAnalise::query()
            ->whereIn('fracao_amostra_id', $ordem_servico->fracoesAmostra()->pluck('fracao_amostra_id'))
            ->pluck('execucao_analise_id')
            ->all();

        $validated = $request->validate([
            'resultados' => ['required', 'array'],
            'resultados.*.execucao_analise_id' => ['required', 'integer', Rule::in($execucoesDaOrdem)],
            'resultados.*.resultado' => ['nullable', 'string', 'max:' . ResultadoLaudoHtml::TAMANHO_MAXIMO],
        ], [
            'resultados.*.resultado.max' => 'O conteúdo do resultado é grande demais.',
            'resultados.*.execucao_analise_id.in' => 'Análise inválida para esta ordem de serviço.',
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['resultados'] as $item) {
                ExecucaoAnalise::whereKey($item['execucao_analise_id'])
                    ->update(['resultado' => ResultadoLaudoHtml::sanitizar($item['resultado'] ?? null)]);
            }
        });

        return back()->with('success', "Resultados da ordem de serviço #{$ordem_servico->ordem_servico_id} gravados com sucesso.");
    }

    // Recebe uma imagem inserida no editor de resultados, guarda em
    // storage/app/laudos-online/{ordem}/ (fora da pasta pública) e devolve a
    // URL autenticada que o editor coloca no <img src>
    public function enviarImagem(Request $request, OrdemServico $ordem_servico)
    {
        $this->autorizar($ordem_servico);

        abort_unless(in_array($ordem_servico->status_atual, self::STATUS_EDITAVEIS), 403);

        $request->validate([
            'imagem' => ['required', 'file', 'mimes:png,jpg,jpeg,gif,webp', 'max:' . self::TAMANHO_MAXIMO_IMAGEM_KB],
        ], [
            'imagem.required' => 'Selecione uma imagem.',
            'imagem.uploaded' => 'Não foi possível enviar a imagem. Verifique se ela não excede o tamanho máximo permitido.',
            'imagem.mimes' => 'A imagem deve ser PNG, JPG, GIF ou WEBP.',
            'imagem.max' => 'A imagem deve ter no máximo ' . (self::TAMANHO_MAXIMO_IMAGEM_KB / 1024) . ' MB.',
        ]);

        $arquivo = $request->file('imagem');
        $nome = Str::uuid() . '.' . strtolower($arquivo->extension());

        Storage::disk('local')->putFileAs(
            self::PASTA_IMAGENS . '/' . $ordem_servico->ordem_servico_id,
            $arquivo,
            $nome
        );

        return response()->json([
            'url' => route('laboratorio.criar-laudo-online.imagem', [$ordem_servico->ordem_servico_id, $nome], false),
        ]);
    }

    // Exibe uma imagem de resultado, só para quem tem acesso à ordem
    public function exibirImagem(OrdemServico $ordem_servico, string $nome)
    {
        $this->autorizar($ordem_servico);

        $caminho = self::PASTA_IMAGENS . '/' . $ordem_servico->ordem_servico_id . '/' . $nome;

        abort_unless(Storage::disk('local')->exists($caminho), 404);

        return response()->file(Storage::disk('local')->path($caminho), [
            'Cache-Control' => 'private, max-age=86400',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    // Pré-visualização do PDF com os resultados gravados. Não gera número
    // de laudo nem muda status; mostra o número se o laudo já tiver um.
    public function previsualizarPdf(OrdemServico $ordem_servico)
    {
        $this->autorizar($ordem_servico);

        $numeroLaudo = Laudo::where('ordem_servico_id', $ordem_servico->ordem_servico_id)->value('numero_laudo');

        $conteudo = $this->gerarPdf($ordem_servico, $numeroLaudo, previa: true);

        return response($conteudo, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="previa_laudo_ordem_' . $ordem_servico->ordem_servico_id . '.pdf"',
        ]);
    }

    // Gera o PDF final do laudo online e o envia para aprovação do RespTec:
    // obtém (ou cria) o laudo com número RYY0009, grava o PDF em
    // arquivo_laboratorio (onde o RespTec o consulta, igual a "Gravar laudo")
    // e marca "LAUDO ESPERANDO APROVAÇÃO DO RESPTEC" no laudo, na ordem de
    // serviço (com histórico), nas frações e na solicitação.
    public function enviarRespTec(OrdemServico $ordem_servico)
    {
        $this->autorizar($ordem_servico);

        abort_unless(in_array($ordem_servico->status_atual, self::STATUS_EDITAVEIS), 403);

        $execucoes = ExecucaoAnalise::query()
            ->whereIn('fracao_amostra_id', $ordem_servico->fracoesAmostra()->pluck('fracao_amostra_id'))
            ->get()
            ->reject(fn ($execucao) => (bool) $execucao->is_cancelado);

        if ($execucoes->isEmpty()) {
            return back()->with('error', 'Esta ordem de serviço não tem análises para gerar o laudo.');
        }

        $pendentes = $execucoes->filter(fn ($execucao) => ResultadoLaudoHtml::vazio($execucao->resultado))->count();

        if ($pendentes > 0) {
            return back()->with('error', "Preencha e grave o resultado de todas as análises antes de gerar o laudo ({$pendentes} pendente(s)).");
        }

        $laudo = DB::transaction(function () use ($ordem_servico) {
            $now = now();

            $laudo = Laudo::where('ordem_servico_id', $ordem_servico->ordem_servico_id)
                ->lockForUpdate()
                ->first();

            if (!$laudo) {
                $laudo = Laudo::create([
                    'numero_laudo' => Laudo::gerarProximoNumero(),
                    'data_emissao' => $now,
                    'status_atual' => self::STATUS_AGUARDANDO_RESPTEC,
                    'ordem_servico_id' => $ordem_servico->ordem_servico_id,
                    'avaliador_matricula' => auth()->user()?->empregado?->matricula,
                ]);
            } elseif (empty($laudo->numero_laudo)) {
                $laudo->update(['numero_laudo' => Laudo::gerarProximoNumero()]);
            }

            $conteudo = $this->gerarPdf($ordem_servico, $laudo->numero_laudo, previa: false);

            ArquivoLaboratorio::updateOrCreate(
                ['laudo_id' => $laudo->laudo_id],
                [
                    'content_type' => 'application/pdf',
                    'document_content' => $conteudo,
                    'nome' => "{$laudo->numero_laudo}.pdf",
                    'tamanho' => strlen($conteudo),
                ]
            );

            $laudo->update(['status_atual' => self::STATUS_AGUARDANDO_RESPTEC]);

            if ($ordem_servico->status_atual !== self::STATUS_AGUARDANDO_RESPTEC) {
                $ordem_servico->update([
                    'status_atual' => self::STATUS_AGUARDANDO_RESPTEC,
                    'data_status_atual' => $now,
                ]);

                $ordem_servico->registrarHistorico();
            }

            FracaoAmostra::where('ordem_servico_id', $ordem_servico->ordem_servico_id)->update([
                'status_atual' => self::STATUS_AGUARDANDO_RESPTEC,
                'data_status_atual' => $now,
            ]);

            SolicitacaoServico::whereKey($ordem_servico->solicitacao_servico_id)
                ->update(['status' => self::STATUS_AGUARDANDO_RESPTEC]);

            return $laudo;
        });

        return back()->with('success', "Laudo {$laudo->numero_laudo} gerado em PDF e enviado para aprovação do RespTec.");
    }

    // PDF gerado do laudo já enviado (o mesmo que o RespTec vê)
    public function baixarPdf(OrdemServico $ordem_servico)
    {
        $this->autorizar($ordem_servico);

        $laudo = Laudo::where('ordem_servico_id', $ordem_servico->ordem_servico_id)->firstOrFail();
        $arquivo = ArquivoLaboratorio::where('laudo_id', $laudo->laudo_id)->firstOrFail();

        return response($arquivo->document_content, 200, [
            'Content-Type' => $arquivo->content_type ?: 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . ($arquivo->nome ?: "{$laudo->numero_laudo}.pdf") . '"',
        ]);
    }

    // Monta o Relatório de Análise (resources/views/pdf/laudo_online.blade.php)
    // com os dados da ordem e os resultados ricos de cada análise, e devolve
    // o conteúdo binário do PDF
    private function gerarPdf(OrdemServico $ordem_servico, ?string $numeroLaudo, bool $previa): string
    {
        $ordem_servico->load([
            'solicitacaoServico.atividade',
            'solicitacaoServico.empregado',
            'unidadeOperacional',
            'fracoesAmostra.amostra',
            'fracoesAmostra.execucoes_analises.servico',
        ]);

        $execucoes = $ordem_servico->fracoesAmostra
            ->flatMap->execucoes_analises
            ->reject(fn ($execucao) => (bool) $execucao->is_cancelado);

        $ultimaConclusao = $execucoes->pluck('data_conclusao')->filter()->max();

        $amostras = $ordem_servico->fracoesAmostra
            ->groupBy('amostra_id')
            ->map(function ($fracoes) use ($ordem_servico) {
                $amostra = $fracoes->first()->amostra;

                return [
                    'descricao' => $amostra->descricao ?? null,
                    'numero_cra' => $amostra->numero_cra ?? null,
                    'analises' => $fracoes
                        ->flatMap->execucoes_analises
                        ->reject(fn ($execucao) => (bool) $execucao->is_cancelado)
                        ->map(fn ($execucao) => [
                            'servico' => $execucao->servico->descricao ?? null,
                            'html' => $this->embutirImagens(
                                ResultadoLaudoHtml::sanitizar($execucao->resultado) ?? '',
                                $ordem_servico->ordem_servico_id
                            ),
                        ])
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();

        $solicitacao = $ordem_servico->solicitacaoServico;

        $pdf = Pdf::loadView('pdf.laudo_online', [
            'previa' => $previa,
            'numeroLaudo' => $numeroLaudo,
            'logo' => 'data:image/jpeg;base64,' . base64_encode(file_get_contents(resource_path('images/laudo_online_logo.jpg'))),
            'ordem' => [
                'solicitante' => $solicitacao->empregado->nome ?? $solicitacao->solicitante_externo_nome ?? null,
                'atividade' => $solicitacao->atividade->titulo ?? null,
                'material' => $solicitacao->descricao ?? null,
                'numero_solicitacao_servico' => $solicitacao->numero_solicitacao_servico ?? null,
                'data_entrada' => $ordem_servico->data_aceite_laboratorio
                    ? Carbon::parse($ordem_servico->data_aceite_laboratorio)->format('d/m/Y')
                    : null,
                'data_analise' => $ultimaConclusao ? Carbon::parse($ultimaConclusao)->format('d/m/Y') : null,
                'unidade_operacional' => $ordem_servico->unidadeOperacional->nome ?? null,
            ],
            'amostras' => $amostras,
            'respTec' => auth()->user()?->empregado?->nome,
            'dataExtenso' => now()->locale('pt_BR')->translatedFormat('j \d\e F \d\e Y'),
        ])->setPaper('a4');

        // "Página X de Y" no cabeçalho de todas as páginas
        $pdf->render();
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();
        $fonte = $dompdf->getFontMetrics()->getFont('DejaVu Sans');
        $canvas->page_text($canvas->get_width() - 170, 62, 'Página {PAGE_NUM} de {PAGE_COUNT}', $fonte, 9);

        return $pdf->output();
    }

    // O dompdf não acessa as URLs autenticadas das imagens: troca cada
    // <img src="/laboratorio/criar-laudo-online/{ordem}/imagens/{nome}"> pelo
    // conteúdo em base64. Imagens de outra ordem ou inexistentes são removidas.
    private function embutirImagens(string $html, int $ordemServicoId): string
    {
        return preg_replace_callback('#<img\b[^>]*>#i', function ($tag) use ($ordemServicoId) {
            $padrao = '#\bsrc="/laboratorio/criar-laudo-online/(\d+)/imagens/([A-Fa-f0-9\-]{36}\.(png|jpe?g|gif|webp))"#i';

            if (!preg_match($padrao, $tag[0], $m) || (int) $m[1] !== $ordemServicoId) {
                return '';
            }

            $caminho = self::PASTA_IMAGENS . '/' . $ordemServicoId . '/' . $m[2];

            if (!Storage::disk('local')->exists($caminho)) {
                return '';
            }

            $mime = Storage::disk('local')->mimeType($caminho) ?: 'image/png';
            $base64 = base64_encode(Storage::disk('local')->get($caminho));

            return str_replace($m[0], 'src="data:' . $mime . ';base64,' . $base64 . '"', $tag[0]);
        }, $html);
    }
}
