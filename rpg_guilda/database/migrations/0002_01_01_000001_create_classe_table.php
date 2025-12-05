<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id(); // Chave primária

            $table->string('nome', 100); // Nome da classe
            $table->unsignedBigInteger('sistema_id'); // FK para sistema
            $table->foreign('sistema_id')->references('id')->on('sistemas')->onDelete('cascade');

            $table->text('descricao')->nullable(); // Descrição da classe
            $table->string('dado_vida', 5)->nullable() // Dado de vida da classe
                  ->comment('Dado de vida da classe (ex: d8), usado na fórmula de PV do Sistema.');

            $table->json('pericias_iniciais')->nullable() // Perícias iniciais em JSON
                  ->comment('JSON com tipo de bônus, quantidade e lista de perícias');

            $table->json('equipamento_inicial')->nullable() // Equipamento inicial em JSON
                  ->comment('Lista de itens e opções de equipamento inicial (JSON)');

            $table->boolean('usa_magia')->default(false); // Se a classe usa magia
            $table->json('atributos_bonus')->nullable(); // Atributos bônus em JSON
            $table->json('poderes')->nullable(); // Poderes da classe em JSON
            $table->string('pagina', 20)->nullable(); // Página de referência

            $table->timestamps(); // created_at e updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes'); // Remove tabela
    }
};
