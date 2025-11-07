<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notificacao;
use Illuminate\Support\Facades\Auth;

class NotificacaoController extends Controller
{
    // ===================================================
    // Listar notificações do usuário logado
    // ===================================================
    public function index()
    {
        $usuario = Auth::user();

        $notificacoes = Notificacao::where('usuario_id', $usuario->id)
                                   ->orderByDesc('created_at')
                                   ->get();

        return view('notificacoes.index', compact('notificacoes'));
    }

    // ===================================================
    // Marcar uma notificação como lida
    // ===================================================
    public function marcarComoLida(Notificacao $notificacao)
    {
        $this->authorize('update', $notificacao); // Segurança: só dono pode marcar como lida

        $notificacao->update(['lida' => true]);

        return redirect()->back()->with('success', 'Notificação marcada como lida.');
    }

    // ===================================================
    // Marcar todas as notificações como lidas
    // ===================================================
    public function marcarTodasComoLidas()
    {
        Notificacao::where('usuario_id', Auth::id())
                   ->where('lida', false)
                   ->update(['lida' => true]);

        return redirect()->back()->with('success', 'Todas as notificações marcadas como lidas.');
    }

    // ===================================================
    // Deletar notificação
    // ===================================================
    public function destroy(Notificacao $notificacao)
    {
        $this->authorize('delete', $notificacao); // Segurança: só dono pode deletar

        $notificacao->delete();

        return redirect()->back()->with('success', 'Notificação deletada com sucesso.');
    }
}
