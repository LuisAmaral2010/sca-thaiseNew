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
        'numero_laudo',
        'data_emissao',
        'data_laudo_cra',
        'data_laudo_lab',
        'status_atual',
        'ordem_servico_id',
        'avaliador_matricula',
    ];

    // Próximo número de laudo no formato RYY0009 (R + ano de 2 dígitos +
    // contador de 4 dígitos, reiniciado a cada virada de ano). Deve ser
    // chamado dentro de DB::transaction: o bloqueio pessimista evita que duas
    // emissões simultâneas gerem o mesmo número.
    public static function gerarProximoNumero(): string
    {
        $prefixo = 'R' . now()->format('y');

        $ultimoNumero = static::where('numero_laudo', 'like', $prefixo . '%')
            ->lockForUpdate()
            ->orderByDesc('numero_laudo')
            ->value('numero_laudo');

        $contador = $ultimoNumero ? ((int) substr($ultimoNumero, 3)) + 1 : 1;

        return $prefixo . str_pad((string) $contador, 4, '0', STR_PAD_LEFT);
    }

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
