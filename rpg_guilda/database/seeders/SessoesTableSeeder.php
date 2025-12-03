<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sessao;
use App\Models\Campanha;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SessoesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $campanhas = Campanha::all();
        $usuarios = User::all();

        if ($campanhas->isEmpty() || $usuarios->isEmpty()) {
            $this->command->info('Nenhuma campanha ou usuário encontrado. Crie pelo menos 1 de cada.');
            return;
        }

        // Lista de títulos de sessões
        $titulos = [
            'A Noite do Conclave',
            'O Mistério da Cidade Perdida',
            'O Ataque dos Orcs',
            'Sombras na Floresta',
            'O Assalto ao Castelo',
            'A Jornada Proibida',
            'O Ritual Esquecido',
            'Em Busca do Artefato',
            'A Rebelião dos Mortos',
            'Segredos do Labirinto'
        ];

        // Quantidade de sessões por campanha
        $sessoesPorCampanha = 5;

        foreach ($campanhas as $campanha) {
            for ($i = 0; $i < $sessoesPorCampanha; $i++) {
                $titulo = $titulos[array_rand($titulos)] . ' #' . ($i + 1);
                $dataHora = Carbon::now()->addDays(rand(-30, 60))->setTime(rand(0,23), rand(0,59));
                $status = ['agendada', 'em_andamento', 'concluida', 'cancelada'][array_rand(['agendada', 'em_andamento', 'concluida', 'cancelada'])];
                $mestre = $usuarios->random();

                Sessao::create([
                    'campanha_id' => $campanha->id,
                    'titulo' => $titulo,
                    'data_hora' => $dataHora,
                    'status' => $status,
                    'criado_por' => $mestre->id,
                    'resumo' => 'Resumo da sessão ' . $titulo,
                ]);

                $this->command->info("Sessão '{$titulo}' criada para a campanha '{$campanha->nome}'.");
            }
        }

        $this->command->info('Sessões populadas com sucesso!');
    }
}
