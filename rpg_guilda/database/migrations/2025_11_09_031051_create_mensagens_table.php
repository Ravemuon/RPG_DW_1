<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('mensagens', function (Blueprint $table) {
            $table->id();  // ID da mensagem
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');  // ID do usuário que enviou a mensagem
            $table->text('conteudo');  // Conteúdo da mensagem
            $table->enum('tipo', ['privada', 'campanha', 'chat'])->default('campanha');  // Tipo de mensagem
            $table->timestamps();  // Timestamps para registrar criação e atualização
        });
    }

    public function down(): void {
        Schema::dropIfExists('mensagens');
    }
};
