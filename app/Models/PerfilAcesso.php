<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerfilAcesso extends Model
{
    use HasFactory;
    
    protected $table = 'perfil_acesso';

    protected $primaryKey= 'perfil_acesso_id';

    protected $fillable = [
        'data_permissao',
        'tipo_perfil',
        'usuario_matricula',
    ];

    public function Empregado():BelongsTo
    {
        return $this->belongsTo(Empregado::class);
    }
}
