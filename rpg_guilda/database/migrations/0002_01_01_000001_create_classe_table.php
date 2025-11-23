<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();

            $table->string('nome', 100);
            $table->unsignedBigInteger('sistema_id');
            $table->foreign('sistema_id')->references('id')->on('sistemas')->onDelete('cascade');

            $table->text('descricao')->nullable();

            // A classe usa magia?
            $table->boolean('usa_magia')->default(false);

            // Atributos extras concedidos pela classe
            // Ex.: {"forca": 2, "sabedoria": 1}
            $table->json('atributos_bonus')->nullable();

            // Poderes especiais
            $table->json('poderes')->nullable();

            // Página do livro (manual)
            $table->string('pagina', 20)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
