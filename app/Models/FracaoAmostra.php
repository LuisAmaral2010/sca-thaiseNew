<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FracaoAmostra extends Model
{
    use HasFactory;
    
    protected $table = 'fracao_amostra';

    protected $primaryKey= 'fracao_amostra_id';

    protected $fillable = [
        'data_recebido_cra',
        'numero',
        'observacao',
        'observacao_cra',
        'recebido_cra',
        'amostra_id',
        'status_atual',
        'data_status_atual',
        'servico_id',
        'ordem_servico_id',
        'responsavel_execucao_matricula',
        'created_at',
        'updated_at',
    ];
 
    public function amostra(): BelongsTo
    {
        return $this->belongsTo(Amostra::class);
    }

    // Get the phone associated with the user.
    public function ordemServico(): HasOne
    {
        return $this->hasOne(OrdemServico::class);
    }

    // Get the phone associated with the user.
    public function servico(): HasOne
    {
        return $this->hasOne(Servico::class);
    }

    public function execucoes_analises(): HasMany
    {
        return $this->hasMany(ExecucaoAnalise::class);
    }    
}
