<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Origem;

class OrigemSeeder extends Seeder
{
    public function run(): void
    {
        // === D&D ===
        Origem::create([
            'nome' => 'Nobre',
            'sistemaRPG' => 'D&D',
            'descricao' => 'Vem de uma família nobre, recebe habilidades sociais extras.',
            'bônus' => ['carisma'=>2,'pericia_social'=>1]
        ]);

        Origem::create([
            'nome' => 'Criminoso',
            'sistemaRPG' => 'D&D',
            'descricao' => 'Experiência em furtividade e redes de informação.',
            'bônus' => ['destreza'=>1,'furtividade'=>2]
        ]);

        // === Ordem Paranormal ===
        Origem::create([
            'nome' => 'Orfão de Incidente Sobrenatural',
            'sistemaRPG' => 'Ordem Paranormal',
            'descricao' => 'Marcado pelo sobrenatural, recebe pontos extras para habilidades específicas.',
            'bônus' => ['nex'=>2,'sanidade'=>1]
        ]);

        Origem::create([
            'nome' => 'Pesquisador',
            'sistemaRPG' => 'Ordem Paranormal',
            'descricao' => 'Treinado em conhecimento oculto, recebe pontos de Intelecto.',
            'bônus' => ['intelecto'=>2,'presenca'=>1]
        ]);

        // === Call of Cthulhu ===
        Origem::create([
            'nome' => 'Acadêmico',
            'sistemaRPG' => 'Call of Cthulhu',
            'descricao' => 'Estudioso e bem informado, bônus em Educação e perícias.',
            'bônus' => ['educacao'=>2,'investigacao'=>1]
        ]);

        Origem::create([
            'nome' => 'Policial',
            'sistemaRPG' => 'Call of Cthulhu',
            'descricao' => 'Treinado em combate e investigação.',
            'bônus' => ['destreza_cth'=>1,'investigacao'=>2]
        ]);

        // === Fate Core ===
        Origem::create([
            'nome' => 'Militar',
            'sistemaRPG' => 'Fate Core',
            'descricao' => 'Treinamento em combate e disciplina.',
            'bônus' => ['stunts'=>['Ataque Preciso']]
        ]);

        // === Cypher System ===
        Origem::create([
            'nome' => 'Acadêmico',
            'sistemaRPG' => 'Cypher System',
            'descricao' => 'Recebe pontos extras para gastar em habilidades intelectuais.',
            'bônus' => ['atributos_custom'=>['inteligencia'=>2]]
        ]);

        // === Apocalypse World ===
        Origem::create([
            'nome' => 'Sobrevivente',
            'sistemaRPG' => 'Apocalypse World',
            'descricao' => 'Experiência em sobrevivência extrema.',
            'bônus' => ['resistencia'=>2,'percepcao'=>1]
        ]);

        // === Cyberpunk 2093 - Arkana-RPG ===
        Origem::create([
            'nome' => 'Hacker de Rua',
            'sistemaRPG' => 'Cyberpunk 2093 - Arkana-RPG',
            'descricao' => 'Conhecimentos em sistemas urbanos e clandestinos.',
            'bônus' => ['tecnologia'=>3,'destreza'=>1]
        ]);
    }
}
