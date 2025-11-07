<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Garante que a tabela só será criada se não existir
        if (!Schema::hasTable('sistema_pericia')) {
            Schema::create('sistema_pericia', function (Blueprint $table) {
                $table->engine = 'InnoDB'; // Força engine compatível com foreign keys
                $table->id();

                // Colunas
                $table->unsignedBigInteger('sistema_id');
                $table->unsignedBigInteger('pericia_id');

                $table->timestamps();

                // Chave única
                $table->unique(['sistema_id', 'pericia_id']);

                // Foreign keys
                $table->foreign('sistema_id')
                      ->references('id')
                      ->on('sistemas')
                      ->onDelete('cascade');

                $table->foreign('pericia_id')
                      ->references('id')
                      ->on('pericias')
                      ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::table('sistema_pericia', function (Blueprint $table) {
            // Remove as foreign keys antes de dropar a tabela
            $table->dropForeign(['sistema_id']);
            $table->dropForeign(['pericia_id']);
        });

        Schema::dropIfExists('sistema_pericia');
    }
};
