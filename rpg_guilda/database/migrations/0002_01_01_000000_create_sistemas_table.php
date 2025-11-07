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
            $table->json('regras_opcionais')->nullable();
            $table->integer('max_atributos')->default(6);
            $table->string('atributo1_nome', 50)->nullable();
            $table->string('atributo2_nome', 50)->nullable();
            $table->string('atributo3_nome', 50)->nullable();
            $table->string('atributo4_nome', 50)->nullable();
            $table->string('atributo5_nome', 50)->nullable();
            $table->string('atributo6_nome', 50)->nullable();
            $table->string('pagina', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('sistemas');
    }
};
