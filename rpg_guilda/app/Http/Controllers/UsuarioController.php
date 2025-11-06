<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Arquivo;

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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'tema' => 'required|in:medieval,sobrenatural,cyberpunk',
        ]);

        $user = User::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tema' => $request->tema,
            'tipo' => 'jogador',
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Conta criada com sucesso!');
    }

    // Perfil do usuário
    public function perfil()
    {
        $usuario = Auth::user();

        // Contar personagens
        $personagemCount = $usuario->personagens()->count();

        // Buscar campanhas do usuário via pivot
        $campanhas = $usuario->campanhas()->get();

        return view('usuarios.perfil', compact('usuario', 'personagemCount', 'campanhas'));
    }

    // Listar todos usuários (opcional)
    public function index()
    {
        $users = User::all();
        return view('usuarios.index', compact('users'));
    }

    // Atualizar perfil
    public function update(Request $request)
    {
        $usuario = Auth::user();

        $request->validate([
            'nome' => 'required|string|max:100',
            'tema' => 'required|in:medieval,sobrenatural,cyberpunk',
            'current_password' => 'required',
            'new_password' => 'nullable|min:6|confirmed',
        ]);

        // Verifica senha atual
        if (!Hash::check($request->current_password, $usuario->password)) {
            return back()->withErrors(['current_password' => 'Senha atual incorreta']);
        }

        $usuario->nome = $request->nome;
        $usuario->tema = $request->tema;

        // Atualiza senha apenas se fornecida
        if ($request->new_password) {
            $usuario->password = Hash::make($request->new_password);
        }

        $usuario->save();

        return redirect()->route('perfil')->with('success', 'Perfil atualizado com sucesso!');
    }
    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        return view('usuarios.edit', compact('usuario'));
    }

    public function uploadBanner(Request $request, $id)
    {
        $request->validate([
            'banner_arquivo' => 'required|image|max:2048',
        ]);

        $usuario = User::findOrFail($id);

        if ($request->hasFile('banner_arquivo')) {
            $path = $request->file('banner_arquivo')->store('banners', 'public');
            $usuario->banner()->updateOrCreate([], ['caminho' => $path]);
        }

        return back()->with('success', 'Banner atualizado!');
    }


    public function uploadPerfil(Request $request, $id)
    {
        $request->validate([
            'perfil_arquivo' => 'required|image|max:2048',
        ]);

        $usuario = User::findOrFail($id);

        if ($request->hasFile('perfil_arquivo')) {
            $path = $request->file('perfil_arquivo')->store('perfis', 'public');
            $usuario->perfil()->updateOrCreate([], ['caminho' => $path]);
        }

        return back()->with('success', 'Avatar atualizado!');
    }



}
