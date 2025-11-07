<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonagemSeeder extends Seeder
{
    public function run(): void
    {
        $personagens = [
            // ===== D&D 5e =====
            [
                'nome' => 'Filhian',
                'user_id' => 2,
                'campanha_id' => 1,
                'raca_id' => 1,
                'classe' => 'Mago',
                'origem' => 'Humano',
                'sistema_rpg' => 'D&D 5e',
                'atributos' => json_encode(['forca' => 8, 'destreza' => 14, 'constituicao' => 12, 'inteligencia' => 18, 'sabedoria' => 10, 'carisma' => 13]),
                'descricao' => 'Um mago prodígio de D&D 5e',
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Mordomo Menta',
                'user_id' => 3,
                'campanha_id' => 1,
                'raca_id' => 2,
                'classe' => 'Clérigo',
                'origem' => 'Nobre',
                'sistema_rpg' => 'D&D 5e',
                'atributos' => json_encode(['forca' => 10, 'destreza' => 12, 'constituicao' => 14, 'inteligencia' => 11, 'sabedoria' => 16, 'carisma' => 12]),
                'descricao' => 'O fiel Mordomo Menta',
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Shrek',
                'user_id' => 4,
                'campanha_id' => 1,
                'raca_id' => 3,
                'classe' => 'Bárbaro',
                'origem' => 'Pântano',
                'sistema_rpg' => 'D&D 5e',
                'atributos' => json_encode(['forca' => 18, 'destreza' => 10, 'constituicao' => 16, 'inteligencia' => 8, 'sabedoria' => 10, 'carisma' => 12]),
                'descricao' => 'O ogro verde favorito de todos',
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ===== Ordem Paranormal =====
            [
                'nome' => 'Kaiser',
                'user_id' => 5,
                'campanha_id' => 2,
                'raca_id' => 4,
                'classe' => 'Investigador',
                'origem' => 'Humano',
                'sistema_rpg' => 'Ordem Paranormal',
                'atributos' => json_encode(['forca' => 12, 'destreza' => 14, 'intelecto' => 16, 'percepcao' => 15, 'vontade' => 13, 'carisma' => 11]),
                'descricao' => 'Investigador Kaiser, mestre das sombras',
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Joui',
                'user_id' => 6,
                'campanha_id' => 2,
                'raca_id' => 5,
                'classe' => 'Místico',
                'origem' => 'Cidade Grande',
                'sistema_rpg' => 'Ordem Paranormal',
                'atributos' => json_encode(['forca' => 10, 'destreza' => 12, 'intelecto' => 18, 'percepcao' => 16, 'vontade' => 15, 'carisma' => 12]),
                'descricao' => 'Místico da Ordem Paranormal',
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ===== Call of Cthulhu / Meme =====
            [
                'nome' => 'Sans',
                'user_id' => 7,
                'campanha_id' => 3,
                'raca_id' => 6,
                'classe' => 'Investigador',
                'origem' => 'Undertale',
                'sistema_rpg' => 'Call of Cthulhu',
                'atributos' => json_encode(['forca' => 8, 'destreza' => 18, 'constituicao' => 10, 'inteligencia' => 14, 'poder' => 12, 'carisma' => 10]),
                'descricao' => 'O esqueleto que nunca morre',
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Máscara',
                'user_id' => 8,
                'campanha_id' => 3,
                'raca_id' => 7,
                'classe' => 'Investigador',
                'origem' => 'Cinema',
                'sistema_rpg' => 'Call of Cthulhu',
                'atributos' => json_encode(['forca' => 12, 'destreza' => 16, 'constituicao' => 12, 'inteligencia' => 10, 'poder' => 12, 'carisma' => 14]),
                'descricao' => 'Personagem caótico e divertido',
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Homem-Aranha',
                'user_id' => 9,
                'campanha_id' => 3,
                'raca_id' => 8,
                'classe' => 'Investigador',
                'origem' => 'Marvel',
                'sistema_rpg' => 'Call of Cthulhu',
                'atributos' => json_encode(['forca' => 14, 'destreza' => 18, 'constituicao' => 12, 'inteligencia' => 14, 'poder' => 10, 'carisma' => 12]),
                'descricao' => 'Amigo da vizinhança',
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Deadpool',
                'user_id' => 10,
                'campanha_id' => 3,
                'raca_id' => 9,
                'classe' => 'Investigador',
                'origem' => 'Marvel',
                'sistema_rpg' => 'Call of Cthulhu',
                'atributos' => json_encode(['forca' => 16, 'destreza' => 16, 'constituicao' => 14, 'inteligencia' => 12, 'poder' => 10, 'carisma' => 16]),
                'descricao' => 'O mercenário tagarela',
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Lobo',
                'user_id' => 11,
                'campanha_id' => 3,
                'raca_id' => 10,
                'classe' => 'Investigador',
                'origem' => 'DC Comics',
                'sistema_rpg' => 'Call of Cthulhu',
                'atributos' => json_encode(['forca' => 18, 'destreza' => 14, 'constituicao' => 16, 'inteligencia' => 10, 'poder' => 12, 'carisma' => 10]),
                'descricao' => 'Anti-herói do espaço',
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('personagens')->insert($personagens);
    }
}
