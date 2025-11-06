<?php

namespace App\Http\Controllers;

use App\Models\Administrador;
use Illuminate\Http\Request;

class AdministradorController extends Controller
{
    public function index()
    {
        $administradores = Administrador::all();
        return view('administradores.index', compact('administradores'));
    }
}
