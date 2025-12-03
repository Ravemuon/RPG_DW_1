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
            $table->json('pericias_iniciais')->nullable()->comment('JSON com tipo de bônus, quantidade e lista de perícias');
            
            $table->json('recursos_adicionais')->nullable()
                ->comment('Talentos, proficiências ou recursos especiais.');

            $table->string('pagina', 50)->nullable();

            $table->timestamps();

            $table->unique(['nome', 'sistema_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('origens');
    }
};
