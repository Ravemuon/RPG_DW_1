<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('origens', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);              // Nome da origem (ex: Nobre, Criminoso, Cientista)
            $table->string('sistemaRPG', 50);         // Sistema ao qual pertence
            $table->text('descricao')->nullable();     // História de fundo e bônus que concede
            $table->json('bônus')->nullable();         // Pontos ou vantagens que a origem concede (flexível, JSON)
            $table->timestamps();
        });

        // Tabela pivot personagem_origem (relacionamento N:N)
        Schema::create('personagem_origem', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personagem_id')->constrained('personagens')->onDelete('cascade');
            $table->foreignId('origem_id')->constrained('origens')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personagem_origem');
        Schema::dropIfExists('origens');
    }
};
