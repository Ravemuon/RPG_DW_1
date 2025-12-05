<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Personagem;
use App\Models\User;
use App\Models\Campanha;
use App\Models\Raca;
use App\Models\Classe;
use App\Models\Origem;
use App\Models\Sistema;

class PersonagemSeeder extends Seeder
{
    /**
     * Seed de personagens realistas com presets aleatórios.
     */
    public function run(): void
    {
        $usuarios = User::all();
        $campanhas = Campanha::all();
        $racas = Raca::all();
        $classes = Classe::all();
        $origens = Origem::all();
        $sistemas = Sistema::all();

        if ($usuarios->isEmpty() || $campanhas->isEmpty()) {
            $this->command->info('Nenhum usuário ou campanha encontrada. Crie pelo menos 1 usuário e 1 campanha.');
            return;
        }

        // Presets de nomes
        $nomes = ['Arthas','Lunara','Thorin','Elandra','Kael','Seraphina','Darius','Lyra','Gideon','Isolde','Caius','Selene','Fenris','Aurora','Ragnar','Valeria'];

        // Presets de histórias
        $historias = [
            'Cresceu em uma vila pacata e aprendeu a lidar com animais e natureza.',
            'Sobreviveu à guerra e carrega marcas do passado.',
            'Filho de mercadores, acostumado ao comércio e à diplomacia.',
            'Estudioso das artes arcanas, passou anos em bibliotecas antigas.',
            'Perdeu a família cedo e aprendeu a se virar sozinho nas ruas.',
            'Explorador de ruínas antigas, sempre em busca de conhecimento e tesouros.'
        ];

        // Presets de personalidades
        $personalidades = [
            'Corajoso e impulsivo.',
            'Calmo e estratégico.',
            'Curioso e observador.',
            'Arrogante, mas leal aos amigos.',
            'Generoso e protetor.',
            'Misterioso e reservado.'
        ];

        // Presets de inventários
        $inventarios = [
            'Espada longa, escudo, mochila, cantil.',
            'Arco curto, aljava com flechas, capa camuflada.',
            'Adagas escondidas, poções de cura, kit de ferramentas.',
            'Livro de magia, varinha, pergaminhos antigos.',
            'Faca de combate, corda, kit de escalada.',
            'Uniforme militar, armas de fogo leves, rádios de comunicação.'
        ];

        $quantidade = 30; // total de personagens a criar

        for ($i = 0; $i < $quantidade; $i++) {
            $usuario = $usuarios->random();
            $campanha = $campanhas->random();
            $raca = $racas->random();
            $classe = $classes->random();
            $origem = $origens->random();
            $sistema = $sistemas->random();

            $nivel = rand(1, 10);
            $xp = rand(0, 5000);
            $bonusProficiencia = 2 + intdiv($nivel - 1, 4);

            Personagem::create([
                'nome' => $nomes[array_rand($nomes)] . ' ' . Str::upper(Str::random(2)),
                'user_id' => $usuario->id,
                'campanha_id' => $campanha->id,
                'raca_id' => $raca?->id,
                'classe_id' => $classe?->id,
                'origem_id' => $origem?->id,
                'sistema_id' => $sistema?->id,
                'nivel' => $nivel,
                'xp' => $xp,
                'bonus_proficiencia' => $bonusProficiencia,
                'sanidade' => rand(50, 100),
                'sorte' => rand(1, 20),
                'atributos' => json_encode([
                    'forca' => rand(8, 18),
                    'destreza' => rand(8, 18),
                    'constituicao' => rand(8, 18),
                    'inteligencia' => rand(8, 18),
                    'sabedoria' => rand(8, 18),
                    'carisma' => rand(8, 18)
                ]),
                'descricao' => 'Personagem gerado com presets realistas.',
                'historia' => $historias[array_rand($historias)],
                'personalidade' => $personalidades[array_rand($personalidades)],
                'inventario' => $inventarios[array_rand($inventarios)],
                'imagem' => null,
                'ativo' => true,
                'pagina' => null
            ]);

            $this->command->info("Personagem " . ($i+1) . " criado: " . $nivel . "º nível, campanha '" . ($campanha?->nome ?? 'Desconhecida') . "'");
        }

        $this->command->info("{$quantidade} personagens foram populados com sucesso!");
    }
}
