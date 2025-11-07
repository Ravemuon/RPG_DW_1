<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SistemasTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sistemas')->insert([
            ['nome' => 'D&D 5e', 'descricao' => 'Sistema Dungeons & Dragons 5ª Edição', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Pathfinder', 'descricao' => 'Sistema Pathfinder 2ª Edição', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
