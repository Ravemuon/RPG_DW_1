<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();

            $table->string('nome', 100);
            $table->unsignedBigInteger('sistema_id');
            $table->foreign('sistema_id')->references('id')->on('sistemas')->onDelete('cascade');

            $table->text('descricao')->nullable();
            $table->string('dado_vida', 5)->nullable()->comment('Dado de vida da classe (ex: d8), usado na fórmula de PV do Sistema.');

            // Perícias iniciais da classe
            $table->json('pericias_iniciais')->nullable()->comment('JSON com tipo de bônus, quantidade e lista de perícias');

            // Equipamento inicial que o personagem ganha ao escolher a classe (JSON)
            $table->json('equipamento_inicial')->nullable()->comment('Lista de itens e opções de equipamento inicial (JSON)');

            // A classe usa magia?
            $table->boolean('usa_magia')->default(false);

            // Atributos extras concedidos pela classe
            $table->json('atributos_bonus')->nullable();

            // Poderes especiais 
            $table->json('poderes')->nullable();

            // Página do livro (manual)
            $table->string('pagina', 20)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
