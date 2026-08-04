<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Historico extends Model
{
    use HasFactory;
    
    protected $table = 'historico';

    protected $primaryKey= 'historico_id';

    protected $fillable = [
        'escopo',
        'escopo_id',
        'status',
        'data',
        'usuario_matricula',
    ];

    public function fracao_amostra():BelongsTo
    {
        return $this->belongsTo(Empregado::class);
    }
}
