<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personagens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('campanha_id')->constrained('campanhas')->onDelete('cascade');

            $table->string('nome', 100);
            $table->string('classe')->nullable(); // arquétipo ou classe
            $table->string('sistemaRPG', 50);
            $table->string('origem')->nullable(); // origem do personagem

            $table->boolean('npc')->default(false);
            $table->string('classe_npc')->nullable();
            $table->json('atributos_extra')->nullable();

            // D&D
            $table->integer('forca')->nullable();
            $table->integer('destreza')->nullable();
            $table->integer('constituicao')->nullable();
            $table->integer('inteligencia')->nullable();
            $table->integer('sabedoria')->nullable();
            $table->integer('carisma')->nullable();

            // Ordem Paranormal
            $table->integer('agilidade')->nullable();
            $table->integer('intelecto')->nullable();
            $table->integer('presenca')->nullable();
            $table->integer('vigor')->nullable();
            $table->integer('nex')->nullable();
            $table->integer('sanidade')->nullable();

            // Call of Cthulhu
            $table->integer('forca_cth')->nullable();
            $table->integer('destreza_cth')->nullable();
            $table->integer('poder')->nullable();
            $table->integer('constituicao_cth')->nullable();
            $table->integer('aparencia')->nullable();
            $table->integer('educacao')->nullable();
            $table->integer('tamanho')->nullable();
            $table->integer('inteligencia_cth')->nullable();
            $table->integer('sanidade_cth')->nullable();
            $table->integer('pontos_vida')->nullable();

            // Fate Core
            $table->json('aspects')->nullable();
            $table->json('stunts')->nullable();
            $table->integer('fate_points')->nullable();

            // Outros sistemas
            $table->json('atributos_custom')->nullable();
            $table->json('poderes')->nullable();

            // Habilidades gerais
            $table->json('habilidades')->nullable();

            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('personagens');
    }
};
