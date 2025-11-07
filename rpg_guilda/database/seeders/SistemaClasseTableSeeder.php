<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SistemaClasseTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sistema_classe')->insert([
            ['sistema_id' => 1, 'classe_id' => 1],
            ['sistema_id' => 1, 'classe_id' => 2],
        ]);
    }
}

