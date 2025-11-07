<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arquivos', function (Blueprint $table) {
            $table->id(); // id padrão
            $table->foreignId('usuario_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->comment('Dono do arquivo');

            $table->foreignId('campanha_id')
                  ->nullable()
                  ->constrained('campanhas')
                  ->onDelete('cascade')
                  ->comment('Campanha relacionada, se houver');

            $table->string('nome_original')->nullable()->comment('Nome original do arquivo');
            $table->string('caminho')->comment('Caminho no storage');
            $table->string('tipo')->default('desconhecido')->comment('Ex.: avatar, banner, documento, imagem');
            $table->bigInteger('tamanho')->nullable()->comment('Tamanho em bytes');

            $table->timestamps();

            // Index para consultas rápidas
            $table->index(['usuario_id', 'campanha_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arquivos');
    }
};
