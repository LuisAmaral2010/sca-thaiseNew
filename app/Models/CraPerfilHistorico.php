<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Histórico de cadastro e edição do perfil do CRA (cra_perfil)
class CraPerfilHistorico extends Model
{
    public const ACAO_CRIACAO = 'CRIACAO';
    public const ACAO_ALTERACAO = 'ALTERACAO';

    public const UPDATED_AT = null;

    protected $table = 'cra_perfil_historico';

    protected $primaryKey = 'cra_perfil_historico_id';

    protected $fillable = [
        'cra_perfil_id',
        'acao',
        'alteracoes',
        'usuario_matricula',
        'usuario_nome',
        'ip',
    ];

    protected $casts = [
        'alteracoes' => 'array',
        'created_at' => 'datetime',
    ];

    public function craPerfil(): BelongsTo
    {
        return $this->belongsTo(CraPerfil::class, 'cra_perfil_id', 'cra_perfil_id');
    }
}
