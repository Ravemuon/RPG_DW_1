<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('origens', function (Blueprint $table) {
            $table->id(); // Chave primária
            $table->string('nome'); // Nome da origem
            $table->foreignId('sistema_id') // FK para sistemas
                  ->constrained('sistemas')
                  ->onDelete('cascade');

            $table->text('descricao')->nullable(); // Descrição da origem
            $table->json('pericias_iniciais')->nullable() // Perícias iniciais em JSON
                  ->comment('JSON com tipo de bônus, quantidade e lista de perícias');
            $table->json('recursos_adicionais')->nullable() // Talentos, proficiências ou recursos especiais
                  ->comment('Talentos, proficiências ou recursos especiais.');

            $table->string('pagina', 50)->nullable(); // Página de referência
            $table->timestamps(); // created_at e updated_at
            $table->unique(['nome', 'sistema_id']); // Nome único por sistema
        });
    }

    public function down(): void {
        Schema::dropIfExists('origens');
    }
};