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
            $table->string('nome', 100)->comment('Nome da classe, ex: Guerreiro, Mago');

            // Ligação direta com o sistema que define esta classe
            $table->unsignedBigInteger('sistema_id')->comment('Referência ao sistema que controla esta classe');
            $table->foreign('sistema_id')->references('id')->on('sistemas')->onDelete('cascade');

            $table->text('descricao')->nullable()->comment('Descrição detalhada da classe');

            // Atributos iniciais — podem variar dependendo do sistema
            $table->integer('forca')->nullable();
            $table->integer('destreza')->nullable();
            $table->integer('constituicao')->nullable();
            $table->integer('inteligencia')->nullable();
            $table->integer('sabedoria')->nullable();
            $table->integer('carisma')->nullable();
            $table->integer('agilidade')->nullable();
            $table->integer('intelecto')->nullable();
            $table->integer('presenca')->nullable();
            $table->integer('vigor')->nullable();
            $table->integer('nex')->nullable();
            $table->integer('sanidade')->nullable();
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

            // Aspectos e poderes especiais
            $table->json('aspects')->nullable();
            $table->json('stunts')->nullable();
            $table->integer('fate_points')->nullable();
            $table->json('atributos_custom')->nullable()->comment('Atributos específicos de sistemas customizados');
            $table->json('poderes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('classes', function(Blueprint $table){
            $table->dropForeign(['sistema_id']);
        });
        Schema::dropIfExists('classes');
    }
};
