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

            // --- NOVOS CAMPOS ADICIONADOS ---

            // Sistema de nível e experiência
            $table->integer('nivel')->default(1);
            $table->integer('xp')->default(0);
            $table->integer('bonus_proficiencia')->default(2);

            // Atributos especiais
            $table->integer('sanidade')->nullable()->comment('Pontuação de sanidade mental');
            $table->integer('sorte')->nullable()->comment('Pontuação de sorte ou destino');

                // Outras informações
                $table->json('atributos')->nullable();
                $table->text('descricao')->nullable();
                $table->text('historia')->nullable();
                $table->text('personalidade')->nullable();
                $table->text('inventario')->nullable();

                // MANTIDO: 'string' é o tipo correto para armazenar o caminho do arquivo (path)
                $table->string('imagem')->nullable();

                $table->boolean('ativo')->default(true);
                $table->string('pagina', 50)->nullable();

            $table->timestamps();

            // Indexes melhorados
            $table->index(['nome', 'raca_id', 'classe_id', 'origem_id']);
            $table->index(['nivel', 'xp']); // Novo index para buscas por nível
        });
    }

    public function down(): void {
        Schema::dropIfExists('personagens');
    }
};
