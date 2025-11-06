<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SistemaController extends Controller
{
    // Exibir a lista de sistemas
    public function index()
    {
        // Aqui você pode passar dados sobre sistemas, por enquanto vazio
        $sistemas = [
            ['nome' => 'D&D 5e', 'descricao' => 'Sistema clássico de RPG de fantasia.'],
            ['nome' => 'Pathfinder', 'descricao' => 'Sistema baseado em D&D 3.5 com mais complexidade.'],
            ['nome' => 'Cyberpunk Red', 'descricao' => 'RPG futurista e cyberpunk.'],
        ];

        return view('sistemas.index', compact('sistemas'));
    }
}
