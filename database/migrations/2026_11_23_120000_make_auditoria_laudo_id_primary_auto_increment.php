<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * auditoria_laudo.auditoria_laudo_id passa a ser chave primária com auto
     * incremento (antes: bigint nulo, sem chave). Exige que não haja ids
     * nulos nem repetidos na tabela.
     */
    public function up(): void
    {
        $nulos = DB::table('auditoria_laudo')->whereNull('auditoria_laudo_id')->count();
        $repetidos = DB::table('auditoria_laudo')
            ->select('auditoria_laudo_id')
            ->groupBy('auditoria_laudo_id')
            ->havingRaw('count(*) > 1')
            ->count();

        if ($nulos > 0 || $repetidos > 0) {
            throw new RuntimeException(
                "auditoria_laudo tem {$nulos} id(s) nulo(s) e {$repetidos} id(s) repetido(s); corrija antes de criar a chave primária."
            );
        }

        DB::statement('ALTER TABLE auditoria_laudo MODIFY auditoria_laudo_id BIGINT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (auditoria_laudo_id)');
    }

    public function down(): void
    {
        // Volta ao formato original: bigint nulo, sem chave primária
        DB::statement('ALTER TABLE auditoria_laudo MODIFY auditoria_laudo_id BIGINT NULL, DROP PRIMARY KEY');
    }
};
