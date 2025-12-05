<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
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

        // Ajusta valores inválidos existentes
        DB::table('users')
            ->whereNotIn('tema', $temasCompletos)
            ->update(['tema' => 'medieval']);

        // Altera a coluna 'tema' para ENUM com valores válidos
        Schema::table('users', function (Blueprint $table) use ($temasCompletos) {
            $table->enum('tema', $temasCompletos)
                  ->default('medieval')
                  ->comment('Temas disponíveis no app: ' . implode(', ', $temasCompletos))
                  ->change();
        });
    }

    public function down(): void
    {
        // Reverte para string genérica
        Schema::table('users', function (Blueprint $table) {
            $table->string('tema', 50)
                  ->default('medieval')
                  ->comment('Tema do usuário, antes era ENUM')
                  ->change();
        });
    }
};
