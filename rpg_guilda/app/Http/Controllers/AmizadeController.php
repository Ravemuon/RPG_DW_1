<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Amizade;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AmizadeController extends Controller
{
    // Enviar solicitação
    public function enviarSolicitacao($friend_id)
    {
        $user = Auth::user();

        // Verifica se já existe amizade
        if (Amizade::where([
            ['user_id', $user->id],
            ['friend_id', $friend_id]
        ])->exists()) {
            return back()->with('error', 'Você já enviou solicitação para este usuário.');
        }

        Amizade::create([
            'user_id' => $user->id,
            'friend_id' => $friend_id,
        ]);

        return back()->with('success', 'Solicitação enviada!');
    }

    // Aceitar solicitação
    public function aceitarSolicitacao($id)
    {
        $amizade = Amizade::findOrFail($id);
        $amizade->status = 'aceita';
        $amizade->save();

        return back()->with('success', 'Amizade aceita!');
    }

    // Recusar solicitação
    public function recusarSolicitacao($id)
    {
        $amizade = Amizade::findOrFail($id);
        $amizade->status = 'recusada';
        $amizade->save();

        return back()->with('success', 'Solicitação recusada!');
    }
}
