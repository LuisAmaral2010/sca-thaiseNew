<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Clientes externos (pessoas físicas e jurídicas) atendidos pelo CRA.
     */
    public function up(): void
    {
        Schema::create('cliente_externo', function (Blueprint $table) {
            $table->id('cliente_externo_id');

            // F = pessoa física, J = pessoa jurídica
            $table->char('tipo_pessoa', 1);
            // Nome completo (PF) ou razão social (PJ)
            $table->string('nome');
            // Só PJ
            $table->string('nome_fantasia')->nullable();
            // CPF (11 dígitos) ou CNPJ (14 dígitos), apenas números
            $table->string('cpf_cnpj', 14)->unique();
            // Só PF
            $table->string('rg', 20)->nullable();
            $table->date('data_nascimento')->nullable();
            // Só PJ
            $table->string('inscricao_estadual', 20)->nullable();
            $table->string('responsavel_nome')->nullable();

            // Contato
            $table->string('email')->nullable();
            $table->string('telefone', 20)->nullable();

            // Endereço
            $table->char('cep', 8)->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero', 20)->nullable();
            $table->string('complemento', 100)->nullable();
            $table->string('bairro', 100)->nullable();
            $table->string('cidade', 100)->nullable();
            $table->char('uf', 2)->nullable();

            $table->text('observacao')->nullable();
            $table->boolean('is_ativo')->default(true);

            $table->timestamps();

            $table->index('nome');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cliente_externo');
    }
};
