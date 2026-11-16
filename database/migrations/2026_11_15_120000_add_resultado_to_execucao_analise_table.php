<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Resultado de cada análise, preenchido pelo laboratório na tela
     * "Criar Laudo Online" (/laboratorio/criar-laudo-online).
     */
    public function up(): void
    {
        if (Schema::hasColumn('execucao_analise', 'resultado')) {
            return;
        }

        Schema::table('execucao_analise', function (Blueprint $table) {
            $table->string('resultado')->nullable()->after('servico_id');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('execucao_analise', 'resultado')) {
            return;
        }

        Schema::table('execucao_analise', function (Blueprint $table) {
            $table->dropColumn('resultado');
        });
    }
};
