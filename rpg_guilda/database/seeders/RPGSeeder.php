<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// Importa todos os seeders usados
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\SistemasTableSeeder;
use Database\Seeders\ClassesTableSeeder;
use Database\Seeders\RacasTableSeeder;
use Database\Seeders\OrigensTableSeeder;
use Database\Seeders\PericiasTableSeeder;
use Database\Seeders\SistemaClasseTableSeeder;
use Database\Seeders\SistemaRacaTableSeeder;
use Database\Seeders\SistemaPericiaTableSeeder;
use Database\Seeders\CampanhasTableSeeder;
use Database\Seeders\PersonagemSeeder;

class RPGSeeder extends Seeder
{
    /**
     * Seed do sistema completo de RPG.
     */
    public function run(): void
    {
        $this->command->info('=== Iniciando RPGSeeder ===');

        // Ordem lógica para evitar erros de FK
        $this->call([
            UsersTableSeeder::class,
            SistemasTableSeeder::class,
            ClassesTableSeeder::class,
            RacasTableSeeder::class,
            OrigensTableSeeder::class,
            PericiasTableSeeder::class,
            CampanhasTableSeeder::class,
            PersonagemSeeder::class,
        ]);

        $this->command->info('=== RPGSeeder concluído com sucesso! ===');
    }
}
