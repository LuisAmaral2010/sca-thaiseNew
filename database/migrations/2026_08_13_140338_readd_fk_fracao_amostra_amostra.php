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
            $table->foreign('amostra_id', 'fk_fracao_amostra_amostra')
                ->references('amostra_id')->on('amostra');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fracao_amostra', function (Blueprint $table) {
            $table->dropForeign('fk_fracao_amostra_amostra');
        });
    }
};
