<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Nome do solicitante das requisições externas
     * (cadastradas pelo CRA em /cra/solicitacoes_servicos_externos/create).
     */
    public function up(): void
    {
        Schema::table('solicitacao_servico', function (Blueprint $table) {
            $table->string('solicitante_externo_nome')->nullable()->after('solicitante_matricula');
        });
    }

    public function down(): void
    {
        Schema::table('solicitacao_servico', function (Blueprint $table) {
            $table->dropColumn('solicitante_externo_nome');
        });
    }
};
