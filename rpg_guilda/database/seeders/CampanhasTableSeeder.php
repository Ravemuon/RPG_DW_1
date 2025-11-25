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
            $this->command->info('Nenhum usuário ou sistema encontrado. Crie pelo menos 1 usuário e 1 sistema.');
            return;
        }

        // Lista de nomes criativos
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
            'O Portal Proibido'
        ];

        for ($i = 0; $i < 10; $i++) {
            $criador = $usuarios->random();
            $sistema = $sistemas->random();
            $privada = rand(0, 1) === 1;

            $campanha = Campanha::create([
                'nome' => $nomes[$i],
                'descricao' => "Descrição da campanha '{$nomes[$i]}', criada para aventuras épicas e desafios emocionantes.",
                'sistema_id' => $sistema->id,
                'criador_id' => $criador->id,
                'status' => rand(0, 1) ? 'ativa' : 'inativa',
                'privada' => $privada,
                'codigo_convite' => $privada ? Str::upper(Str::random(6)) : null,
                'pagina' => null,
            ]);

            $this->command->info("Campanha '{$campanha->nome}' criada: " . ($privada ? 'Privada' : 'Pública'));
        }
    }
}
