<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Chat;
use App\Models\ChatMensagem;
use App\Models\User;
use App\Models\Campanha; // Corrigido o nome do modelo

class ChatSeeder extends Seeder
{
    public function run(): void
    {
        // Pega a primeira campanha
        $campanha = Campanha::first();
        if (!$campanha) {
            $this->command->warn('Nenhuma campanha encontrada. Chat não criado.');
            return;
        }

        // Pega o primeiro usuário
        $user = User::first();
        if (!$user) {
            $this->command->warn('Nenhum usuário encontrado. Mensagem do chat não criada.');
            return;
        }

        // Cria o chat principal da campanha
        $chat = Chat::create([
            'campanha_id' => $campanha->id,
            'nome' => 'Chat Geral',
        ]);

        // Cria a primeira mensagem de boas-vindas
        ChatMensagem::create([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'mensagem' => 'Bem-vindos à campanha!',
        ]);

        $this->command->info("Chat 'Chat Geral' criado para a campanha '{$campanha->nome}' com mensagem inicial.");
    }
}
