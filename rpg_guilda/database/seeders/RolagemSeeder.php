<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rolagem;
use App\Models\User;
use App\Models\Campanha;
use App\Models\Personagem;

class RolagemSeeder extends Seeder
{
    public function run(): void
    {
        // Pega o primeiro usuário como base
        $user = User::first();
        if (!$user) {
            $this->command->warn('Nenhum usuário encontrado. Rolagem não criada.');
            return;
        }

        // Pega a primeira campanha
        $campanha = Campanha::first();
        if (!$campanha) {
            $this->command->warn('Nenhuma campanha encontrada. Rolagem não criada.');
            return;
        }

        // Busca um personagem existente do usuário nesta campanha
        $personagem = Personagem::where('user_id', $user->id)
            ->where('campanha_id', $campanha->id)
            ->first();

        // Se não existir, cria uma mensagem de aviso
        if (!$personagem) {
            $this->command->warn("Nenhum personagem encontrado para o usuário '{$user->nome}' na campanha '{$campanha->nome}'.");
        }

        // Configura dados da rolagem
        $tipoDado = 'd20';
        $quantidade = 1;
        $mod = 2;
        $resultado = rand(1, 20) + $mod; // Simula o resultado da rolagem

        // Cria a rolagem no banco
        Rolagem::create([
            'user_id' => $user->id,
            'campanha_id' => $campanha->id,
            'personagem_id' => $personagem ? $personagem->id : null,
            'tipo_dado' => $tipoDado,
            'quantidade' => $quantidade,
            'modificador' => $mod,
            'resultado' => $resultado,
            'descricao' => 'Rolagem de teste de ataque',
            'tipo_rolagem' => 'ataque',
        ]);
    }
}
