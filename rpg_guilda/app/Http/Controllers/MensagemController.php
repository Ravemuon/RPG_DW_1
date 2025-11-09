<?php

namespace App\Http\Controllers;

use App\Models\Mensagem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MensagemController extends Controller
{
    // MensagemController.php
    public function index()
    {
        // Lógica para exibir os chats ou mensagens de uma campanha
        $mensagens = Mensagem::where('campanha_id', $campanhaId)->get();  // Ajuste conforme sua necessidade

        return view('chat.campanha.index', compact('mensagens'));
    }

    /**
     * Armazenar uma nova mensagem.
     */
    public function store(Request $request)
    {
        $request->validate([
            'conteudo' => 'required|string',
            'tipo' => 'required|in:privada,campanha,chat',
            'user_id' => 'required|exists:users,id',
            'campanha_id' => 'nullable|required_if:tipo,campanha|exists:campanhas,id', // validação de campanha
            'chat_id' => 'nullable|required_if:tipo,chat|exists:chats,id', // validação de chat
        ]);

        $mensagem = Mensagem::create([
            'conteudo' => $request->conteudo,
            'tipo' => $request->tipo,
            'user_id' => $request->user_id,
            'campanha_id' => $request->campanha_id, // se for uma mensagem de campanha
            'chat_id' => $request->chat_id, // se for uma mensagem de chat
        ]);

        return response()->json($mensagem, 201);  // Retorna a mensagem criada com status 201
    }


    /**
     * Exibir as mensagens de uma campanha ou chat específico.
     */
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


    /**
     * Exibir todas as mensagens privadas do usuário logado.
     */
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

    /**
     * Marcar uma mensagem privada como lida.
     */
    public function marcarComoLida($id)
    {
        $mensagem = Mensagem::findOrFail($id);

        if ($mensagem->tipo !== 'privada') {
            return response()->json(['error' => 'Somente mensagens privadas podem ser marcadas como lidas.'], 400);
        }

        // Verifica se a mensagem já foi marcada como lida
        if ($mensagem->lida) {
            return response()->json(['message' => 'A mensagem já foi marcada como lida.']);
        }

        // Marca a mensagem como lida
        $mensagem->lida = true;
        $mensagem->save();

        return response()->json(['message' => 'Mensagem marcada como lida.']);
    }

}
