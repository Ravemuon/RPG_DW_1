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
    public function index()
    {
        $user = Auth::user();

        // Carrega amizades aceitas
        $amizades = Amizade::with(['user', 'friend'])
            ->where(fn($q) => $q->where('user_id', $user->id)->orWhere('friend_id', $user->id))
            ->where('status', 'aceito')
            ->get();

        // Lista de amigos
        $amigos = $amizades->map(fn($a) => $a->user_id === $user->id ? $a->friend : $a->user);

        // Paginação
        $page = request('page', 1);
        $perPage = 12;
        $amigos = new LengthAwarePaginator(
            $amigos->forPage($page, $perPage),
            $amigos->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // IDs para excluir nas sugestões
        $idsRelacionados = $amizades->pluck('user_id')
            ->merge($amizades->pluck('friend_id'))
            ->merge([$user->id])
            ->unique();

        // Sugestões de usuários
        $sugestoes = User::whereNotIn('id', $idsRelacionados)
            ->inRandomOrder()
            ->take(4)
            ->get()
            ->map(function ($usuario) use ($user) {
                $amizade = Amizade::where(fn($q) => $q->where('user_id', $user->id)->where('friend_id', $usuario->id))
                    ->orWhere(fn($q) => $q->where('user_id', $usuario->id)->where('friend_id', $user->id))
                    ->first();

                $usuario->status_amizade = $amizade ? $amizade->status : 'nenhum';
                return $usuario;
            });

        return view('amizades.index', compact('amigos', 'sugestoes'));
    }

    public function pendentes(Request $request)
    {
        $usuarioId = auth()->id();
        $query = $request->get('q');

        // Solicitações recebidas
        $recebidas = Amizade::with('user')
            ->where('friend_id', $usuarioId)
            ->where('status', 'pendente')
            ->when($query, fn($q) =>
                $q->whereHas('user', fn($s) => $s->where('nome', 'like', "%$query%")
                    ->orWhere('username', 'like', "%$query%"))
            )
            ->get()
            ->map(fn($a) => ['amizade_id' => $a->id, 'tipo' => 'recebida', 'usuario' => $a->user]);

        // Solicitações enviadas
        $enviadas = Amizade::with('friend')
            ->where('user_id', $usuarioId)
            ->where('status', 'pendente')
            ->when($query, fn($q) =>
                $q->whereHas('friend', fn($s) => $s->where('nome', 'like', "%$query%")
                    ->orWhere('username', 'like', "%$query%"))
            )
            ->get()
            ->map(fn($a) => ['amizade_id' => $a->id, 'tipo' => 'enviada', 'usuario' => $a->friend]);

        $pendentes = collect($recebidas)->concat($enviadas)->values();

        return view('amizades.pendentes', compact('pendentes', 'query'));
    }

    public function aceitar($id)
    {
        $user = Auth::user();

        $amizade = Amizade::where('id', $id)
            ->where('status', 'pendente')
            ->where('friend_id', $user->id)
            ->first();

        if (!$amizade) return back()->with('error', 'Solicitação inválida.');

        $amizade->update(['status' => 'aceito']);

        // Notificação ao remetente
        Notificacao::create([
            'usuario_id' => $amizade->user_id,
            'tipo' => 'Amizade Aceita',
            'mensagem' => "{$user->nome} aceitou sua solicitação de amizade.",
        ]);

        return back()->with('success', 'Solicitação aceita com sucesso!');
    }

    public function remover($id)
    {
        $user = Auth::user();

        $amizade = Amizade::where('id', $id)
            ->where(fn($q) => $q->where('user_id', $user->id)->orWhere('friend_id', $user->id))
            ->first();

        if (!$amizade) {
            $amizade = Amizade::where(fn($q) => $q->where('user_id', $user->id)->where('friend_id', $id))
                ->orWhere(fn($q) => $q->where('friend_id', $user->id)->where('user_id', $id))
                ->first();
        }

        if (!$amizade) return back()->with('error', 'Amizade ou solicitação não encontrada.');

        $amizade->delete();

        return back()->with('success', 'Amizade ou solicitação removida com sucesso.');
    }

    public function procurar(Request $request)
    {
        $query = $request->get('q');
        $usuarios = collect();
        $authUser = Auth::user();

        if ($query && $authUser) {
            $usuarios = User::where('id', '!=', $authUser->id)
                ->where(fn($q) => $q->where('id', $query)
                    ->orWhere('nome', 'like', "%$query%")
                    ->orWhere('username', 'like', "%$query%"))
                ->take(15)
                ->get()
                ->map(function ($usuario) use ($authUser) {
                    $amizade = Amizade::where(fn($q) => $q->where('user_id', $authUser->id)->where('friend_id', $usuario->id))
                        ->orWhere(fn($q) => $q->where('user_id', $usuario->id)->where('friend_id', $authUser->id))
                        ->first();

                    $usuario->status_amizade = $amizade ? $amizade->status : 'nenhum';
                    return $usuario;
                });
        }

        return view('amizades.procurar', compact('usuarios', 'query'));
    }

    public function amigos()
    {
        $user = Auth::user();

        $amizades = Amizade::with(['user', 'friend'])
            ->where(fn($q) => $q->where('user_id', $user->id)->orWhere('friend_id', $user->id))
            ->where('status', 'aceito')
            ->get();

        $lista = $amizades->map(fn($a) => $a->user_id === $user->id ? $a->friend : $a->user);

        $page = request('page', 1);
        $perPage = 12;

        $amigos = new LengthAwarePaginator(
            $lista->forPage($page, $perPage),
            $lista->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $solicitacoesPendentes = Amizade::where('friend_id', $user->id)
            ->where('status', 'pendente')
            ->get();

        return view('amizades.amigos', compact('amigos', 'solicitacoesPendentes'));
    }

    public function adicionar($id)
    {
        $user = Auth::user();
        if ($user->id == $id) return back()->with('error', 'Você não pode adicionar a si mesmo.');

        $amigo = User::findOrFail($id);

        $existe = Amizade::where(fn($q) => $q->where('user_id', $user->id)->where('friend_id', $amigo->id))
            ->orWhere(fn($q) => $q->where('user_id', $amigo->id)->where('friend_id', $user->id))
            ->exists();

        if ($existe) return back()->with('info', 'Vocês já são amigos ou há uma solicitação pendente.');

        Amizade::create(['user_id' => $user->id, 'friend_id' => $amigo->id, 'status' => 'pendente']);

        Notificacao::create([
            'usuario_id' => $amigo->id,
            'tipo' => 'Solicitação de amizade',
            'mensagem' => "{$user->nome} enviou uma solicitação de amizade.",
        ]);

        return back()->with('success', 'Solicitação de amizade enviada!');
    }

    public function perfilPublico($id)
    {
        $user = User::with(['personagens', 'campanhas'])->findOrFail($id);
        $authUser = Auth::user();

        $amizade = Amizade::where(fn($q) => $q->where('user_id', $authUser->id)->where('friend_id', $user->id))
            ->orWhere(fn($q) => $q->where('user_id', $user->id)->where('friend_id', $authUser->id))
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
