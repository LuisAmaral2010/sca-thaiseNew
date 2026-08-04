<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermissaoUnidadeOperacional extends Model
{
    use HasFactory;
    
    protected $table = 'permissao_unidade_operacional';

    protected $primaryKey= 'permissao_unidade_operacional_id';

    protected $fillable = [
        'data_permissao',
        'unidade_operacional_id',
        'usuario_matricula',
    ];

    public function fracao_amostra():HasMany
    {
        return $this->hasMany(Empregado::class);
    }

        public function unidadeOperacional():HasMany
    {
        return $this->hasMany(UnidadeOperacional::class);
    }
}
