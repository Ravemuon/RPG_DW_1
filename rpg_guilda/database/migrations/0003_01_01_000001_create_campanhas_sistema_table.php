<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campanhas', function (Blueprint $table) {
            // Verifica se a coluna não existe antes de criar
            if (!Schema::hasColumn('campanhas', 'sistema_id')) {
                $table->unsignedBigInteger('sistema_id')->nullable()->after('id')
                      ->comment('Referência ao sistema utilizado nesta campanha');

                $table->foreign('sistema_id')
                      ->references('id')->on('sistemas')
                      ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('campanhas', function (Blueprint $table) {
            // Só tenta remover se a coluna existir
            if (Schema::hasColumn('campanhas', 'sistema_id')) {
                $table->dropForeign(['sistema_id']);
                $table->dropColumn('sistema_id');
            }
        });
    }
};
