<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $temasCompletos = [
            'medieval',
            'fantasia',
            'sobrenatural',
            'steampunk',
            'cyberpunk',
            'apocaliptico',
            'oceano',
            'floresta',
            'deserto'
        ];

        // 🔹 Ajusta valores inválidos existentes no banco para evitar erro na alteração de ENUM
        DB::table('users')
            ->whereNotIn('tema', $temasCompletos)
            ->update(['tema' => 'medieval']);

        // 🔹 Altera a coluna 'tema' para ENUM com os temas válidos
        Schema::table('users', function (Blueprint $table) use ($temasCompletos) {
            $table->enum('tema', $temasCompletos)
                  ->default('medieval')
                  ->comment('Temas disponíveis no app: ' . implode(', ', $temasCompletos))
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 🔹 Reverte a coluna 'tema' para string genérica, mantendo padrão 'medieval'
        Schema::table('users', function (Blueprint $table) {
            $table->string('tema', 50)
                  ->default('medieval')
                  ->comment('Tema do usuário, antes era ENUM')
                  ->change();
        });
    }
};
