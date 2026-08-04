<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ArquivoCRA extends Model
{
       use HasFactory;

    protected $table = 'arquivo_cra';

    protected $primaryKey= 'arquivo_cra_id';

    protected $fillable = [
        'content_type',
        'document_content',
        'nome',
        'tamanho',
        'aprovado_resp_tec',
        'data_apreciacao',
        'observacao',
        'audo_id',
    ];

    public function laudo():HasOne
    {
        return $this->hasOne(Laudo::class);
    }
}
