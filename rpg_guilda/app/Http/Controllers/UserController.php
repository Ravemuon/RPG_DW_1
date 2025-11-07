<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notificacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // ===================================================
    // 🔹 Autenticação
    // ===================================================
    public function loginForm()
    {
        $temas = User::TEMAS;
        return view('auth.login', compact('temas'));
    }

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

        return back()->withErrors(['email' => 'Credenciais inválidas.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logout realizado com sucesso!');
    }

    public function registerForm()
    {
        $temas = User::TEMAS;
        return view('auth.register', compact('temas'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'tema' => 'required|in:' . implode(',', User::TEMAS),
        ]);

        // Gera username automático
        $usernameBase = strtolower(preg_replace('/\s+/', '', $request->nome));
        $username = $usernameBase;
        $contador = 1;

        // Garante username único
        while(User::where('username', $username)->exists()) {
            $username = $usernameBase . $contador;
            $contador++;
        }

        $user = User::create([
            'nome' => $request->nome,
            'username' => $username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tema' => $request->tema,
            'papel' => 'jogador',
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Bem-vindo(a)! Conta criada com sucesso!');
    }


    // ===================================================
    // 🔹 Perfil do Usuário
    // ===================================================
    public function perfil(User $user = null)
    {
        $user = $user ?? Auth::user();
        $personagemCount = $user->personagens()->count();

        // Busca campanhas do usuário
        $campanhas = $user->campanhas()->get();

        return view('users.perfil', compact('user', 'personagemCount', 'campanhas'));
    }


    public function edit(User $user = null)
    {   
        $user = $user ?? Auth::user();

        if (!Auth::user()->canEdit($user)) {
            abort(403, 'Acesso não autorizado.');
        }

        $temas = User::TEMAS;
        return view('users.edit', compact('user', 'temas'));
    }

    public function update(Request $request, User $user = null)
    {
        $user = $user ?? Auth::user();

        if (!Auth::user()->canEdit($user)) {
            abort(403, 'Acesso não autorizado.');
        }

        $request->validate([
            'nome' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'biografia' => 'nullable|string|max:1000',
            'tema' => 'required|in:' . implode(',', User::TEMAS),
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed',
        ]);

        if ($request->filled('new_password') && !Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Senha atual incorreta.']);
        }

        $user->update([
            'nome' => $request->nome,
            'email' => $request->email,
            'biografia' => $request->biografia,
            'tema' => $request->tema,
            'password' => $request->filled('new_password') ? Hash::make($request->new_password) : $user->password,
        ]);

        return redirect()->route('users.perfil')->with('success', 'Perfil atualizado com sucesso!');
    }

    // ===================================================
    // 🔹 Alterar Tema
    // ===================================================
    public function atualizarTema(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'tema' => ['required', 'string', 'in:' . implode(',', User::TEMAS)],
        ]);

        $user->tema = $request->tema;
        $user->save();

        return redirect()->back()->with('success', 'Tema atualizado com sucesso!');
    }



    // ===================================================
    // 🔹 Lista / Amigos
    // ===================================================
    public function index(Request $request)
    {
        $search = $request->search;
        $userId = Auth::id();

        $users = User::where('id', '!=', $userId)
            ->when($search, fn($q) =>
                $q->where('nome', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
            )->get();

        return view('users.index', compact('users', 'search'));
    }

    public function friends()
    {
        $friends = Auth::user()->todosAmigos();
        return view('amizades.amigos', compact('friends'));
    }

    public function pendingRequests()
    {
        $pending = Auth::user()->receivedRequests()->where('status', 'pending')->get();
        return view('amizades.index', compact('pending'));
    }

    // ===================================================
    // 🔹 Notificações
    // ===================================================
    public function notifications()
    {
        $user = Auth::user();
        $notifications = $user->notificacoes()->latest()->get();
        return view('notificacoes.index', compact('notifications'));
    }

    public function markNotificationAsRead($id)
    {
        $notification = Notificacao::findOrFail($id);

        if ($notification->usuario_id !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }

        $notification->update(['read' => true]);

        return back()->with('success', 'Notificação marcada como lida.');
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->notificacoes()->where('read', false)->update(['read' => true]);

        return back()->with('success', 'Todas as notificações foram marcadas como lidas.');
    }

    // ===================================================
    // 🔹 Home
    // ===================================================
    public function home()
    {
        return view('home.home');
    }
    public function uploadImagem(Request $request, $tipo)
    {
        $user = auth()->user();

        if (!in_array($tipo, ['avatar', 'banner'])) {
            abort(400, 'Tipo inválido.');
        }

        $request->validate([
            'arquivo' => 'required|image|max:2048', // max 2MB
        ]);

        $file = $request->file('arquivo');
        $nomeArquivo = $tipo . '_' . $user->id . '.' . $file->getClientOriginalExtension();
        $caminho = $file->storeAs('uploads/users', $nomeArquivo, 'public');

        // Atualiza usuário
        $user->$tipo = 'storage/' . $caminho;
        $user->save();

        return back()->with('success', ucfirst($tipo) . ' atualizado com sucesso!');
    }

}
