<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Campanha;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Pega todas as campanhas que o usuário participa ou criou
        $campanhasUsuario = Campanha::whereHas('jogadores', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->orWhere('criador_id', $user->id)
          ->get();

        return view('portal', compact('campanhasUsuario'));
    }
}
    