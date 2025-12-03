<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Missao;
use App\Models\Campanha;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MissoesTableSeeder extends Seeder
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

        // Lista de títulos e recompensas possíveis
        $titulos = [
            'Roubo na Fortaleza',
            'Resgate do Prisioneiro',
            'Expedição ao Vale Proibido',
            'Investigação das Sombras',
            'Defesa da Cidade',
            'Ritual Perdido',
            'Busca pelo Artefato',
            'Ameaça dos Mortos',
            'A Rebelião dos Mercenários',
            'O Confronto Final'
        ];

        $recompensas = [
            '100 moedas de ouro',
            'Espada encantada',
            'Pergaminho mágico',
            'Armadura resistente',
            'Poção de cura',
            'Troféu raro',
            'Mapa antigo',
            'Acesso a guilda secreta',
            'Reputação +10',
            'Item lendário'
        ];

        $prioridades = ['baixa', 'media', 'alta'];
        $statusPossiveis = ['pendente', 'em_andamento', 'concluida', 'cancelada'];

        // Quantidade de missões por campanha
        $missoesPorCampanha = 5;

        foreach ($campanhas as $campanha) {
            for ($i = 0; $i < $missoesPorCampanha; $i++) {
                $titulo = $titulos[array_rand($titulos)] . ' #' . ($i + 1);
                $descricao = 'Descrição detalhada da missão ' . $titulo;
                $recompensa = $recompensas[array_rand($recompensas)];
                $prioridade = $prioridades[array_rand($prioridades)];
                $status = $statusPossiveis[array_rand($statusPossiveis)];
                $mestre = $usuarios->random();

                Missao::create([
                    'campanha_id' => $campanha->id,
                    'user_id' => $mestre->id,
                    'titulo' => $titulo,
                    'descricao' => $descricao,
                    'recompensa' => $recompensa,
                    'prioridade' => $prioridade,
                    'status' => $status,
                ]);

                $this->command->info("Missão '{$titulo}' criada para a campanha '{$campanha->nome}'.");
            }
        }

        $this->command->info('Missões populadas com sucesso!');
    }
}
