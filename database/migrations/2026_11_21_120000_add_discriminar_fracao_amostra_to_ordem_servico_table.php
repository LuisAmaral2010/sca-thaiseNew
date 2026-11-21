<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Toggle "Discriminar Fração Amostra" do resumo do laudo online:
     * - ligado (padrão): um resultado por análise (execucao_analise.resultado)
     * - desligado: um único resultado para a ordem de serviço (resultado_unico)
     */
    public function up(): void
    {
        Schema::table('ordem_servico', function (Blueprint $table) {
            $table->boolean('discriminar_fracao_amostra')->default(true)->after('data_aceite_laboratorio');
            // HTML do editor (mesmo formato de execucao_analise.resultado)
            $table->longText('resultado_unico')->nullable()->after('discriminar_fracao_amostra');
        });
    }

    public function down(): void
    {
        Schema::table('ordem_servico', function (Blueprint $table) {
            $table->dropColumn(['discriminar_fracao_amostra', 'resultado_unico']);
        });
    }
};
