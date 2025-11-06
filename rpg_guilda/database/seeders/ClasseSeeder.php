<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classe;

class ClasseSeeder extends Seeder
{
    public function run(): void
    {
        // === D&D ===
        Classe::create([
            'nome' => 'Guerreiro',
            'sistemaRPG' => 'D&D',
            'descricao' => 'Especialista em combate corpo a corpo. Ganha 5 pontos de vida adicionais e +1 em Força.'
        ]);

        Classe::create([
            'nome' => 'Mago',
            'sistemaRPG' => 'D&D',
            'descricao' => 'Usuário de magia arcana. Recebe 3 pontos extras em Inteligência e +2 em Pontos de Magia.'
        ]);

        Classe::create([
            'nome' => 'Ladino',
            'sistemaRPG' => 'D&D',
            'descricao' => 'Ágil e sorrateiro. Ganha +2 em Destreza e +1 em Furtividade.'
        ]);

        // === Ordem Paranormal ===
        Classe::create([
            'nome' => 'Combatente',
            'sistemaRPG' => 'Ordem Paranormal',
            'descricao' => 'Especialista em combate físico. Recebe 5 pontos para distribuir entre Força, Agilidade e Vigor.'
        ]);

        Classe::create([
            'nome' => 'Ocultista',
            'sistemaRPG' => 'Ordem Paranormal',
            'descricao' => 'Especialista em magia e rituais. Recebe 5 pontos para distribuir entre Intelecto e Presença.'
        ]);

        Classe::create([
            'nome' => 'Especialista',
            'sistemaRPG' => 'Ordem Paranormal',
            'descricao' => 'Perito em habilidades específicas (armas, perícias ou tecnologia). Recebe 5 pontos para distribuir livremente e bônus específico na perícia escolhida.'
        ]);

        // === Call of Cthulhu ===
        Classe::create([
            'nome' => 'Investigador',
            'sistemaRPG' => 'Call of Cthulhu',
            'descricao' => 'Personagem focado em descobrir mistérios e resolver enigmas. Recebe 5 pontos de perícias adicionais e bônus em Sanidade.'
        ]);

        Classe::create([
            'nome' => 'Acadêmico',
            'sistemaRPG' => 'Call of Cthulhu',
            'descricao' => 'Especialista em conhecimentos. Recebe +3 em Educação e 5 pontos em perícias de investigação.'
        ]);

        Classe::create([
            'nome' => 'Detetive Particular',
            'sistemaRPG' => 'Call of Cthulhu',
            'descricao' => 'Focado em investigação e combate moderado. Recebe 3 pontos para distribuir entre Destreza e Perícias de investigação.'
        ]);

        // === Fate Core ===
        Classe::create([
            'nome' => 'Guerreiro',
            'sistemaRPG' => 'Fate Core',
            'descricao' => 'Arquetipo de combate. Recebe 3 pontos para distribuir em Stunts e Aspects.'
        ]);

        Classe::create([
            'nome' => 'Explorador',
            'sistemaRPG' => 'Fate Core',
            'descricao' => 'Personagem de exploração e sobrevivência. Recebe 3 pontos em Stunts de mobilidade e percepção.'
        ]);

        // === Cypher System ===
        Classe::create([
            'nome' => 'Mago',
            'sistemaRPG' => 'Cypher System',
            'descricao' => 'Usuário de magia. Recebe pontos extras para gastar em habilidades e poderes.'
        ]);

        Classe::create([
            'nome' => 'Guerreiro',
            'sistemaRPG' => 'Cypher System',
            'descricao' => 'Especialista em combate. Recebe pontos extras para melhorar atributos físicos e de combate.'
        ]);

        // === Apocalypse World ===
        Classe::create([
            'nome' => 'Líder',
            'sistemaRPG' => 'Apocalypse World',
            'descricao' => 'Comanda e protege o grupo. Recebe bônus de pontos para Carisma e Resistência.'
        ]);

        Classe::create([
            'nome' => 'Explorador',
            'sistemaRPG' => 'Apocalypse World',
            'descricao' => 'Focado em sobrevivência e exploração. Recebe pontos extras para mobilidade e perícias.'
        ]);

        // === Cyberpunk 2093 - Arkana-RPG ===
        Classe::create([
            'nome' => 'Hacker',
            'sistemaRPG' => 'Cyberpunk 2093 - Arkana-RPG',
            'descricao' => 'Especialista em tecnologia e sistemas. Recebe pontos para melhorar atributos de Tecnologia e Perícias digitais.'
        ]);

        Classe::create([
            'nome' => 'Mercenário',
            'sistemaRPG' => 'Cyberpunk 2093 - Arkana-RPG',
            'descricao' => 'Focado em combate e sobrevivência urbana. Recebe pontos extras em Força, Destreza e armas.'
        ]);
    }
}
