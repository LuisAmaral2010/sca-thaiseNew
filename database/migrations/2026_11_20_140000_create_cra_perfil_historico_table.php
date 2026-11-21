<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Histórico de alterações do perfil do CRA (cra_perfil): uma linha por
     * cadastro ou edição, com os campos alterados (valor anterior e novo).
     */
    public function up(): void
    {
        Schema::create('cra_perfil_historico', function (Blueprint $table) {
            $table->id('cra_perfil_historico_id');

            // Fica nulo quando o registro do CRA é excluído (o histórico é mantido)
            $table->foreignId('cra_perfil_id')
                ->nullable()
                ->constrained('cra_perfil', 'cra_perfil_id')
                ->nullOnDelete();

            // CRIACAO ou ALTERACAO
            $table->string('acao', 20);

            // [{ "campo": "...", "anterior": ..., "novo": ... }, ...]
            $table->json('alteracoes');

            // Quem alterou (nome guardado para não depender do cadastro de empregados)
            $table->string('usuario_matricula', 6)->nullable();
            $table->string('usuario_nome')->nullable();
            $table->string('ip', 45)->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['cra_perfil_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cra_perfil_historico');
    }
};
