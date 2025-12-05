<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('racas', function (Blueprint $table) {
            $table->id(); // Chave primária
            $table->string('nome'); // Nome da raça
            $table->foreignId('sistema_id') // FK para sistemas
                  ->constrained('sistemas')
                  ->onDelete('cascade');
            $table->text('descricao')->nullable(); // Descrição da raça
            $table->json('modificadores_atributos')->nullable() // Modificadores de atributos
                  ->comment('Bônus de atributos da raça, mapeados pelos nomes internos dos atributos do sistema.');

            $table->enum('tipo_bonus', ['flat', 'multiplicador', 'escolha'])->default('flat'); // Tipo de bônus
            $table->integer('bonus_livre')->default(0); // Pontos de bônus livres

            $table->string('pagina', 50)->nullable(); // Página de referência
            $table->timestamps(); // created_at e updated_at
            $table->unique(['nome', 'sistema_id']); // Nome único por sistema
        });
    }

    public function down(): void {
        Schema::dropIfExists('racas');
    }
};






