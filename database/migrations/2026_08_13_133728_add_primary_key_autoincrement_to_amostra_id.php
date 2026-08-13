<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE amostra MODIFY amostra_id BIGINT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (amostra_id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE amostra DROP PRIMARY KEY, MODIFY amostra_id BIGINT NOT NULL');
    }
};
