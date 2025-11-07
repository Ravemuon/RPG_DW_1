<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Campanha;

class HomeController extends Controller
{
    // Página inicial
    public function index()
    {
        $user = Auth::user();

        // Inicializa a collection para evitar erro de variável indefinida
        $campanhas = collect();

        if ($user) {
            // Campanhas do usuário logado
            $campanhas = Campanha::whereHas('jogadores', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orWhere('criador_id', $user->id)
            ->get();
        } else {
            // Usuário não logado — últimas 5 campanhas públicas
            $campanhas = Campanha::latest()->take(5)->get();
        }

        return view('home.home', compact('campanhas'));
    }
}
