<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('personagens', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('campanha_id')->constrained('campanhas')->onDelete('cascade');
            $table->foreignId('raca_id')->nullable()->constrained('racas')->onDelete('set null');
            $table->string('classe', 50)->nullable();
            $table->string('origem', 50)->nullable();
            $table->string('sistema_rpg', 50)->nullable();
            $table->json('atributos')->nullable();
            $table->text('descricao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->string('pagina', 50)->nullable();
            $table->timestamps();
            $table->index(['nome', 'classe', 'origem', 'raca_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('personagens');
    }
};
