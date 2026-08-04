<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnidadeOperacional extends Model
{
    use HasFactory;
    
    protected $table = 'unidade_operacional';

    protected $primaryKey= 'unidade_operacional_id';

    protected $fillable = [
        'is_ativo',
        'nome',
        'responsavel_matricula',
        'responsavel_substituto_matricula',
    ];

    public function ordemServico():HasMany
    {
        return $this->hasMany(OrdemServico::class);
    }

    // Get the user that owns the Permissão Unidade Atendimento.
    public function permissaoUnidadeAtendimento(): BelongsTo
    {
        return $this->belongsTo(PermissaoUnidadeAtendimento::class);
    }   

    public function servico():HasMany
    {
        return $this->hasMany(Servico::class, 'unidade_operacional_id', 'unidade_operacional_id');
    }

    public function fracao_amostra():BelongsTo
    {
        return $this->belongsTo(Empregado::class);
    }
}
