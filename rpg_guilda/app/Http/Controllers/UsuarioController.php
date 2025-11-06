<?php

namespace App\Http\Controllers;

use App\Models\User; // <- Use o model correto
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    // Form de login
    public function loginForm()
    {
        return view('usuarios.login');
    }

    // Autenticação
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'Credenciais inválidas.',
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // Form de registro
    public function registerForm()
    {
        return view('usuarios.register');
    }

    // Registro de usuário
    public function register(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email', // <- tabela correta
            'password' => 'required|min:6|confirmed',
            'tema' => 'required|in:medieval,sobrenatural,cyberpunk',
        ]);

        $user = User::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'password' => Hash::make($request->password), // <- coluna correta
            'tema' => $request->tema,
            'tipo' => 'jogador', // define padrão
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Conta criada com sucesso!');
    }

    // Perfil do usuário
    public function perfil()
    {
        $user = Auth::user();
        return view('usuarios.perfil', compact('user'));
    }

    // Listar todos usuários (opcional)
    public function index()
    {
        $users = User::all();
        return view('usuarios.index', compact('users'));
    }
}
