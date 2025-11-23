<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('campanhas', function (Blueprint $table) {
            $table->id();

            // Informações básicas
            $table->string('nome', 100)->index()->comment('Nome da campanha');
            $table->text('descricao')->nullable()->comment('Descrição da campanha');

            // Relacionamentos
            $table->foreignId('sistema_id')->constrained('sistemas')->onDelete('cascade');
            $table->foreignId('criador_id')->constrained('users')->onDelete('cascade');

            // Status e visibilidade
            $table->enum('status', ['ativa','inativa'])->default('ativa')->index();
            $table->boolean('privada')->default(false)->index();

            // Código de convite único para campanhas privadas
            $table->string('codigo_convite', 10)->nullable()->unique()->comment('Código para acesso à campanha privada');

            // Página relacionada (opcional)
            $table->string('pagina', 100)->nullable()->comment('Página ou link da campanha');

            // Timestamps e exclusão lógica (soft delete)
            $table->timestamps();
            $table->softDeletes()->comment('Marca se a campanha foi deletada logicamente');

            // Índices compostos para busca rápida
            $table->index(['nome', 'sistema_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('campanhas');
    }
};
    