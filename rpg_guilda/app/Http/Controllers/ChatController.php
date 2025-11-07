<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\ChatMensagem;
use App\Models\Campanha;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NovaMensagemChat;

class ChatController extends Controller
{
    // ===================================================
    // Lista o chat de uma campanha
    // ===================================================
    public function index(Campanha $campanha)
    {
        $user = Auth::user();

        // Verifica acesso: mestre ou jogador da campanha
        if (!$user || ($campanha->privada && $campanha->criador_id !== $user->id && !$campanha->jogadores->contains($user->id))) {
            return redirect()->route('campanhas.index')->with('error', 'Acesso negado ao chat desta campanha.');
        }

        // Garante que exista um chat
        $chat = $campanha->chat ?? Chat::create([
            'campanha_id' => $campanha->id,
            'nome' => "Chat da campanha {$campanha->nome}"
        ]);

        // Carrega mensagens com usuário, apenas campos essenciais
        $mensagens = $chat->mensagens()
                          ->with('user:id,nome') // evita recursão
                          ->orderBy('created_at', 'asc')
                          ->get();

        return view('campanhas.chat', compact('campanha', 'chat', 'mensagens'));
    }

    // ===================================================
    // Envia uma nova mensagem
    // ===================================================
    public function enviarMensagem(Request $request, Campanha $campanha)
    {
        $user = Auth::user();

        if (!$user || ($campanha->privada && $campanha->criador_id !== $user->id && !$campanha->jogadores->contains($user->id))) {
            return redirect()->route('campanhas.index')->with('error', 'Acesso negado ao chat desta campanha.');
        }

        $request->validate([
            'mensagem' => 'required|string|max:1000'
        ]);

        $chat = $campanha->chat ?? Chat::create([
            'campanha_id' => $campanha->id,
            'nome' => "Chat da campanha {$campanha->nome}"
        ]);

        $mensagem = $chat->mensagens()->create([
            'user_id' => $user->id,
            'mensagem' => $request->mensagem
        ]);

        // Notifica os jogadores da campanha (somente campos essenciais)
        foreach ($campanha->jogadores as $usuario) {
            if ($usuario->id !== $user->id) {
                $usuario->notify(new NovaMensagemChat($mensagem));
            }
        }

        return redirect()->route('campanhas.chat', $campanha->id)->with('success', 'Mensagem enviada!');
    }

    // ===================================================
    // Atualiza mensagem existente
    // ===================================================
    public function atualizarMensagem(Request $request, ChatMensagem $mensagem)
    {
        $user = Auth::user();

        if ($user->id !== $mensagem->user_id && $user->id !== $mensagem->chat->campanha->criador_id) {
            return back()->with('error', 'Você não tem permissão para editar esta mensagem.');
        }

        $request->validate([
            'mensagem' => 'required|string|max:1000'
        ]);

        $mensagem->update(['mensagem' => $request->mensagem]);

        return back()->with('success', 'Mensagem atualizada!');
    }

    // ===================================================
    // Exclui mensagem (substitui pelo texto "Mensagem excluída")
    // ===================================================
    public function excluirMensagem(ChatMensagem $mensagem)
    {
        $user = Auth::user();

        if ($user->id !== $mensagem->user_id && $user->id !== $mensagem->chat->campanha->criador_id) {
            return back()->with('error', 'Você não tem permissão para excluir esta mensagem.');
        }

        $mensagem->update(['mensagem' => null]);

        return back()->with('success', 'Mensagem excluída!');
    }
}
