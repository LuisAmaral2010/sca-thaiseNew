<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SolicitacaoServico extends Model
{
    use HasFactory;

    protected $table = 'solicitacao_servico';

    protected $primaryKey= 'solicitacao_servico_id';

    protected $fillable = [			
        'descricao',
        'data_solicitacao',
        'atividade_id',	
        'solicitante_matricula',
        'created_at',
        'update_at',
    ];
    
    public function atividade()
    {
        return $this->belongsTo(Atividade::class, 'atividade_id', 'atividade_id');
    }

    // Criar relacionamento um para muitos
    /*
    public function amostra():HasMany
    {
        return $this->hasMany(Amostra::class);
    }
    */
    
    // Criar relacionamento um para muitos
    public function amostras():HasMany
    {
        return $this->hasMany(Amostra::class, 'solicitacao_id', 'solicitacao_servico_id');
    }

    public function ordemServico():HasMany
    {
        //return $this->hasMany(OrdemServico::class);
        return $this->hasMany(OrdemServico::class, 'solicitacao_servico_id', 'solicitacao_servico_id');
    }

    // Get the Empregado associated with the Solicitação de Serviço.
    public function empregado(): HasOne
    {
        return $this->hasOne(Empregado::class, 'matricula','solicitante_matricula');
    }
}
