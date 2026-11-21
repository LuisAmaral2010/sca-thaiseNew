<?php

namespace App\Http\Controllers;

use App\Models\ArquivoLaboratorio;
use App\Models\Laudo;
use App\Models\OrdemServico;
use App\Models\SolicitacaoServico;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\HeaderUtils;

// Botão "Visualizar Laudo" da lista /solicitacoes_servicos: laudos (PDF) já
// aprovados pelo RespTec das ordens de serviço da solicitação
class SolicitacaoServicoLaudoController extends Controller
{
    public const STATUS_APROVADO_RESPTEC = 'LAUDO APROVADO PELO RESPTEC';

    // Um laudo aprovado: abre o PDF direto. Mais de um: lista para escolher.
    public function index(SolicitacaoServico $solicitacao_servico)
    {
        $laudos = $this->laudosAprovados($solicitacao_servico);

        if ($laudos->isEmpty()) {
            return redirect()
                ->route('solicitacoes_servicos.index')
                ->with('error', 'Esta solicitação ainda não tem laudo aprovado pelo RespTec com arquivo disponível.');
        }

        if ($laudos->count() === 1) {
            return redirect()->route('solicitacoes_servicos.laudos.pdf', [$solicitacao_servico, $laudos->first()]);
        }

        return view('solicitacoes_servicos.laudos', [
            'solicitacao' => $solicitacao_servico,
            'laudos' => $laudos,
        ]);
    }

    // PDF do laudo aberto no navegador; só laudos aprovados desta solicitação
    public function pdf(SolicitacaoServico $solicitacao_servico, Laudo $laudo)
    {
        $permitido = $this->laudosAprovados($solicitacao_servico)
            ->contains(fn (Laudo $l) => $l->laudo_id === $laudo->laudo_id);

        abort_unless($permitido, 404);

        $arquivo = ArquivoLaboratorio::where('laudo_id', $laudo->laudo_id)->firstOrFail();
        $nome = $arquivo->nome ?: "{$laudo->numero_laudo}.pdf";

        return response($arquivo->document_content, 200, [
            'Content-Type' => $arquivo->content_type ?: 'application/pdf',
            'Content-Disposition' => HeaderUtils::makeDisposition('inline', $nome, Str::ascii($nome)),
        ]);
    }

    // Laudos aprovados pelo RespTec (laudo e ordem de serviço) que têm arquivo
    private function laudosAprovados(SolicitacaoServico $solicitacao): Collection
    {
        $ordens = OrdemServico::where('solicitacao_servico_id', $solicitacao->solicitacao_servico_id)
            ->where('status_atual', self::STATUS_APROVADO_RESPTEC)
            ->pluck('ordem_servico_id');

        $comArquivo = ArquivoLaboratorio::whereNotNull('laudo_id')->select('laudo_id');

        return Laudo::whereIn('ordem_servico_id', $ordens)
            ->where('status_atual', self::STATUS_APROVADO_RESPTEC)
            ->whereIn('laudo_id', $comArquivo)
            ->orderBy('numero_laudo')
            ->get();
    }
}
