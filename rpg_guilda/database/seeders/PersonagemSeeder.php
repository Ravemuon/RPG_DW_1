<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonagemSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('personagens')->insert([
            [
                'nome' => 'Heroi Exemplo',
                'user_id' => 2,
                'campanha_id' => 1,
                'raca_id' => 1,
                'classe' => 'Guerreiro',
                'origem' => 'Aldeão',
                'sistema_rpg' => 'D&D 5e',
                'atributos' => json_encode(['forca' => 10, 'destreza' => 12, 'constituicao' => 14]),
                'descricao' => 'Personagem de teste',
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
