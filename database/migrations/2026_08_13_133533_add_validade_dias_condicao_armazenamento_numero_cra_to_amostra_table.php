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
        Schema::table('amostra', function (Blueprint $table) {
            $table->unsignedInteger('validade_dias')->nullable()->after('descricao');
            $table->string('condicao_armazenamento')->nullable()->after('validade_dias');
            $table->unsignedBigInteger('numero_cra')->nullable()->after('condicao_armazenamento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('amostra', function (Blueprint $table) {
            $table->dropColumn(['validade_dias', 'condicao_armazenamento', 'numero_cra']);
        });
    }
};
