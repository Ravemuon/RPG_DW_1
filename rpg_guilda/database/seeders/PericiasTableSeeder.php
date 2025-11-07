<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pericia;

class PericiasTableSeeder extends Seeder
{
    public function run(): void
    {
        $pericias = [
            ['nome' => 'Acrobacia', 'sistema_rpg' => 'D&D 5e'],
            ['nome' => 'Arcanismo', 'sistema_rpg' => 'D&D 5e'],
            ['nome' => 'Atletismo', 'sistema_rpg' => 'D&D 5e'],
            ['nome' => 'Furtividade', 'sistema_rpg' => 'D&D 5e'],
        ];

        foreach ($pericias as $pericia) {
            Pericia::create($pericia);
        }
    }
}
