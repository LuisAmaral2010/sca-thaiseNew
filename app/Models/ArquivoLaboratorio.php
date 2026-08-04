<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ArquivoLaboratorio extends Model
{

    protected $table = 'arquivo_laboratorio';

    protected $primaryKey= 'arquivo_laboratorio_id';

    protected $fillable = [
        'content_type',
        'document_content',
        'nome',
        'tamanho',
        'aprovado_resp_tec',
        'data_apreciacao',
        'observacao',
        'laudo_id',
    ];

    public function laudo():HasOne
    {
        return $this->hasOne(Laudo::class);
    }
}
