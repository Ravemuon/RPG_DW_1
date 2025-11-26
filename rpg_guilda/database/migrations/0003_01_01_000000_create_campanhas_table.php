<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('campanhas', function (Blueprint $table) {
            $table->id();

            $table->string('nome', 100)->index()->comment('Nome da campanha');
            $table->text('descricao')->nullable()->comment('Descrição da campanha');

            $table->foreignId('sistema_id')->constrained('sistemas')->onDelete('cascade');
            $table->foreignId('criador_id')->constrained('users')->onDelete('cascade');

            $table->enum('status', ['ativa','inativa'])->default('ativa')->index();
            $table->boolean('privada')->default(false)->index();

            $table->string('codigo_convite', 10)->nullable()->unique()->comment('Código para acesso à campanha privada');

            $table->string('pagina', 100)->nullable()->comment('Página ou link da campanha');

            $table->timestamps();
            $table->softDeletes()->comment('Marca se a campanha foi deletada logicamente');

            $table->index(['nome', 'sistema_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('campanhas');
    }
};
