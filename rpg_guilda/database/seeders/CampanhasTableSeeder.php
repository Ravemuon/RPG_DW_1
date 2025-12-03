<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Campanha;
use App\Models\User;
use App\Models\Sistema;

class CampanhasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuarios = User::all();
        $sistemas = Sistema::all();

        if ($usuarios->isEmpty() || $sistemas->isEmpty()) {
            $this->command->info('Nenhum usuário ou sistema encontrado. Crie pelo menos 1 usuário e 1 sistema para continuar.');
            return;
        }

        // Lista ampliada (40 nomes)
        $nomes = [
            'A Sombra do Dragão',
            'Mistérios de Arkham',
            'O Legado dos Heróis',
            'A Guerra das Dimensões',
            'Crônicas da Ordem Perdida',
            'Os Segredos de Avalon',
            'O Último Mago',
            'A Rebelião dos Mortos',
            'Expedição ao Desconhecido',
            'O Portal Proibido',
            'Ecos do Abismo',
            'Reino das Areias Carmesim',
            'As Sete Lâminas do Destino',
            'O Reino Partido',
            'Tempestade de Ferro',
            'O Guardião da Tormenta',
            'A Cidade Submersa',
            'O Véu da Serpente',
            'A Canção das Estrelas Mortas',
            'Sombras de um Deus Esquecido',
            'O Sorriso do Demônio',
            'Trono de Sangue Antigo',
            'O Despertar dos Titãs',
            'Floresta das Almas Perdidas',
            'Além do Horizonte Quebrado',
            'O Circo dos Horrores Eternos',
            'O Engenho dos Autômatos',
            'A Noite que Nunca Acaba',
            'Relíquias da Aurora',
            'A Torre que Sussurra',
            'O Caminho das Mil Máscaras',
            'A Filha da Tempestade',
            'Legião de Prata',
            'O Reino Sem Nome',
            'A Maré dos Condenados',
            'O Arco do Infinito',
            'A Fortaleza do Eclipse',
            'As Crônicas do Relâmpago',
            'O Manto da Imperatriz Sombria',
            'As Ruínas do Dragão Caído',
            'Fronteira do Vazio',
            'O Pacto das Três Luas',
            'Os Filhos do Crepúsculo',
            'Lamento de Aço'
        ];

        foreach ($nomes as $nome) {

            $criador = $usuarios->random();
            $sistema = $sistemas->random();
            $privada = rand(0, 1) === 1;

            Campanha::create([
                'nome' => $nome,
                'descricao' => "A campanha '{$nome}' envolve mistérios, aventuras e desafios únicos dentro do sistema {$sistema->nome}.",
                'sistema_id' => $sistema->id,
                'criador_id' => $criador->id,
                'status' => rand(0, 1) ? 'ativa' : 'inativa',
                'privada' => $privada,
                'codigo_convite' => $privada ? Str::upper(Str::random(6)) : null,
                'pagina' => null,
            ]);

            $this->command->info("Campanha '{$nome}' criada com sucesso!");
        }
    }
}
