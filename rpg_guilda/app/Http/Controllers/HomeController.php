<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Campanha;

class HomeController extends Controller
{
    // Mostra a página inicial com campanhas do usuário ou últimas campanhas públicas
    public function index()
    {
        $user = Auth::user();
        $campanhas = collect();

        if ($user) {
            // Busca campanhas em que o usuário participa ou que ele criou
            $campanhas = Campanha::whereHas('jogadores', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orWhere('criador_id', $user->id)
            ->get();
        } else {
            // Usuário não logado vê as 5 últimas campanhas criadas
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
