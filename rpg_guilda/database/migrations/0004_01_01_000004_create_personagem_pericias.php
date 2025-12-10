<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personagem_classe', function (Blueprint $table) {
            $table->id();

            // Relacionamento com personagem
            $table->foreignId('personagem_id')
                  ->constrained('personagens')
                  ->onDelete('cascade')
                  ->comment('Referência ao personagem');

            // Relacionamento com classe
            $table->foreignId('classe_id')
                  ->constrained('classes')
                  ->onDelete('cascade')
                  ->comment('Referência à classe do personagem');

            $table->timestamps();

            // Evita duplicidade de classes para o mesmo personagem
            $table->unique(['personagem_id', 'classe_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personagem_classe');
    }
};
