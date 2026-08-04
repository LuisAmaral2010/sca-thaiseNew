<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExecucaoAnalise extends Model
{
    use HasFactory;
    
    protected $table = 'execucao_analise';

    protected $primaryKey= 'execucao_analise_id';

    protected $fillable = [
        'fracao_amostra_id',
        'laudo_id',
        'ordem_servico_id',
        'servico_id',
        'is_concluido',
        'data_conclusao',
        'is_cancelado',
        'data_cancelamento',
        'observacao',
        'created_at',
        'updated_at'
    ];        

    public function fracao_amostra()
    {
        return $this->belongsTo(FracaoAmostra::class);
    } 

    public function laudo()
    {
        return $this->belongsTo(Laudo::class);
    } 

    public function ordem_servico()
    {
        return $this->belongsTo(OrdemServico::class);
    }     

    public function servico()
    {
        return $this->belongsTo(Servico::class);
    }       
}


/*
1 execucao_analise_id bigint 
2 fracao_amostra_id bigint 
3 laudo_id bigint 
4 ordem_servico_id bigint 
5 servico_id bigint 
6 is_concluido bit(1) 
7 data_conclusao datetime 
8 is_cancelado bit(1) 
9 data_cancelamento datetime 
10 observacao varchar(60) 
11 created_at datetime 
12 updated_at datetime
*/