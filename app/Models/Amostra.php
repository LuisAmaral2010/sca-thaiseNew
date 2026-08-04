<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Amostra extends Model
{
    use HasFactory;

    protected $table = 'amostra';

    protected $primaryKey= 'amostra_id';

    protected $fillable = [
        'amostra_id',
        'descricao',
        'solicitacao_id',
        'validade_dias',
        'condicao_armazenamento',
        'numero_cra',
    ];
/*
    public function solicitacaoServico(): BelongsTo
    {
        return $this->belongsTo(solicitacaoServico::class);
    }
*/

    public function solicitacaoServico()
    {
        return $this->belongsTo(SolicitacaoServico::class, 'solicitacao_id', 'solicitacao_servico_id');
    }

    // Criar relacionamento um para muitos
    public function fracaoAmostra(): HasMany
    {
        return $this->hasMany(FracaoAmostra::class);
    }
}
	

