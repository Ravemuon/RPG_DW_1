<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Mensagem;
use App\Models\Campanha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\DadoRPG;

class ChatController extends Controller
{
    // Lista todos os chats de uma campanha
    public function index($campanha_id)
    {
        $chats = Chat::where('campanha_id', $campanha_id)->get();
        return response()->json($chats);
    }

    // Mostra mensagens de um chat
    public function show($chat_id)
    {
        $chat = Chat::with('mensagens.user')->findOrFail($chat_id);
        return response()->json($chat);
    }

    // Envia uma mensagem no chat
    public function enviarMensagem(Request $request, $chat_id)
    {
        $request->validate([
            'mensagem' => 'required|string|max:1000',
        ]);

        $mensagem = Mensagem::create([
            'chat_id' => $chat_id,
            'user_id' => Auth::id(),
            'mensagem' => $request->mensagem,
        ]);

        return response()->json([
            'message' => 'Mensagem enviada!',
            'mensagem' => $mensagem
        ]);
    }

    // Rola dados de RPG no chat
    public function rolarDado(Request $request, $chat_id)
    {
        $request->validate([
            'quantidade' => 'required|integer|min:1',
            'valor' => 'required|integer|min:2',
        ]);

        $resultado = DadoRPG::rolar($request->quantidade, $request->valor);

        $mensagem = Mensagem::create([
            'chat_id' => $chat_id,
            'user_id' => Auth::id(),
            'mensagem' => "Rolagem de {$request->quantidade}d{$request->valor}: "
                           . implode(', ', $resultado['resultados'])
                           . " (Total: {$resultado['total']})"
        ]);

        return response()->json([
            'message' => 'Dado rolado!',
            'resultado' => $resultado,
            'mensagem' => $mensagem
        ]);
    }
}
