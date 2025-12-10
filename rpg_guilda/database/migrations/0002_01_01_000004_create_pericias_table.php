<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pericias', function (Blueprint $table) {
            $table->id();

            // Relacionamento com sistemas
            $table->unsignedBigInteger('sistema_id');
            $table->foreign('sistema_id')->references('id')->on('sistemas')->onDelete('cascade');

            // Campos principais
            $table->string('nome');
            $table->string('atributo_relacionado');
            $table->string('atributo_nome')->nullable();
            $table->text('descricao')->nullable();
            $table->integer('modificador')->default(0);

            $table->timestamps();

            // Evita duplicação dentro do mesmo sistema
            $table->unique(['nome', 'sistema_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pericias');
    }
};
