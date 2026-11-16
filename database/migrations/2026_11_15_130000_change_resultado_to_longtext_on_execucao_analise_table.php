<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * O resultado passa a ser conteúdo rico (HTML com textos, tabelas e
     * imagens) editado na tela "Criar Laudo Online", que não cabe em
     * VARCHAR(255). SQL direto porque o projeto não usa doctrine/dbal.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE execucao_analise MODIFY resultado LONGTEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE execucao_analise MODIFY resultado VARCHAR(255) NULL');
    }
};
