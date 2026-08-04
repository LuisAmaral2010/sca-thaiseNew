<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Servico extends Model
{
    use HasFactory;
    
    protected $table = 'servico';

    protected $primaryKey= 'servico_id';

    protected $fillable = [
        'is_ativo',
        'data_cadastro',
        'descricao',
        'unidade_operacional_id',
        'tipo_servico',
    ];

    public function fracaAmostra(): BelongsTo
    {
        return $this->belongsTo(FracaoAmostra::class, 'unidade_operacional_id', 'unidade_operacional_id');
    }

    public function unidadeOperacional(): BelongsTo
    {
        return $this->belongsTo(UnidadeOperacional::class, 'unidade_operacional_id', 'unidade_operacional_id');
    }

    public function execucoes_analises(): HasMany
    {
        return $this->hasMany(ExecucaoAnalise::class);
    }     
}
