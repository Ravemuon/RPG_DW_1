<?php

namespace App\Http\Controllers;

use App\Models\Mensagem;
use App\Models\Campanha;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MensagemController extends Controller
{
    // Lista todas as mensagens de uma campanha
    public function index($campanhaId)
    {
        $mensagens = Mensagem::where('campanha_id', $campanhaId)->get();
        return view('chat.campanha.index', compact('mensagens'));
    }

    // Salva uma nova mensagem (privada, de campanha ou de chat)
    public function store(Request $request)
    {
        $request->validate([
            'conteudo' => 'required|string',
            'tipo'    => 'required|in:privada,campanha,chat',
            'user_id' => 'required|exists:users,id',
            'campanha_id' => 'nullable|required_if:tipo,campanha|exists:campanhas,id',
            'chat_id'     => 'nullable|required_if:tipo,chat|exists:chats,id',
        ]);

        $mensagem = Mensagem::create([
            'conteudo' => $request->conteudo,
            'tipo' => $request->tipo,
            'user_id' => $request->user_id,
            'campanha_id' => $request->campanha_id,
            'chat_id' => $request->chat_id,
        ]);

        return response()->json($mensagem, 201);
    }

    // Mostra mensagens de uma campanha ou chat
    public function show($id)
    {
        $campanha = Campanha::find($id);
        $chat = Chat::find($id);

        if (!$campanha && !$chat) {
            return response()->json(['error' => 'Campanha ou Chat não encontrado.'], 404);
        }

        $mensagens = Mensagem::where('campanha_id', $id)
            ->orWhere('chat_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($mensagens);
    }

    // Lista mensagens privadas do usuário logado
    public function mensagensPrivadas()
    {
        $mensagens = Mensagem::where('tipo', 'privada')
            ->where(function ($query) {
                $query->where('user_id', Auth::id())
                      ->orWhere('destinatario_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($mensagens);
    }

    // Marca uma mensagem privada como lida
    public function marcarComoLida($id)
    {
        $mensagem = Mensagem::findOrFail($id);

        if ($mensagem->tipo !== 'privada') {
            return response()->json(['error' => 'Somente mensagens privadas podem ser marcadas como lidas.'], 400);
        }

        if ($mensagem->lida) {
            return response()->json(['message' => 'A mensagem já foi marcada como lida.']);
        }

        $mensagem->lida = true;
        $mensagem->save();

        return response()->json(['message' => 'Mensagem marcada como lida.']);
    }
}
