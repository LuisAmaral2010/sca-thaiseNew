<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Empregado extends Model
{
    use HasFactory;
    
    protected $table = 'empregado_view';

    protected $primaryKey= 'matricula';

    protected $fillable = [
        'empregado_id',
        'matricula',
        'nome',
        'login',
        'email',
    ];

    // Criar relacionamento um para muitos
    public function solicitacaoServico(): BelongsTo
    {
        return $this->belongsTo(SolicitacaoServico::class, 'solicitante_matricula', 'matricula');
    }

    // Get the user that owns the phone.
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'username', 'login');
    }
/*
    public function atividade()
    {
        return $this->belongsTo(Atividade::class, 'atividade_id', 'atividade_id');
    }
*/
    public function historico():HasMany
    {
        return $this->hasMany(Historico::class);
    }

    public function ordemServico():HasMany
    {
        return $this->hasMany(OrdemServico::class);
    }

    // Get the user that owns the Permissão Atividade.
    public function permissaoAtividade(): BelongsTo
    {
        return $this->belongsTo(PermissaoAtividade::class);
    }

     // Get the user that owns the Permissão Unidade Atendimento.
    public function permissaoUnidadeAtendimento(): BelongsTo
    {
        return $this->belongsTo(PermissaoUnidadeAtendimento::class);
    }   

    // Criar relacionamento um para muitos
    public function unidadeOperacional(): HasMany
    {
        return $this->hasMany(Atividade::class);
    }

        // Criar relacionamento um para muitos
    public function perfilAcesso(): HasMany
    {
        return $this->hasMany(PerfilAcesso::class);
    }

}
