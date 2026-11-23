<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Auditoria (versões) de laudos: tabela auditoria_laudo do banco sca.
// auditoria_laudo_id é a chave primária com auto incremento (gerada pelo
// banco na gravação).
class AuditoriaLaudo extends Model
{
    protected $table = 'auditoria_laudo';

    protected $primaryKey = 'auditoria_laudo_id';

    protected $fillable = [
        'versao',
        'tipo_revisao',
        'data_emissao',
        'data_laudo_cra',
        'data_laudo_laboratorio',
        'numero_controle',
        'status',
        'avaliador_matricula',
        'ordem_servico_id',
    ];

    protected $casts = [
        'auditoria_laudo_id' => 'integer',
        'versao' => 'integer',
        'tipo_revisao' => 'integer',
        'data_emissao' => 'datetime',
        'data_laudo_cra' => 'datetime',
        'data_laudo_laboratorio' => 'datetime',
        'avaliador_matricula' => 'integer',
        'ordem_servico_id' => 'integer',
    ];

    public function ordemServico(): BelongsTo
    {
        return $this->belongsTo(OrdemServico::class, 'ordem_servico_id', 'ordem_servico_id');
    }

    // Empregado avaliador (empregado_view.matricula)
    public function avaliador(): BelongsTo
    {
        return $this->belongsTo(Empregado::class, 'avaliador_matricula', 'matricula');
    }
}
