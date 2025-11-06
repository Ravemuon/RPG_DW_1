<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campanhas', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('sistemaRPG', 50);
            $table->text('descricao')->nullable();
            $table->enum('status', ['ativa', 'inativa'])->default('ativa');
            $table->boolean('privada')->default(false);
            $table->string('codigo_convite', 10)->nullable(); // gerado se privada
            $table->foreignId('criador_id')->constrained('users')->onDelete('cascade'); // Mestre
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campanhas');
    }
};
