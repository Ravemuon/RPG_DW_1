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

            $table->json('pericias_iniciais')->nullable()->comment('JSON com tipo de bônus, quantidade e lista de perícias');

            $table->json('equipamento_inicial')->nullable()->comment('Lista de itens e opções de equipamento inicial (JSON)');

            $table->boolean('usa_magia')->default(false);

            $table->json('atributos_bonus')->nullable();

            $table->json('poderes')->nullable();

            $table->string('pagina', 20)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
