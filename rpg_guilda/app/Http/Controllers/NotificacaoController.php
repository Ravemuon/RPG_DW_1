<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notificacao;
use Illuminate\Support\Facades\Auth;

class NotificacaoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ===================================================
    // 🔹 Lista todas as notificações do usuário logado
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
    // 🔹 Marca uma notificação como lida
    // ===================================================
    public function marcarComoLida($id)
    {
        $user = Auth::user();

        $notificacao = Notificacao::where('id', $id)
                                  ->where('usuario_id', $user->id)
                                  ->firstOrFail();

        $notificacao->update(['lida' => true]);

        return redirect()->back()->with('success', 'Notificação marcada como lida.');
    }

    // ===================================================
    // 🔹 Marca todas as notificações como lidas
    // ===================================================
    public function marcarTodasComoLidas()
    {
        Notificacao::where('usuario_id', Auth::id())
                   ->where('lida', false)
                   ->update(['lida' => true]);

        return redirect()->back()->with('success', 'Todas as notificações marcadas como lidas.');
    }

    // ===================================================
    // 🔹 Deleta uma notificação
    // ===================================================
    public function destroy($id)
    {
        $user = Auth::user();

        $notificacao = Notificacao::where('id', $id)
                                  ->where('usuario_id', $user->id)
                                  ->firstOrFail();

        $notificacao->delete();

        return redirect()->back()->with('success', 'Notificação deletada com sucesso.');
    }
}
