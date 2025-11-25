<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabela de sessões (encontros)
        Schema::create('sessoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campanha_id')->constrained('campanhas')->onDelete('cascade');
            $table->string('titulo');
            $table->dateTime('data_hora');
            $table->enum('status', ['agendada', 'em_andamento', 'concluida', 'cancelada'])
                  ->default('agendada');
            // 'criado_por' refere-se ao Mestre (GM)
            $table->foreignId('criado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->text('resumo')->nullable();
            $table->timestamps();
        });

        // Tabela pivot entre sessões e personagens (rastreia quais personagens participaram)
        Schema::create('sessoes_personagens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sessao_id')->constrained('sessoes')->onDelete('cascade');
            $table->foreignId('personagem_id')->constrained('personagens')->onDelete('cascade');
            $table->boolean('presente')->default(false);
            // Campo para registrar resultados da sessão relacionados ao personagem
            $table->json('resultado')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessoes_personagens');
        Schema::dropIfExists('sessoes');
    }
};
