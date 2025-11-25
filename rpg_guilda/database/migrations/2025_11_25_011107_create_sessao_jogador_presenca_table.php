<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

public function up(): void
{
    Schema::create('sessao_jogador_presenca', function (Blueprint $table) {
        // Chaves Estrangeiras
        $table->foreignId('sessao_id')->constrained('sessoes')->onDelete('cascade');
        $table->foreignId('jogador_id')->constrained('users')->onDelete('cascade');

        // Campo de presença
        $table->boolean('confirmou_presenca')->default(true);

        $table->timestamps();

        // Chave primária composta para garantir a unicidade da presença
        $table->primary(['sessao_id', 'jogador_id']);
    });
}

};
