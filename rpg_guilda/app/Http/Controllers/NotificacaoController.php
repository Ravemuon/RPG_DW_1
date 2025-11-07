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
        $usuario = Auth::user();

        $notificacao = Notificacao::where('id', $id)
            ->where('usuario_id', $usuario->id)
            ->firstOrFail();

        $notificacao->lida = true;
        $notificacao->save();

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
        $usuario = Auth::user();

        $notificacao = Notificacao::where('id', $id)
            ->where('usuario_id', $usuario->id)
            ->firstOrFail();

        $notificacao->delete();

        return redirect()->back()->with('success', 'Notificação deletada com sucesso.');
    }

    // ===================================================
    // 🔹 Método extra para criar notificações de forma genérica
    // ===================================================
    public static function criarNotificacao($usuarioId, $mensagem, $tipo = 'geral', $sessaoId = null)
    {
        return Notificacao::create([
            'usuario_id' => $usuarioId,
            'sessao_id' => $sessaoId,
            'tipo' => $tipo,
            'mensagem' => $mensagem,
            'lida' => false,
        ]);
    }
}
