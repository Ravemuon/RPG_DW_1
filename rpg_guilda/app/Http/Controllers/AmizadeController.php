<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Amizade;
use App\Models\User;
use App\Models\Notificacao;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class AmizadeController extends Controller
{
    /**
     * Lista amigos e possíveis amigos
     */
    public function index()
    {
        $user = auth()->user();

        // Busca amizades aceitas
        $amizades = Amizade::where(function($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhere('friend_id', $user->id);
        })
        ->where('status', 'aceito')
        ->get();

        // Mapeia para retornar somente o "amigo"
        $amigos = $amizades->map(function ($amizade) use ($user) {
            return $amizade->user_id === $user->id
                ? $amizade->friend
                : $amizade->user;
        });

        // IDs dos amigos atuais + o próprio usuário
        $idsRelacionados = $amigos->pluck('id')->push($user->id);

        // Busca usuários que não são amigos ainda
        $possiveisAmigos = User::whereNotIn('id', $idsRelacionados)
            ->orderBy('nome')
            ->paginate(8);

        // Paginar amigos manualmente
        $amigos = new LengthAwarePaginator(
            $amigos->forPage(request('page', 1), 12),
            $amigos->count(),
            12,
            request('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Adiciona o status de amizade para possíveis amigos
        foreach ($possiveisAmigos as $usuario) {
            $amizade = Amizade::where(function ($q) use ($user, $usuario) {
                    $q->where('user_id', $user->id)
                      ->where('friend_id', $usuario->id);
                })
                ->orWhere(function ($q) use ($user, $usuario) {
                    $q->where('user_id', $usuario->id)
                      ->where('friend_id', $user->id);
                })
                ->first();

            if ($amizade) {
                if ($amizade->status === 'aceito') {
                    $usuario->amizade_status = 'aceito';
                } elseif ($amizade->user_id == $user->id) {
                    $usuario->amizade_status = 'pendente_enviado';
                } else {
                    $usuario->amizade_status = 'pendente_recebido';
                }
            } else {
                $usuario->amizade_status = 'nenhum';
            }
        }

        return view('amizades.amigos', compact('amigos', 'possiveisAmigos'));
    }

    /**
     * Aceita solicitação de amizade
     */
    public function aceitar($id)
    {
        $user = Auth::user();

        // Procura a amizade pendente, seja qual for a direção
        $amizade = Amizade::where('id', $id)
            ->where('status', 'pendente')
            ->where(function($q) use ($user) {
                $q->where('friend_id', $user->id)
                  ->orWhere('user_id', $user->id);
            })
            ->firstOrFail();

        $amizade->update(['status' => 'aceito']);

        // Notificação para o outro usuário
        $outroId = $amizade->user_id === $user->id ? $amizade->friend_id : $amizade->user_id;

        Notificacao::create([
            'usuario_id' => $outroId,
            'tipo' => 'Amizade Aceita',
            'mensagem' => "{$user->nome} aceitou sua solicitação de amizade."
        ]);

        return redirect()->back()->with('success', 'Amizade aceita com sucesso!');
    }

    /**
     * Lista amigos via model User
     */
    public function amigos()
    {
        $user = Auth::user();

        $lista = $user->todosAmigos();

        $currentPage = request()->get('page', 1);
        $perPage = 12;

        $amigos = new LengthAwarePaginator(
            $lista->forPage($currentPage, $perPage),
            $lista->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('amizades.amigos', compact('amigos'));
    }

    /**
     * Solicitações pendentes
     */
    public function pendentes()
    {
        $user = Auth::user();

        $solicitacoes = Amizade::with('user')
            ->where('friend_id', $user->id)
            ->where('status', 'pendente')
            ->paginate(10);

        return view('amizades.pendentes', compact('solicitacoes'));
    }

    /**
     * Envia solicitação de amizade
     */
    public function adicionar($friendId)
    {
        $user = Auth::user();

        if ($user->id == $friendId) {
            return redirect()->back()->with('error', 'Não é possível adicionar você mesmo.');
        }

        $destino = User::find($friendId);
        if (!$destino) {
            return redirect()->back()->with('error', 'Usuário não encontrado.');
        }

        // Verifica se já existe amizade ou solicitação
        $existe = Amizade::where(function($q) use ($user, $friendId) {
                $q->where('user_id', $user->id)->where('friend_id', $friendId);
            })
            ->orWhere(function($q) use ($user, $friendId){
                $q->where('user_id', $friendId)->where('friend_id', $user->id);
            })
            ->first();

        if ($existe) {
            return redirect()->back()->with('info', 'Solicitação já existe ou vocês já são amigos.');
        }

        DB::transaction(function () use ($user, $friendId) {
            Amizade::create([
                'user_id' => $user->id,
                'friend_id' => $friendId,
                'status' => 'pendente'
            ]);

            Notificacao::create([
                'usuario_id' => $friendId,
                'tipo' => 'Solicitação de Amizade',
                'mensagem' => "{$user->nome} enviou uma solicitação de amizade."
            ]);
        });

        return redirect()->back()->with('success', 'Solicitação enviada com sucesso.');
    }

    /**
     * Remove amizade ou recusa solicitação
     */
    public function remover($id)
    {
        $user = Auth::user();
        $amizade = Amizade::findOrFail($id);

        if ($amizade->user_id != $user->id && $amizade->friend_id != $user->id) {
            return redirect()->back()->with('error', 'Ação não autorizada.');
        }

        $amizade->delete();

        return redirect()->back()->with('success', 'Amizade ou solicitação removida.');
    }
}
