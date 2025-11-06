<?php

namespace App\Http\Controllers;

use App\Models\Notificacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacaoController extends Controller
{
    // Lista todas as notificações do usuário logado
    public function index()
    {
        $usuario = Auth::user();
        $notificacoes = $usuario->notificacoes()->with('sessao')->orderBy('created_at', 'desc')->get();
        return view('notificacoes.index', compact('notificacoes'));
    }

    // Marca notificação como lida
    public function marcarComoLida($id)
    {
        $notificacao = Notificacao::findOrFail($id);

        // Verifica se a notificação pertence ao usuário
        if ($notificacao->usuario_id !== Auth::id()) {
            abort(403);
        }

        $notificacao->update(['lida' => true]);

        return redirect()->back()->with('success', 'Notificação marcada como lida!');
    }

    // Deleta uma notificação
    public function destroy($id)
    {
        $notificacao = Notificacao::findOrFail($id);

        if ($notificacao->usuario_id !== Auth::id()) {
            abort(403);
        }

        $notificacao->delete();

        return redirect()->back()->with('success', 'Notificação deletada com sucesso!');
    }
}
