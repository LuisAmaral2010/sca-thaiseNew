<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
class OrdemServico extends Model
{
    use HasFactory;
    
    protected $table = 'ordem_servico';

    protected $primaryKey= 'ordem_servico_id';

    protected $fillable = [
        'status_atual',
        'data_status_atual',
        'observacao',
        'recebedor_matricula',
        'solicitacao_servico_id',
        'unidade_operacional_id',
        'data_aceite_laboratorio',
        'discriminar_fracao_amostra',
        'resultado_unico',
    ];

    protected $casts = [
        'discriminar_fracao_amostra' => 'boolean',
    ];

    public function fracaoAmostra(): BelongsTo
    {
        return $this->belongsTo(FracaoAmostra::class);
    }

    public function fracoesAmostra(): HasMany
    {
        return $this->hasMany(FracaoAmostra::class, 'ordem_servico_id', 'ordem_servico_id');
    }

    public function laudo(): BelongsTo
    {
        return $this->belongsTo(Laudo::class);
    }

    public function fracao_amostra(): BelongsTo
    {
        return $this->belongsTo(Empregado::class);
    }

    public function solicitacaoServico()
    {
        //return $this->belongsTo(SolicitacaoServico::class);
        return $this->belongsTo(SolicitacaoServico::class, 'solicitacao_servico_id', 'solicitacao_servico_id');
    }
/*
    public function unidadeOperacional(): BelongsTo
    {
        return $this->belongsTo(UnidadeOperacional::class);
    }
*/

    // Get the Empregado associated with the Solicitação de Serviço.
    public function unidadeOperacional(): HasOne
    {
        return $this->hasOne(UnidadeOperacional::class, 'unidade_operacional_id','unidade_operacional_id');
    }

    // Get the Empregado associated with the Solicitação de Serviço.
    public function empregado(): HasOne
    {
        return $this->hasOne(Empregado::class, 'matricula','solicitante_matricula');
    }

    // Empregado que recebeu as amostras no CRA
    public function recebedor(): BelongsTo
    {
        return $this->belongsTo(Empregado::class, 'recebedor_matricula', 'matricula');
    }

    // Registra o status atual da ordem em ordem_servico_historico. A chave
    // primária da tabela não é auto-incremento, então o próximo id é obtido
    // com bloqueio pessimista dentro de uma transação (aninhada, quando já
    // houver uma em curso).
    public function registrarHistorico($dataStatus = null): void
    {
        DB::transaction(function () use ($dataStatus) {
            $proximoId = (int) DB::table('ordem_servico_historico')
                ->lockForUpdate()
                ->max('ordem_servico_historico_id') + 1;

            DB::table('ordem_servico_historico')->insert([
                'ordem_servico_historico_id' => $proximoId,
                'ordem_servico_id' => $this->ordem_servico_id,
                'status_atual' => $this->status_atual,
                'data_status_atual' => $dataStatus ?? $this->data_status_atual ?? now(),
            ]);
        });
    }

    public function perfilAcesso(): BelongsTo
    {
        return $this->belongsTo(PerfilAcesso::class);
    }

    public function execucoes_analises(): HasMany
    {
        return $this->hasMany(ExecucaoAnalise::class, 'ordem_servico_id', 'ordem_servico_id');
    }
}
