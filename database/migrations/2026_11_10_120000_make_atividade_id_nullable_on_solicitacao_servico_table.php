<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Requisições externas (cadastradas pelo CRA em
     * /cra/solicitacoes_servicos_externos/create) não têm atividade.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE solicitacao_servico MODIFY atividade_id BIGINT NULL');
    }

    /**
     * Só reverte se não houver solicitações sem atividade gravadas.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE solicitacao_servico MODIFY atividade_id BIGINT NOT NULL');
    }
};
