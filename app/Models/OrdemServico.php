<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
class OrdemServico extends Model
{
    use HasFactory;
    
    protected $table = 'ordem_servico';

    protected $primaryKey= 'ordem_servico_id';

    protected $fillable = [
        'status_atual',
        'data_status_atual',
        'observacao',
        'recebedor_matricula',
        'solicitacao_servico_id',
        'unidade_operacional_id',
    ];

    public function fracaoAmostra(): BelongsTo
    {
        return $this->belongsTo(FracaoAmostra::class);
    }

    public function fracoesAmostra(): HasMany
    {
        return $this->hasMany(FracaoAmostra::class, 'ordem_servico_id', 'ordem_servico_id');
    }

    public function laudo(): BelongsTo
    {
        return $this->belongsTo(Laudo::class);
    }

    public function fracao_amostra(): BelongsTo
    {
        return $this->belongsTo(Empregado::class);
    }

    public function solicitacaoServico()
    {
        //return $this->belongsTo(SolicitacaoServico::class);
        return $this->belongsTo(SolicitacaoServico::class, 'solicitacao_servico_id', 'solicitacao_servico_id');
    }
/*
    public function unidadeOperacional(): BelongsTo
    {
        return $this->belongsTo(UnidadeOperacional::class);
    }
*/

    // Get the Empregado associated with the Solicitação de Serviço.
    public function unidadeOperacional(): HasOne
    {
        return $this->hasOne(unidadeOperacional::class, 'unidade_operacional_id','unidade_operacional_id');
    }

    // Get the Empregado associated with the Solicitação de Serviço.
    public function empregado(): HasOne
    {
        return $this->hasOne(Empregado::class, 'matricula','solicitante_matricula');
    }

    public function perfilAcesso(): BelongsTo
    {
        return $this->belongsTo(PerfilAcesso::class);
    }

    public function execucoes_analises(): HasMany
    {
        return $this->hasMany(ExecucaoAnalise::class);
    }   
}
