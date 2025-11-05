<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function loginForm()
    {
        return view('usuarios.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'senha' => 'required',
        ]);

        $usuario = Usuario::where('email', $data['email'])->where('senha', $data['senha'])->first();

        if ($usuario) {
            session(['usuario' => $usuario]);
            return redirect()->route('dashboard');
        }

        return back()->with('error', 'Usuário ou senha inválidos.');
    }

    public function logout()
    {
        session()->forget('usuario');
        return redirect()->route('login');
    }
}

