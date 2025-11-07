<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('campanhas', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->foreignId('sistema_id')->constrained('sistemas')->onDelete('cascade');
            $table->foreignId('criador_id')->constrained('users')->onDelete('cascade');
            $table->text('descricao')->nullable();
            $table->enum('status', ['ativa','inativa'])->default('ativa')->index();
            $table->boolean('privada')->default(false)->index();
            $table->string('codigo_convite', 10)->nullable();
            $table->string('pagina', 50)->nullable();
            $table->timestamps();
            $table->index(['nome', 'sistema_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('campanhas');
    }
};
