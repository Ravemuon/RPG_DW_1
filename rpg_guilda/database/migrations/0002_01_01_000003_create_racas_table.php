<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('racas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->foreignId('sistema_id')->constrained('sistemas')->onDelete('cascade');
            $table->text('descricao')->nullable();
            $table->json('modificadores_atributos')->nullable()->comment('Bônus de atributos da raça, mapeados pelos nomes internos dos atributos do sistema.');

            // Mantendo os campos genéricos
            $table->enum('tipo_bonus', ['flat', 'multiplicador', 'escolha'])->default('flat');

            // Página / referência
            $table->string('pagina', 50)->nullable();

            $table->timestamps();

            $table->unique(['nome', 'sistema_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('racas');
    }
};
