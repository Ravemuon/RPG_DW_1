<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrigensTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('origens')->insert([
            ['nome' => 'Aldeão', 'sistema_id' => 1, 'descricao' => 'Origem simples de aldeão', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Nobre', 'sistema_id' => 1, 'descricao' => 'Origem nobre e influente', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
