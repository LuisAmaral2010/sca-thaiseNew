<?php

namespace App\Mail;

use App\Models\SolicitacaoServico;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// Aviso ao CRA (e-mail de cra_perfil) de que um solicitante enviou uma
// solicitação de serviço
class SolicitacaoEnviadaCraMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SolicitacaoServico $solicitacao)
    {
    }

    public function envelope(): Envelope
    {
        $numero = $this->solicitacao->numero_solicitacao_servico;

        return new Envelope(
            subject: 'Nova solicitação de serviço enviada ao CRA' . ($numero ? " — nº {$numero}" : ''),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.solicitacao_enviada_cra',
            with: [
                'solicitante' => $this->solicitacao->empregado?->nome
                    ?? $this->solicitacao->solicitante_matricula
                    ?? 'Solicitante não identificado',
                'numero' => $this->solicitacao->numero_solicitacao_servico,
                'descricao' => $this->solicitacao->descricao,
                'data' => now()->format('d/m/Y H:i'),
                'url' => route('cra.receber-amostra.ordens', $this->solicitacao),
            ],
        );
    }
}
