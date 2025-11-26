<?php

namespace App\Http\Controllers;

use App\Models\Amizade;
use App\Models\User;
use App\Models\Notificacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class AmizadeController extends Controller
{
    /**
     * Exibe o dashboard de amizades, incluindo a lista paginada de amigos e sugestões.
     */
    public function index()
    {
        $user = Auth::user();

        // Carrega as amizades aceitas do usuário
        $amizades = Amizade::with(['user', 'friend'])
            ->where(fn($q) => $q->where('user_id', $user->id)->orWhere('friend_id', $user->id))
            ->where('status', 'aceito')
            ->get();

        // Mapeia para obter a lista de objetos User (amigos)
        $amigos = $amizades->map(
            fn($a) => $a->user_id === $user->id ? $a->friend : $a->user
        );

        // Paginação manual da Collection de amigos
        $page = request('page', 1);
        $perPage = 12;

        $amigos = new LengthAwarePaginator(
            $amigos->forPage($page, $perPage),
            $amigos->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Identifica IDs relacionados (amigos e o próprio usuário) para exclusão nas sugestões
        $idsRelacionados = $amizades->pluck('user_id')
            ->merge($amizades->pluck('friend_id'))
            ->merge([$user->id])
            ->unique();

        // Carrega sugestões (Usuários que não são amigos/pendentes)
        $sugestoes = User::whereNotIn('id', $idsRelacionados)
            ->inRandomOrder()
            ->take(4)
            ->get();

        // Anexa o status de amizade a cada sugestão antes de exibir
        $sugestoes = $sugestoes->map(function ($usuario) use ($user) {
            $amizade = Amizade::where(function ($q) use ($user, $usuario) {
                $q->where('user_id', $user->id)->where('friend_id', $usuario->id);
            })
            ->orWhere(function ($q) use ($user, $usuario) {
                $q->where('user_id', $usuario->id)->where('friend_id', $user->id);
            })
            ->first();

            $usuario->status_amizade = $amizade ? $amizade->status : 'nenhum';
            return $usuario;
        });

        return view('amizades.index', compact('amigos', 'sugestoes'));
    }

    /**
     * Exibe todas as solicitações de amizade pendentes (recebidas e enviadas).
     */
    public function pendentes(Request $request)
    {
        $usuarioId = auth()->id();
        $query = $request->get('q');

        // Solicitações recebidas
        $recebidas = Amizade::with('user')
            ->where('friend_id', $usuarioId)
            ->where('status', 'pendente')
            ->when($query, fn($q) =>
                $q->whereHas('user', fn($s) =>
                    $s->where('nome', 'like', "%$query%")
                    ->orWhere('username', 'like', "%$query%")
                )
            )
            ->get()
            ->map(fn($a) => [
                'amizade_id' => $a->id, // ID da relação para uso na rota
                'tipo' => 'recebida',
                'usuario' => $a->user
            ]);

        // Solicitações enviadas
        $enviadas = Amizade::with('friend')
            ->where('user_id', $usuarioId)
            ->where('status', 'pendente')
            ->when($query, fn($q) =>
                $q->whereHas('friend', fn($s) =>
                    $s->where('nome', 'like', "%$query%")
                    ->orWhere('username', 'like', "%$query%")
                )
            )
            ->get()
            ->map(fn($a) => [
                'amizade_id' => $a->id, // ID da relação para uso na rota
                'tipo' => 'enviada',
                'usuario' => $a->friend
            ]);

        // Combina e reindexa as solicitações
        $pendentes = collect($recebidas)->concat($enviadas)->values();

        return view('amizades.pendentes', compact('pendentes', 'query'));
    }

    /**
     * Aceita uma solicitação de amizade.
     * @param int $id ID da relação de amizade.
     */
    public function aceitar($id)
    {
        $user = Auth::user();

        $amizade = Amizade::where('id', $id)
            ->where('status', 'pendente')
            // Garante que o usuário logado é o destinatário (friend_id)
            ->where('friend_id', $user->id)
            ->first();

        if (!$amizade) {
            return back()->with('error', 'Solicitação inválida.');
        }

        $amizade->update(['status' => 'aceito']);

        // Notifica o usuário que enviou a solicitação
        Notificacao::create([
            'usuario_id' => $amizade->user_id,
            'tipo' => 'Amizade Aceita',
            'mensagem' => "{$user->nome} aceitou sua solicitação de amizade.",
        ]);

        return back()->with('success', 'Solicitação aceita com sucesso!');
    }

    /**
     * Remove ou cancela uma amizade/solicitação.
     * Tenta buscar pelo ID da relação ou pelo ID do amigo (fallback).
     * @param int $id ID da amizade (relação) ou do amigo (usuário).
     */
    public function remover($id)
    {
        $user = Auth::user();

        // 1. Tenta buscar pelo ID da RELAÇÃO de amizade
        $amizade = Amizade::where('id', $id)
            ->where(fn($q) => $q->where('user_id', $user->id)->orWhere('friend_id', $user->id))
            ->first();

        if (!$amizade) {
            // 2. Tenta buscar pelo ID do AMIGO (assumindo que $id é o ID do usuário)
            $amizade = Amizade::where(fn($q) => $q->where('user_id', $user->id)->where('friend_id', $id))
            ->orWhere(fn($q) => $q->where('friend_id', $user->id)->where('user_id', $id))
            ->first();
        }

        if (!$amizade) {
            return back()->with('error', 'Amizade ou solicitação não encontrada.');
        }

        $amizade->delete();

        return back()->with('success', 'Amizade ou solicitação removida com sucesso.');
    }

    /**
     * Permite ao usuário procurar outros usuários.
     * Anexa o status de amizade a cada resultado.
     */
    public function procurar(Request $request)
    {
        $query = $request->get('q');
        $usuarios = collect();
        $authUser = Auth::user();

        if ($query && $authUser) {
            $usuarios = User::where('id', '!=', $authUser->id)
                ->where(function($q) use ($query) {
                    $q->where('id', $query)
                      ->orWhere('nome', 'like', "%$query%")
                      ->orWhere('username', 'like', "%$query%");
                })
                ->take(15)
                ->get();

            // Anexa o STATUS DE AMIZADE a cada usuário retornado
            $usuarios = $usuarios->map(function ($usuario) use ($authUser) {

                $amizade = Amizade::where(function ($q) use ($authUser, $usuario) {
                    $q->where('user_id', $authUser->id)->where('friend_id', $usuario->id);
                })
                ->orWhere(function ($q) use ($authUser, $usuario) {
                    $q->where('user_id', $usuario->id)->where('friend_id', $authUser->id);
                })
                ->first();

                $usuario->status_amizade = $amizade ? $amizade->status : 'nenhum';
                return $usuario;
            });
        }

        return view('amizades.procurar', compact('usuarios', 'query'));
    }

    /**
     * Exibe a lista paginada de amigos e o contador de solicitações pendentes.
     */
    public function amigos()
    {
        $user = Auth::user();

        $amizades = Amizade::with(['user', 'friend'])
            ->where(fn($q) => $q->where('user_id', $user->id)->orWhere('friend_id', $user->id))
            ->where('status', 'aceito')
            ->get();

        // Mapeia para obter o objeto User do amigo (o que não for o usuário logado)
        $lista = $amizades->map(
            fn($a) => $a->user_id === $user->id ? $a->friend : $a->user
        );

        // Paginação manual
        $page = request('page', 1);
        $perPage = 12;

        $amigos = new LengthAwarePaginator(
            $lista->forPage($page, $perPage),
            $lista->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Contagem de solicitações recebidas
        $solicitacoesPendentes = Amizade::where('friend_id', $user->id)
            ->where('status', 'pendente')
            ->get();

        return view('amizades.amigos', compact('amigos', 'solicitacoesPendentes'));
    }

    /**
     * Envia uma nova solicitação de amizade.
     * @param int $id ID do usuário a ser adicionado.
     */
    public function adicionar($id)
    {
        $user = Auth::user();
        if ($user->id == $id) {
            return back()->with('error', 'Você não pode adicionar a si mesmo.');
        }

        $amigo = User::findOrFail($id);

        // Verifica se a relação já existe em qualquer direção
        $existe = Amizade::where(fn($q) =>
            $q->where('user_id', $user->id)->where('friend_id', $amigo->id)
        )
        ->orWhere(fn($q) =>
            $q->where('user_id', $amigo->id)->where('friend_id', $user->id)
        )
        ->exists();

        if ($existe) {
            return back()->with('info', 'Vocês já são amigos ou há uma solicitação pendente.');
        }

        Amizade::create([
            'user_id' => $user->id,
            'friend_id' => $amigo->id,
            'status' => 'pendente',
        ]);

        // Cria notificação para o destinatário
        Notificacao::create([
            'usuario_id' => $amigo->id,
            'tipo' => 'Solicitação de amizade',
            'mensagem' => "{$user->nome} enviou uma solicitação de amizade.",
        ]);

        return back()->with('success', 'Solicitação de amizade enviada!');
    }

    /**
     * Exibe o perfil público de um usuário, calculando o status de amizade.
     * @param int $id ID do usuário cujo perfil será exibido.
     */
    public function perfilPublico($id)
    {
        $user = User::with(['personagens', 'campanhas'])->findOrFail($id);
        $authUser = Auth::user();

        // Busca a relação de amizade entre os dois usuários, em qualquer direção
        $amizade = Amizade::where(fn($q) =>
            $q->where('user_id', $authUser->id)->where('friend_id', $user->id)
        )
        ->orWhere(fn($q) =>
            $q->where('user_id', $user->id)->where('friend_id', $authUser->id)
        )
        ->first();

        $ehAmigo = $amizade && $amizade->status === 'aceito';
        $solicitacaoPendente = $amizade && $amizade->status === 'pendente';

        $amizadeObjeto = $amizade;

        return view('amizades.perfilpublico', [
            'user' => $user,
            'ehAmigo' => $ehAmigo,
            'solicitacaoPendente' => $solicitacaoPendente,
            'amizadeId' => $amizadeObjeto->id ?? null
        ]);
    }
}
