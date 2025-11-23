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
        // Verifica se existem usuários e sistemas
        $usuarios = User::all();
        $sistemas = Sistema::all();

        if ($usuarios->isEmpty() || $sistemas->isEmpty()) {
            $this->command->info('Nenhum usuário ou sistema encontrado. Crie pelo menos 1 usuário e 1 sistema.');
            return;
        }

        // Criar 10 campanhas de teste
        for ($i = 1; $i <= 10; $i++) {

            // Escolhe usuário e sistema aleatórios
            $criador = $usuarios->random();
            $sistema = $sistemas->random();

            $privada = rand(0, 1) === 1;

            $campanha = Campanha::create([
                'nome' => "Campanha de Teste #$i",
                'descricao' => "Esta é a descrição da campanha de teste número $i.",
                'sistema_id' => $sistema->id,
                'criador_id' => $criador->id,
                'status' => rand(0, 1) ? 'ativa' : 'inativa',
                'privada' => $privada,
                'codigo_convite' => $privada ? Str::upper(Str::random(6)) : null,
                'pagina' => null,
            ]);

            // Exibe feedback no console
            $this->command->info("Campanha '{$campanha->nome}' criada: " . ($privada ? 'Privada' : 'Pública'));
        }
    }
}
