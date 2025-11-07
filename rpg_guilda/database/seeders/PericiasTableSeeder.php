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
            ['nome' => 'Enganação', 'sistema_rpg' => 'D&D 5e'],
            ['nome' => 'Intimidação', 'sistema_rpg' => 'D&D 5e'],
            ['nome' => 'Investigação', 'sistema_rpg' => 'D&D 5e'],
            ['nome' => 'Medicina', 'sistema_rpg' => 'D&D 5e'],
            ['nome' => 'Natureza', 'sistema_rpg' => 'D&D 5e'],
            ['nome' => 'Percepção', 'sistema_rpg' => 'D&D 5e'],
            ['nome' => 'Persuasão', 'sistema_rpg' => 'D&D 5e'],
            ['nome' => 'Prestidigitação', 'sistema_rpg' => 'D&D 5e'],
            ['nome' => 'Religião', 'sistema_rpg' => 'D&D 5e'],
            ['nome' => 'Sobrevivência', 'sistema_rpg' => 'D&D 5e'],
            ['nome' => 'Acuidade com Animais', 'sistema_rpg' => 'D&D 5e'],
            ['nome' => 'Intuição', 'sistema_rpg' => 'D&D 5e'],
        ];

        foreach ($pericias as $pericia) {
            Pericia::create($pericia);
        }
    }
}
