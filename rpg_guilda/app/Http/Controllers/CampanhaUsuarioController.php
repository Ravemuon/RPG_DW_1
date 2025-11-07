<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campanha;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\NotificacaoController;

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

        // Adiciona como pendente
        $campanha->jogadores()->attach($user->id, ['status' => 'pendente']);

        // Notificação para o mestre
        NotificacaoController::criarNotificacao(
            $campanha->criador_id,
            "{$user->nome} solicitou entrar na sua campanha '{$campanha->nome}'.",
            'solicitacao_entrada'
        );

        return back()->with('success', 'Solicitação enviada ao mestre da campanha!');
    }

    // ===================================================
    // Mestre adiciona/convida usuário
    // ===================================================
    public function adicionar(Request $request, Campanha $campanha)
    {
        $this->authorize('update', $campanha);

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userId = $request->input('user_id');

        if ($campanha->jogadores->contains($userId)) {
            return back()->with('error', 'Este usuário já está na campanha.');
        }

        // Adiciona como pendente
        $campanha->jogadores()->attach($userId, ['status' => 'pendente']);

        // Notificação para o usuário convidado
        NotificacaoController::criarNotificacao(
            $userId,
            "Você foi convidado para a campanha '{$campanha->nome}' por {$campanha->criador->nome}.",
            'convite_campanha'
        );

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

        // Atualiza ou adiciona pivot
        if ($campanha->jogadores()->where('user_id', $user->id)->exists()) {
            $campanha->jogadores()->updateExistingPivot($user->id, ['status' => $request->status]);
        } else {
            $campanha->jogadores()->attach($user->id, ['status' => $request->status]);
        }

        // Notificação direta
        NotificacaoController::criarNotificacao(
            $user->id,
            "Seu status na campanha '{$campanha->nome}' foi atualizado para '{$request->status}'.",
            'status_campanha'
        );

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

        NotificacaoController::criarNotificacao(
            $request->user_id,
            "Você foi removido da campanha '{$campanha->nome}'.",
            'remocao_campanha'
        );

        return back()->with('success', 'Usuário removido da campanha com sucesso.');
    }

    // ===================================================
    // Lista jogadores da campanha
    // ===================================================
    public function listarJogadores(Campanha $campanha)
    {
        $jogadores = $campanha->jogadores->sortBy(function($user) {
            return match($user->pivot->status) {
                'ativo' => 1,
                'pendente' => 2,
                'rejeitado' => 3,
                default => 4,
            };
        });

        return view('campanhas.jogadores', compact('campanha', 'jogadores'));
    }

    public function adicionarAjax(Request $request, Campanha $campanha)
    {
        $this->authorize('update', $campanha);

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userId = $request->user_id;
        $usuario = User::findOrFail($userId);

        if ($campanha->jogadores->contains($userId)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Este usuário já participa da campanha.'
            ], 400);
        }

        $campanha->jogadores()->attach($userId, ['status' => 'pendente']);

        // Notificação
        NotificacaoController::criarNotificacao(
            $userId,
            "Você foi convidado para a campanha '{$campanha->nome}' por {$campanha->criador->nome}.",
            'convite_campanha'
        );

        return response()->json([
            'status' => 'success',
            'message' => "Convite enviado para {$usuario->nome} com sucesso!"
        ]);
    }

}
