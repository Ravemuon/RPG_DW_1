<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sistemas', function (Blueprint $table) {
            $table->id(); // Chave primária auto-increment
            $table->string('nome', 100)->unique(); // Nome único do sistema
            $table->text('descricao')->nullable(); // Descrição do sistema
            $table->string('foco', 100)->nullable(); // Foco principal do sistema
            $table->string('mecanica_principal', 50)->nullable(); // Mecânica principal
            $table->string('complexidade', 50)->nullable(); // Complexidade

            $table->json('atributos')->nullable() // Atributos em JSON
                  ->comment('Atributos do sistema em forma de chave => nome');

            $table->boolean('usa_sanidade')->default(false); // Indica se usa sanidade

            $table->string('formula_pontos_vida', 200)->nullable(); // Fórmula de pontos de vida

            $table->json('recursos')->nullable(); // Recursos adicionais em JSON

            $table->json('regras_opcionais')->nullable(); // Regras opcionais em JSON

            $table->timestamps(); // created_at e updated_at
        });
    }

    public function down(): void {
        Schema::dropIfExists('sistemas'); // Remove tabela
    }
};
