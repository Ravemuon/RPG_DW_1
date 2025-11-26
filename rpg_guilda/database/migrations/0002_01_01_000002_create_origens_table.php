<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('origens', function (Blueprint $table) {
            $table->id();

            $table->string('nome');
            $table->foreignId('sistema_id')
                ->constrained('sistemas')
                ->onDelete('cascade');

                $table->text('descricao')->nullable();
                $table->json('bonus_pericias')->nullable()->comment('Bônus de perícias concedidos pela origem, mapeados pelos nomes das perícias do sistema.');

                $table->json('recursos_adicionais')->nullable()->comment('Quaisquer talentos, proficiências ou recursos especiais que a origem fornece.');

                $table->string('pagina', 50)->nullable();

            $table->timestamps();
            $table->unique(['nome', 'sistema_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('origens');
    }
};
