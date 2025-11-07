<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('racas', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            $table->foreignId('sistema_id')->constrained('sistemas')->onDelete('cascade');
            $table->text('descricao')->nullable();
            $table->integer('forca_bonus')->default(0);
            $table->integer('destreza_bonus')->default(0);
            $table->integer('constituicao_bonus')->default(0);
            $table->integer('inteligencia_bonus')->default(0);
            $table->integer('sabedoria_bonus')->default(0);
            $table->integer('carisma_bonus')->default(0);
            $table->string('pagina', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('racas');
    }
};
