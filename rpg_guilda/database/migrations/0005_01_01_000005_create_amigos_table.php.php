<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('amizades', function (Blueprint $table) {
            $table->id();

            //  Relação entre usuários
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Usuário que envia a solicitação');

            $table->foreignId('friend_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Usuário que recebe a solicitação');

            //  Status da amizade
            $table->enum('status', ['pendente', 'aceita'])->default('pendente')
                ->comment('Status da amizade');

            //  Timestamps
            $table->timestamps();

            //  Evita duplicidade de solicitações entre os mesmos usuários (ordem não importa)
            $table->unique(['user_id', 'friend_id']);

            //  Índice para buscas rápidas de status
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amizades');
    }
};
