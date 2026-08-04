<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListaSolicitacaoServico extends Model
{
    // Nome da view/tabela no banco
    protected $table = 'listasolicitacaoservico';

    // Chave primária da view
    protected $primaryKey = 'solicitacao_servico_id';

    // Tipo de chave primária
    protected $keyType = 'int';

    // A view provavelmente não tem timestamps
    public $timestamps = false;

    // Caso queira permitir atribuição em massa (opcional)
    protected $guarded = [];
}