<?php

namespace App\Http\Controllers;

use App\Models\ArquivoLaboratorio;
use App\Models\FracaoAmostra;
use App\Models\Laudo;
use App\Models\OrdemServico;
use App\Models\PermissaoUnidadeOperacional;
use App\Models\SolicitacaoServico;
use App\Models\UnidadeOperacional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class LaboratorioGravarLaudoController extends Controller
{
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

    // Tela de filtro (data inicial/final e unidade operacional) e listagem
    // dos laudos já emitidos pelo laboratório, disponíveis para gravação
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

        $laudos = $unidadeOperacionalId
            ? Laudo::query()
                ->join('ordem_servico', 'ordem_servico.ordem_servico_id', '=', 'laudo.ordem_servico_id')
                ->leftJoin('unidade_operacional', 'unidade_operacional.unidade_operacional_id', '=', 'ordem_servico.unidade_operacional_id')
                ->where('ordem_servico.unidade_operacional_id', $unidadeOperacionalId)
                ->whereNotNull('laudo.numero_laudo')
                ->when($validated['data_inicial'] ?? null, fn ($query, $data) => $query->whereDate('laudo.data_emissao', '>=', $data))
                ->when($validated['data_final'] ?? null, fn ($query, $data) => $query->whereDate('laudo.data_emissao', '<=', $data))
                ->orderByDesc('laudo.numero_laudo')
                ->get([
                    'laudo.laudo_id',
                    'laudo.numero_laudo',
                    'laudo.data_emissao',
                    'laudo.status_atual',
                    'laudo.ordem_servico_id',
                    'unidade_operacional.nome as unidade_operacional',
                ])
            : collect();

        // Nome do PDF já gravado para cada laudo, se houver
        $arquivos = ArquivoLaboratorio::whereIn('laudo_id', $laudos->pluck('laudo_id'))
            ->pluck('nome', 'laudo_id');

        $laudos->each(fn ($laudo) => $laudo->arquivo_nome = $arquivos[$laudo->laudo_id] ?? null);

        return Inertia::render('Laboratorio/GravarLaudo', [
            'unidadesOperacionais' => $unidadesOperacionais,
            'filtros' => $request->only(['data_inicial', 'data_final', 'unidade_operacional_id']),
            'laudos' => $laudos,
        ]);
    }

    // Grava o PDF escolhido pelo usuário na tabela arquivo_laboratorio,
    // substituindo o arquivo anterior do laudo, se existir, e marca com
    // "LAUDO ESPERANDO APROVAÇÃO DO RESPTEC" o laudo, a ordem de serviço (com
    // registro em ordem_servico_historico), suas frações e a solicitação
    public function gravar(Request $request, Laudo $laudo)
    {
        $unidadeOperacionalId = OrdemServico::whereKey($laudo->ordem_servico_id)->value('unidade_operacional_id');

        abort_unless(in_array($unidadeOperacionalId, $this->unidadesOperacionaisPermitidas()), 403);

        $request->validate([
            'arquivo' => ['required', 'file', 'mimes:pdf'],
        ], [
            'arquivo.required' => 'Selecione um arquivo PDF.',
            'arquivo.uploaded' => 'Não foi possível enviar o arquivo. Verifique se ele não excede o tamanho máximo permitido.',
            'arquivo.mimes' => 'O arquivo deve ser um PDF.',
        ]);

        $arquivo = $request->file('arquivo');

        // O nome do PDF deve ser o número do laudo (ex.: R260002.pdf), sem
        // diferenciar maiúsculas de minúsculas
        $nomeSemExtensao = pathinfo($arquivo->getClientOriginalName(), PATHINFO_FILENAME);

        if (strcasecmp($nomeSemExtensao, (string) $laudo->numero_laudo) !== 0) {
            throw ValidationException::withMessages([
                'arquivo' => "O arquivo deve se chamar {$laudo->numero_laudo}.pdf (arquivo selecionado: {$arquivo->getClientOriginalName()}).",
            ]);
        }

        DB::transaction(function () use ($laudo, $arquivo) {
            $now = now();

            ArquivoLaboratorio::updateOrCreate(
                ['laudo_id' => $laudo->laudo_id],
                [
                    'content_type' => $arquivo->getMimeType(),
                    'document_content' => $arquivo->get(),
                    'nome' => Str::limit($arquivo->getClientOriginalName(), 50, ''),
                    'tamanho' => $arquivo->getSize(),
                ]
            );

            $laudo->update(['status_atual' => self::STATUS_AGUARDANDO_RESPTEC]);

            // Ordem de serviço: só muda (e ganha linha no histórico) no
            // primeiro envio; regravar o PDF não duplica o histórico
            $ordem = OrdemServico::findOrFail($laudo->ordem_servico_id);

            if ($ordem->status_atual !== self::STATUS_AGUARDANDO_RESPTEC) {
                $ordem->update([
                    'status_atual' => self::STATUS_AGUARDANDO_RESPTEC,
                    'data_status_atual' => $now,
                ]);

                $ordem->registrarHistorico();
            }

            FracaoAmostra::where('ordem_servico_id', $laudo->ordem_servico_id)->update([
                'status_atual' => self::STATUS_AGUARDANDO_RESPTEC,
                'data_status_atual' => $now,
            ]);

            SolicitacaoServico::whereKey($ordem->solicitacao_servico_id)
                ->update(['status' => self::STATUS_AGUARDANDO_RESPTEC]);
        });

        return back()->with('success', "PDF do laudo {$laudo->numero_laudo} gravado com sucesso.");
    }
}
