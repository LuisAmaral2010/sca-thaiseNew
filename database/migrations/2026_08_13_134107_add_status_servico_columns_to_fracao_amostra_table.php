<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fracao_amostra', function (Blueprint $table) {
            $table->string('status_atual')->nullable()->after('amostra_id');
            $table->dateTime('data_status_atual')->nullable()->after('status_atual');
            $table->unsignedBigInteger('servico_id')->nullable()->after('data_status_atual');
            $table->unsignedBigInteger('ordem_servico_id')->nullable()->after('servico_id');
            $table->string('responsavel_execucao_matricula', 6)->nullable()->after('ordem_servico_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fracao_amostra', function (Blueprint $table) {
            $table->dropColumn([
                'status_atual',
                'data_status_atual',
                'servico_id',
                'ordem_servico_id',
                'responsavel_execucao_matricula',
            ]);
        });
    }
};
