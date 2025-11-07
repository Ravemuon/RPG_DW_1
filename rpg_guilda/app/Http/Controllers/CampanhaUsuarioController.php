<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campanha;
use App\Models\User;
use App\Notifications\SolicitarEntradaCampanha; // Usando Notifications
use Illuminate\Support\Facades\Auth;

class CampanhaUsuarioController extends Controller
{
    // Apenas mestre pode gerenciar (adicionar/remover/aprovar)
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ===================================================
    // 🔹 Adiciona ou Atualiza o status de um usuário na campanha
    // (Pode ser usado para aprovar solicitação: pendente -> ativo)
    // ===================================================
    public function gerenciar(Request $request, Campanha $campanha)
    {
        // Garante que apenas o mestre pode fazer isso
        $this->authorize('update', $campanha);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:ativo,mestre,pendente',
        ]);

        $user = User::findOrFail($request->user_id);
        $statusAtual = $campanha->jogadores()->where('user_id', $user->id)->first()?->pivot->status;

        // Se já existir, atualiza. Se não, anexa.
        if ($statusAtual) {
            $campanha->jogadores()->updateExistingPivot($user->id, ['status' => $request->status]);
            $mensagem = "Status de {$user->nome} atualizado para **{$request->status}**.";
        } else {
            // Se for adicionar um novo usuário (sem solicitação prévia)
            $campanha->jogadores()->attach($user->id, ['status' => $request->status]);
            $mensagem = "{$user->nome} adicionado(a) à campanha com status **{$request->status}**.";

            // Opcional: Notificar o novo jogador adicionado
        }

        return redirect()->back()->with('success', $mensagem);
    }

    // ===================================================
    // 🔹 Remove usuário da campanha
    // ===================================================
    public function remover(Request $request, Campanha $campanha)
    {
        $this->authorize('update', $campanha); // Apenas o mestre pode remover

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Evita que o mestre remova a si mesmo (o criador) - a menos que seja um sistema mais complexo de transferência de maestria
        if ($campanha->criador_id == $request->user_id) {
             return redirect()->back()->with('error', 'O criador da campanha não pode ser removido.');
        }

        $user = User::findOrFail($request->user_id);
        $campanha->jogadores()->detach($user->id);

        return redirect()->back()->with('success', "{$user->nome} removido(a) da campanha com sucesso.");
    }

    // ===================================================
    // 🔹 Lista jogadores de uma campanha (Acesso mais direto, geralmente via CampanhaController@show)
    // ===================================================
    public function listarJogadores(Campanha $campanha)
    {
        // Você provavelmente usará isso apenas como um fragmento na view show.blade, mas está ok aqui.
        // O show() já carrega os jogadores.
        return view('campanhas.jogadores', compact('campanha'));
    }
}
