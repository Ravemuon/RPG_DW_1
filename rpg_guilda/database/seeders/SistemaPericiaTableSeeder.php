<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SistemaPericiaTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sistema_pericia')->insert([
            ['sistema_id' => 1, 'pericia_id' => 1],
            ['sistema_id' => 1, 'pericia_id' => 2],
        ]);
    }
}
