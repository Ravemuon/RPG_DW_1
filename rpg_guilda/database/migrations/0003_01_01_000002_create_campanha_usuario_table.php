<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campanha_usuario', function (Blueprint $table) {
            $table->id();

            // Relacionamento com usuários
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Relacionamento com campanhas
            $table->foreignId('campanha_id')
                  ->constrained('campanhas')
                  ->onDelete('cascade');

            $table->timestamps();

            // Evita duplicidade
            $table->unique(['user_id', 'campanha_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campanha_usuario');
    }
};
