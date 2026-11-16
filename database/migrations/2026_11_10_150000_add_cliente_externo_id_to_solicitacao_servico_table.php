<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Liga a requisição externa ao cliente externo cadastrado. O nome continua
     * gravado em solicitante_externo_nome (como estava no momento da requisição).
     */
    public function up(): void
    {
        Schema::table('solicitacao_servico', function (Blueprint $table) {
            $table->foreignId('cliente_externo_id')
                ->nullable()
                ->after('solicitante_externo_nome')
                ->constrained('cliente_externo', 'cliente_externo_id')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('solicitacao_servico', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cliente_externo_id');
        });
    }
};
