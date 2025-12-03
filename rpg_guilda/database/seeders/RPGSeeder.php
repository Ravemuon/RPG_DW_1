<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// Seeders do sistema
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\SistemasTableSeeder;
use Database\Seeders\ClassesTableSeeder;
use Database\Seeders\RacasTableSeeder;
use Database\Seeders\RacasTableSeederOutros;
use Database\Seeders\OrigensTableSeeder;
use Database\Seeders\OrigensTableSeederOutros;
use Database\Seeders\PericiasTableSeeder;
use Database\Seeders\CampanhasTableSeeder;
use Database\Seeders\PersonagemSeeder;
use Database\Seeders\SessoesTableSeeder;
use Database\Seeders\MissoesTableSeeder;

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
            RacasTableSeederOutros::class,
            OrigensTableSeeder::class,
            OrigensTableSeederOutros::class,
            PericiasTableSeeder::class,
            CampanhasTableSeeder::class,
            PersonagemSeeder::class,
            SessoesTableSeeder::class,
            MissoesTableSeeder::class,
        ]);

        $this->command->info('=== RPGSeeder concluído com sucesso! ===');
    }
}
