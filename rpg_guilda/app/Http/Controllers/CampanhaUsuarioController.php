<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campanha;
use App\Models\User;
use App\Notifications\SolicitarEntradaCampanha;
use App\Notifications\ConviteParaCampanha;
use App\Notifications\StatusCampanhaAtualizado;
use Illuminate\Support\Facades\Auth;

class CampanhaUsuarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ===================================================
    // Usuário solicita entrada em uma campanha
    // ===================================================
    public function solicitarEntrada(Campanha $campanha)
    {
        $user = Auth::user();
        if ($campanha->jogadores->contains($user->id)) {
            return back()->with('info', 'Você já participa desta campanha.');
        }

        $campanha->jogadores()->attach($user->id, ['status' => 'pendente']);
        $campanha->criador->notify(new SolicitarEntradaCampanha($user, $campanha));

        return back()->with('success', 'Solicitação enviada ao mestre da campanha!');
    }

    // ===================================================
    // Mestre adiciona/convida usuário
    // ===================================================
    public function adicionar(Request $request, Campanha $campanha)
    {
        $this->authorize('update', $campanha);

        $request->validate(['user_id' => 'required|exists:users,id']);
        $userId = $request->input('user_id');

        if ($campanha->jogadores->contains($userId)) {
            return back()->with('error', 'Este usuário já está na campanha.');
        }

        $campanha->jogadores()->attach($userId, ['status' => 'pendente']);
        User::findOrFail($userId)->notify(new ConviteParaCampanha($campanha, auth()->user()));

        return back()->with('success', 'Convite enviado com sucesso!');
    }

    // ===================================================
    // Mestre aprova/rejeita usuários pendentes
    // ===================================================
    public function gerenciar(Request $request, Campanha $campanha)
    {
        $this->authorize('update', $campanha);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:ativo,mestre,pendente,rejeitado',
        ]);

        $user = User::findOrFail($request->user_id);

        if ($campanha->jogadores()->where('user_id', $user->id)->exists()) {
            $campanha->jogadores()->updateExistingPivot($user->id, ['status' => $request->status]);
        } else {
            $campanha->jogadores()->attach($user->id, ['status' => $request->status]);
        }

        $user->notify(new StatusCampanhaAtualizado($campanha, $request->status));

        return back()->with('success', "Status de {$user->nome} atualizado para {$request->status}!");
    }

    // ===================================================
    // Mestre remove usuário
    // ===================================================
    public function remover(Request $request, Campanha $campanha)
    {
        $this->authorize('update', $campanha);

        $request->validate(['user_id' => 'required|exists:users,id']);
        if ($campanha->criador_id == $request->user_id) {
            return back()->with('error', 'O criador da campanha não pode ser removido.');
        }

        $campanha->jogadores()->detach($request->user_id);
        return back()->with('success', 'Usuário removido da campanha com sucesso.');
    }

    // ===================================================
    // Lista jogadores da campanha
    // ===================================================
    public function listarJogadores(Campanha $campanha)
    {
        return view('campanhas.jogadores', compact('campanha'));
    }
}
