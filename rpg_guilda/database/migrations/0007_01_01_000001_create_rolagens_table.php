<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rolagens', function (Blueprint $table) {
            $table->id();

            // Relações
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // jogador que rolou
            $table->foreignId('campanha_id')->constrained('campanhas')->onDelete('cascade'); // campanha
            $table->foreignId('personagem_id')->nullable()->constrained('personagens')->onDelete('set null'); // personagem opcional

            // Dados da rolagem
            $table->string('tipo_dado', 10); // ex: d6, d20, d100
            $table->integer('quantidade')->default(1); // número de dados rolados
            $table->integer('modificador')->default(0); // bônus ou penalidade
            $table->integer('resultado'); // resultado final (soma dos dados + modificadores)
            $table->text('descricao')->nullable(); // ex: "Ataque do guerreiro"

            // Tipo de rolagem
            $table->enum('tipo_rolagem', ['ataque', 'pericia', 'magia', 'resistencia', 'outro'])->default('outro');

            $table->timestamps();

            // Índices úteis
            $table->index(['user_id', 'campanha_id', 'personagem_id']);
            $table->index(['tipo_rolagem']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rolagens');
    }
};
