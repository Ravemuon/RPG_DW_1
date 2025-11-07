<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RacasTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('racas')->insert([
            ['nome' => 'Humano', 'sistema_id' => 1, 'descricao' => 'Versátil e comum', 'forca_bonus' => 0, 'destreza_bonus' => 0, 'constituicao_bonus' => 0, 'inteligencia_bonus' => 0, 'sabedoria_bonus' => 0, 'carisma_bonus' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Elfo', 'sistema_id' => 1, 'descricao' => 'Ágil e inteligente', 'forca_bonus' => 0, 'destreza_bonus' => 2, 'constituicao_bonus' => 0, 'inteligencia_bonus' => 1, 'sabedoria_bonus' => 0, 'carisma_bonus' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
