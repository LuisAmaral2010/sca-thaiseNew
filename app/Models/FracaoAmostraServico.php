<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Serviço (análise) de uma fração de amostra, com a indicação de se o laudo
// daquele serviço já foi criado
class FracaoAmostraServico extends Model
{
    use HasFactory;

    protected $table = 'fracao_amostra_servico';

    protected $primaryKey = 'fracao_amostra_servico_id';

    protected $fillable = [
        'fracao_amostra_id',
        'servico_id',
        'laudo_criado',
    ];

    protected $casts = [
        'laudo_criado' => 'boolean',
    ];

    public function fracaoAmostra(): BelongsTo
    {
        return $this->belongsTo(FracaoAmostra::class, 'fracao_amostra_id', 'fracao_amostra_id');
    }

    public function servico(): BelongsTo
    {
        return $this->belongsTo(Servico::class, 'servico_id', 'servico_id');
    }
}
