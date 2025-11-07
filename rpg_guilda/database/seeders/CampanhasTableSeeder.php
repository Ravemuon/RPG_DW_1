<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampanhasTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('campanhas')->insert([
            ['nome' => 'Campanha Exemplo', 'descricao' => 'Campanha inicial', 'sistema_id' => 1, 'criador_id' => 1, 'status' => 'ativa', 'privada' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
