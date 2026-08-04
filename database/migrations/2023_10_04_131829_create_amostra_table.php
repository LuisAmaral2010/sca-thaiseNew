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
        Schema::create('amostra', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('data');
            $table->date('dt_recebido_cra');
            $table->string('st_descricao', 250);
            $table->string('st_numero_cra', 5);
            $table->bigInteger('solicitacao_id');
          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amostra');
    }
};
