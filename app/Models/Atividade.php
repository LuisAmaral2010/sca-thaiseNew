<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Atividade extends Model
{
    use HasFactory;
    
    protected $table = 'atividade_view';

    protected $primaryKey= 'atividade_id';

    protected $fillable = [
        'plano_acao_id',
        'codigo',
        'titulo',
        'data_inicio',
        'data_fim',
        'matricula',
        'descricao',
        'status_atividade_id',
        'status_atividade_descricao',
    ];

    // Criar relacionamento um para muitos
    public function solicitacoesServicos()
    {
        return $this->hasMany(SolicitacaoServico::class, 'atividade_id', 'atividade_id');
    }

    // Get the user that owns the phone.
    public function permissaoAtividade(): BelongsTo
    {
        return $this->belongsTo(PermissaoAtividade::class);
    }
}
