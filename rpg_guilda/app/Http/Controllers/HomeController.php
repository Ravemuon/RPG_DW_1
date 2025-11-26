<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Campanha;

class HomeController extends Controller
{
    // Mostra a página inicial com campanhas relacionadas ao usuário ou as últimas campanhas públicas
    public function index()
    {
        $user = Auth::user();
        $campanhas = collect();

        if ($user) {
            // Busca campanhas onde o usuário participa ou é o criador
            $campanhas = Campanha::whereHas('jogadores', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orWhere('criador_id', $user->id)
            ->get();
        } else {
            // Usuário não logado vê as últimas 5 campanhas criadas
            $campanhas = Campanha::latest()->take(5)->get();
        }

        return view('home.home', compact('campanhas'));
    }

    // Abre a página do dicionário
    public function dicionario()
    {
        return view('home.dicionario');
    }
}
