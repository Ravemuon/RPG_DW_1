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
        Schema::create('notificacoes', function (Blueprint $table) {
            $table->id();

            // Relacionamento com usuário
            $table->foreignId('usuario_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Relacionamento opcional com sessão
            $table->foreignId('sessao_id')
                  ->nullable()
                  ->constrained('sessoes')
                  ->onDelete('cascade');

            // Tipo e conteúdo da notificação
            $table->string('tipo')->nullable();
            $table->text('mensagem');

            // Status de leitura
            $table->boolean('lida')->default(false);

            // Timestamps padrão
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacoes');
    }
};
