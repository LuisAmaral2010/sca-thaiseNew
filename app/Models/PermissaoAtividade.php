<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermissaoAtividade extends Model
{
    use HasFactory;
    
    protected $table = 'permissao_atividade';

    protected $primaryKey= 'permissao_atividade_id';

    protected $fillable = [
        'data_permissao',
        'atividade_id',
        'usuario_matricula',
        'permissao_resultado',
        'permissao_todos_resultados',
        'created_at',
        'updated_at',
    ];

    public function atividade(): HasMany
    {
        return $this->hasMany(Atividade::class);
    }

    public function fracao_amostra():HasMany
    {
        return $this->hasMany(Empregado::class);
    }
}
