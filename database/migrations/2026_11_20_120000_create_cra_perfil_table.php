<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dados institucionais do CRA (registro único).
     */
    public function up(): void
    {
        Schema::create('cra_perfil', function (Blueprint $table) {
            $table->id('cra_perfil_id');

            // Identificação
            $table->string('nome');
            $table->string('sigla', 20)->nullable();
            // CNPJ apenas números
            $table->string('cnpj', 14)->nullable();

            // Cargos do responsável e do substituto; os nomes vêm da
            // unidade_operacional do CRA (CraPerfil::UNIDADE_OPERACIONAL_ID)
            $table->string('responsavel_cargo', 100)->nullable();
            $table->string('responsavel_substituto_cargo', 100)->nullable();

            // Contato
            $table->string('email')->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('site')->nullable();

            // Endereço
            $table->char('cep', 8)->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero', 20)->nullable();
            $table->string('complemento', 100)->nullable();
            $table->string('bairro', 100)->nullable();
            $table->string('cidade', 100)->nullable();
            $table->char('uf', 2)->nullable();

            // Atendimento
            $table->string('horario_atendimento')->nullable();
            $table->text('observacao')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cra_perfil');
    }
};
