<?php

namespace App\Http\Controllers;

use App\Models\Notificacao;
use Illuminate\Http\Request;

class NotificacaoController extends Controller
{
    // Lista todas as notificações de um usuário
    public function index($usuario_id)
    {
        $notificacoes = Notificacao::where('usuario_id', $usuario_id)
            ->with('sessao')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notificacoes);
    }

    // Cria uma notificação
    public function store(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|exists:usuarios,id_usuario',
            'sessao_id' => 'nullable|exists:sessoes,id',
            'tipo' => 'nullable|string|max:50',
            'mensagem' => 'required|string',
        ]);

        $notificacao = Notificacao::create($request->all());

        return response()->json([
            'message' => 'Notificação criada com sucesso!',
            'notificacao' => $notificacao
        ]);
    }

    // Marca notificação como lida
    public function marcarComoLida($id)
    {
        $notificacao = Notificacao::findOrFail($id);
        $notificacao->lida = true;
        $notificacao->save();

        return response()->json([
            'message' => 'Notificação marcada como lida!',
            'notificacao' => $notificacao
        ]);
    }

    // Deleta uma notificação
    public function destroy($id)
    {
        $notificacao = Notificacao::findOrFail($id);
        $notificacao->delete();

        return response()->json([
            'message' => 'Notificação deletada com sucesso!'
        ]);
    }
}
