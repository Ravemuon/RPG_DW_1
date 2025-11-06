<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arquivos', function (Blueprint $table) {
            $table->id('id_arquivo');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('campanha_id')->nullable()->constrained('campanhas')->onDelete('cascade');
            $table->string('nome_original');
            $table->string('caminho'); // caminho físico ou URL
            $table->string('tipo'); // ex: imagem, pdf, mapa
            $table->bigInteger('tamanho')->nullable(); // tamanho em bytes
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arquivos');
    }
};
