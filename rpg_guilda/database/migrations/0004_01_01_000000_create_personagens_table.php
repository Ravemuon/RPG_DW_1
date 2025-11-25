<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('personagens', function (Blueprint $table) {
            $table->id();

            // Dados básicos
            $table->string('nome', 100);

            // Chaves estrangeiras principais
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('campanha_id')
                ->constrained('campanhas')
                ->onDelete('cascade');

            // --- RELACIONAMENTOS CORRETOS ---

            $table->foreignId('raca_id')
                ->nullable()
                ->constrained('racas')
                ->nullOnDelete();

            $table->foreignId('classe_id')
                ->nullable()
                ->constrained('classes')
                ->nullOnDelete();

            $table->foreignId('origem_id')
                ->nullable()
                ->constrained('origens')
                ->nullOnDelete();

            $table->foreignId('sistema_id')
                ->nullable()
                ->constrained('sistemas')
                ->nullOnDelete();

            // Outras informações
            $table->json('atributos')->nullable();
            $table->text('descricao')->nullable();
            $table->text('historia')->nullable();
            $table->text('personalidade')->nullable();
            $table->text('inventario')->nullable();
            $table->string('imagem')->nullable();
            $table->boolean('ativo')->default(true);
            $table->string('pagina', 50)->nullable();

            $table->timestamps();

            // Indexes melhorados
            $table->index(['nome', 'raca_id', 'classe_id', 'origem_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('personagens');
    }
};
