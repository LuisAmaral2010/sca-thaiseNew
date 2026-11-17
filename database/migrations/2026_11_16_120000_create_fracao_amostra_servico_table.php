<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Serviços (análises) de cada fração de amostra, com a indicação de se o
     * laudo daquele serviço já foi criado.
     */
    public function up(): void
    {
        Schema::create('fracao_amostra_servico', function (Blueprint $table) {
            $table->id('fracao_amostra_servico_id');

            // bigint com sinal, igual às chaves de fracao_amostra e servico
            // (foreignId() criaria unsigned e a chave estrangeira falharia)
            $table->bigInteger('fracao_amostra_id');
            $table->bigInteger('servico_id');

            $table->boolean('laudo_criado')->default(false);

            $table->timestamps();

            $table->foreign('fracao_amostra_id')
                ->references('fracao_amostra_id')->on('fracao_amostra');
            $table->foreign('servico_id')
                ->references('servico_id')->on('servico');

            $table->unique(['fracao_amostra_id', 'servico_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fracao_amostra_servico');
    }
};
