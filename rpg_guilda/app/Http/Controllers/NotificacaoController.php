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

    public function index()
    {
        $usuario = Auth::user();

        $notificacoes = Notificacao::where('usuario_id', $usuario->id)
            ->orderByDesc('created_at')
            ->get();

        return view('notificacoes.index', compact('notificacoes'));
    }

    public function marcarComoLida($id)
    {
        $usuario = Auth::user();

        $notificacao = Notificacao::where('id', $id)
            ->where('usuario_id', $usuario->id)
            ->first();

        if (!$notificacao) {
            return redirect()->back()->with('error', 'Notificação não encontrada.');
        }

        $notificacao->update(['lida' => true]);

        return redirect()->back()->with('success', 'Notificação marcada como lida.');
    }

    public function marcarTodasComoLidas()
    {
        Notificacao::where('usuario_id', Auth::id())
            ->where('lida', false)
            ->update(['lida' => true]);

        return redirect()->back()->with('success', 'Todas as notificações marcadas como lidas.');
    }

    public function destroy($id)
    {
        $usuario = Auth::user();

        $notificacao = Notificacao::where('id', $id)
            ->where('usuario_id', $usuario->id)
            ->first();

        if (!$notificacao) {
            return redirect()->back()->with('error', 'Notificação não encontrada.');
        }

        $notificacao->delete();

        return redirect()->back()->with('success', 'Notificação deletada com sucesso.');
    }

    public static function criarNotificacao($usuarioId, $mensagem, $tipo = 'geral', $sessaoId = null)
    {
        try {
            return Notificacao::create([
                'usuario_id' => $usuarioId,
                'sessao_id' => $sessaoId,
                'tipo' => $tipo,
                'mensagem' => $mensagem,
                'lida' => false,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Erro ao criar notificação: ' . $e->getMessage());
            return false;
        }
    }

    public function aprovarUsuario(Request $request, $campanhaId)
    {
        $campanha = Campanha::findOrFail($campanhaId);
        $userId = $request->input('user_id');
        $status = $request->input('status');

        $campanha->jogadores()->updateExistingPivot($userId, [
            'status' => $status === 'remover' ? 'removido' : $status,
        ]);

        $usuario = \App\Models\User::find($userId);

        if ($usuario) {
            $mensagem = match ($status) {
                'ativo'     => "🎉 Você foi aprovado para participar da campanha **{$campanha->nome}**!",
                'rejeitado' => "❌ Sua solicitação para entrar na campanha **{$campanha->nome}** foi rejeitada.",
                'remover'   => "🚫 Você foi removido da campanha **{$campanha->nome}**.",
                default     => null,
            };

            if ($mensagem) {
                NotificacaoController::criarNotificacao($usuario->id, $mensagem, 'campanha');
            }
        }

        return back()->with('success', 'Status do jogador atualizado com sucesso!');
    }

    public function limparTodas()
    {
        Notificacao::where('usuario_id', Auth::id())->delete();

        return redirect()->back()->with('success', 'Todas as notificações foram excluídas.');
    }
}
