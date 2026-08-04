<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Laudo extends Model
{
    use HasFactory;
    
    protected $table = 'laudo';

    protected $primaryKey= 'laudo_id';

    protected $fillable = [
        'data_emissao',
        'data_laudo_cra',
        'data_laudo_lab',
        'status_atual',
        'ordem_servico_id',
        'avaliador_matricula',
    ];

    public function arquivoCra(): BelongsTo
    {
        return $this->belongsTo(ArquivoCRA::class);
    }

    public function arquivoLaboratorio(): BelongsTo
    {
        return $this->belongsTo(ArquivoLaboratorio::class);
    }

    public function ordemServico():HasMany
    {
        return $this->hasMany(OrdemServico::class);
    }
    
    public function execucoes_analises(): HasMany
    {
        return $this->hasMany(ExecucaoAnalise::class);
    }   
}
