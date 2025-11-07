<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Amizade;
use App\Models\User;
use App\Models\Notificacao;
use Illuminate\Support\Facades\Auth;

class AmizadeController extends Controller
{
    /**
     * Lista todas as amizades do usuário logado
     */
    public function index()
    {
        $user = Auth::user();

        $amizades = Amizade::with('friend')
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('friend_id', $user->id);
            })
            ->get();

        return view('amizades.index', compact('amizades'));
    }

    /**
     * Lista solicitações pendentes recebidas
     */
    public function pendentes()
    {
        $user = Auth::user();

        $solicitacoes = Amizade::with('user')
            ->where('friend_id', $user->id)
            ->where('status', 'pendente')
            ->get();

        return view('amizades.pendentes', compact('solicitacoes'));
    }

    /**
     * Envia solicitação de amizade
     */
    public function enviar($friendId)
    {
        $user = Auth::user();

        if ($user->id == $friendId) {
            return redirect()->back()->with('error', 'Não é possível adicionar você mesmo.');
        }

        // Verifica se já existe
        $existe = Amizade::where(function($q) use ($user, $friendId){
            $q->where('user_id', $user->id)->where('friend_id', $friendId);
        })->orWhere(function($q) use ($user, $friendId){
            $q->where('user_id', $friendId)->where('friend_id', $user->id);
        })->first();

        if ($existe) {
            return redirect()->back()->with('info', 'Solicitação já existe ou vocês já são amigos.');
        }

        $amizade = Amizade::create([
            'user_id' => $user->id,
            'friend_id' => $friendId,
            'status' => 'pendente'
        ]);

        // Notificação para o destinatário
        Notificacao::create([
            'usuario_id' => $friendId,
            'tipo' => 'Solicitação de Amizade',
            'mensagem' => "{$user->nome} enviou uma solicitação de amizade."
        ]);

        return redirect()->back()->with('success', 'Solicitação enviada com sucesso.');
    }

    /**
     * Aceita solicitação
     */
    public function aceitar($id)
    {
        $user = Auth::user();
        $amizade = Amizade::findOrFail($id);

        if ($amizade->friend_id != $user->id) {
            return redirect()->back()->with('error', 'Ação não autorizada.');
        }

        $amizade->status = 'aceita';
        $amizade->save();

        // Notificação para o remetente
        Notificacao::create([
            'usuario_id' => $amizade->user_id,
            'tipo' => 'Amizade Aceita',
            'mensagem' => "{$user->nome} aceitou sua solicitação de amizade."
        ]);

        return redirect()->back()->with('success', 'Amizade aceita.');
    }

    /**
     * Recusa ou remove amizade
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
