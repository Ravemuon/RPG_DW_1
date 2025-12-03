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
            $table->string('nome');
            $table->foreignId('sistema_id')->constrained('sistemas')->onDelete('cascade');
            $table->string('atributo_relacionado');
            $table->string('atributo_nome')->nullable()->comment('Nome legível do atributo conforme o sistema');
            $table->text('descricao')->nullable();
            $table->integer('modificador')->default(0);
            $table->timestamps();
            $table->unique(['nome', 'sistema_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pericias');
    }
};
