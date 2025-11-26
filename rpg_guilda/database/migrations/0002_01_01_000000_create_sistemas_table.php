<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sistemas', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100)->unique();
            $table->text('descricao')->nullable();
            $table->string('foco', 100)->nullable();
            $table->string('mecanica_principal', 50)->nullable();
            $table->string('complexidade', 50)->nullable();

            $table->json('atributos')->nullable()
                  ->comment('Atributos do sistema em forma de chave => nome');

            $table->boolean('usa_sanidade')->default(false);

            $table->string('formula_pontos_vida', 200)->nullable();

            $table->json('recursos')->nullable();

            $table->json('regras_opcionais')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('sistemas');
    }
};
