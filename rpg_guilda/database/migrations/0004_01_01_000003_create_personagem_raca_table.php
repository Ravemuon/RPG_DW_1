<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personagem_raca', function (Blueprint $table) {
            $table->id();

            // Relacionamento com personagem
            $table->foreignId('personagem_id')
                  ->constrained('personagens')
                  ->onDelete('cascade')
                  ->comment('Referência ao personagem');

            // Relacionamento com raça
            $table->foreignId('raca_id')
                  ->constrained('racas')
                  ->onDelete('cascade')
                  ->comment('Referência à raça do personagem');

            $table->timestamps();

            // Evita duplicidade de raças para o mesmo personagem
            $table->unique(['personagem_id', 'raca_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personagem_raca');
    }
};
