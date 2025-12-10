<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notificacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // ===================================================
    // Autenticação
    // ===================================================

    // Exibe o formulário de login
    public function loginForm()
    {
        $temas = User::TEMAS;
        return view('auth.login', compact('temas'));
    }

    public function isMestre(): bool
    {
        return $this->papel === 'mestre';
    }

    // Realiza login do usuário
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

    // Realiza logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logout realizado com sucesso.');
    }

    // Exibe o formulário de registro
    public function registerForm()
    {
        $temas = User::TEMAS;
        return view('auth.register', compact('temas'));
    }

    // Registra novo usuário
    public function register(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'tema' => 'required|in:' . implode(',', User::TEMAS),
        ]);

        // Gera username único
        $usernameBase = strtolower(preg_replace('/\s+/', '', $request->nome));
        $username = $usernameBase;
        $contador = 1;
        while (User::where('username', $username)->exists()) {
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

        return redirect()->route('home')->with('success', 'Conta criada com sucesso.');
    }

    // ===================================================
    // Perfil do usuário
    // ===================================================

    // Exibe perfil do usuário logado ou outro usuário
    public function perfil(User $user = null)
    {
        $user = $user ?? Auth::user();
        $personagemCount = $user->personagens()->count();
        $campanhas = $user->campanhas()->get();

        return view('users.perfil', compact('user', 'personagemCount', 'campanhas'));
    }

    // Exibe formulário de edição do perfil
    public function edit(User $user = null)
    {
        $user = $user ?? Auth::user();
        if (!Auth::user()->canEdit($user)) abort(403, 'Acesso não autorizado.');

        $temas = User::TEMAS;
        return view('users.edit', compact('user', 'temas'));
    }

    // Atualiza perfil do usuário
    public function update(Request $request, User $user = null)
    {
        $user = $user ?? Auth::user();
        if (!Auth::user()->canEdit($user)) abort(403, 'Acesso não autorizado.');

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

        return redirect()->route('usuarios.perfil')->with('success', 'Perfil atualizado com sucesso.');
    }

    // Atualiza tema do usuário
    public function atualizarTema(Request $request)
    {
        $user = auth()->user();
        $request->validate(['tema' => ['required', 'string', 'in:' . implode(',', User::TEMAS)]]);

        $user->tema = $request->tema;
        $user->save();

        return redirect()->back()->with('success', 'Tema atualizado com sucesso.');
    }

    // ===================================================
    // Usuários / Amigos
    // ===================================================

    // Lista todos os usuários exceto o logado
    public function index(Request $request)
    {
        $search = $request->search;
        $userId = Auth::id();

        $users = User::where('id', '!=', $userId)
            ->when($search, fn($q) =>
                $q->where('nome', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
            )
            ->get();

        return view('users.index', compact('users', 'search'));
    }

    // Lista amigos do usuário
    public function friends()
    {
        $friends = Auth::user()->todosAmigos()->get();
        return view('amizades.amigos', compact('friends'));
    }

    // Lista solicitações de amizade pendentes
    public function pendingRequests()
    {
        $pending = Auth::user()->amizadesRecebidas()->where('status', 'pendente')->get();
        return view('amizades.index', compact('pending'));
    }

    // ===================================================
    // Notificações
    // ===================================================

    // Lista todas as notificações
    public function notifications()
    {
        $user = Auth::user();
        $notifications = $user->notificacoes()->latest()->get();
        return view('notificacoes.index', compact('notifications'));
    }

    // Marca notificação como lida
    public function markNotificationAsRead($id)
    {
        $notification = Notificacao::findOrFail($id);
        if ($notification->usuario_id !== Auth::id()) abort(403, 'Acesso negado.');

        $notification->update(['read' => true]);

        return back()->with('success', 'Notificação marcada como lida.');
    }

    // Marca todas as notificações como lidas
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->notificacoes()->where('read', false)->update(['read' => true]);

        return back()->with('success', 'Todas as notificações foram marcadas como lidas.');
    }

    // ===================================================
    // Home
    // ===================================================

    public function home()
    {
        return view('home.home');
    }

    // ===================================================
    // Upload de imagem
    // ===================================================

    // Realiza upload de avatar ou banner
    public function uploadImagem(Request $request, $tipo)
    {
        $user = auth()->user();
        if (!in_array($tipo, ['avatar', 'banner'])) {
            return back()->withErrors(['arquivo' => 'Tipo de upload inválido.'])->withInput();
        }

        $request->validate(['arquivo' => 'required|image|max:2048']);

        $file = $request->file('arquivo');
        $nomeArquivo = $tipo . '_' . $user->id . '.' . $file->getClientOriginalExtension();
        $diretorio = "users/{$tipo}s";

        if ($user->$tipo && Storage::disk('public')->exists($user->$tipo)) {
            Storage::disk('public')->delete($user->$tipo);
        }

        $caminho = Storage::disk('public')->putFileAs($diretorio, $file, $nomeArquivo);

        $user->$tipo = $caminho;
        $user->save();

        return back()->with('success', ucfirst($tipo) . ' atualizado com sucesso.');
    }

    // ===================================================
    // Perfil público de outro usuário
    // ===================================================

    public function perfilPublico($id)
    {
        $usuario = User::findOrFail($id);
        $user = auth()->user();
        if ($usuario->id === $user->id) return redirect()->route('usuarios.perfil');

        $amizade = \App\Models\Amizade::where(function ($q) use ($user, $usuario) {
            $q->where('user_id', $user->id)->where('friend_id', $usuario->id);
        })->orWhere(function ($q) use ($user, $usuario) {
            $q->where('user_id', $usuario->id)->where('friend_id', $user->id);
        })->first();

        $status = 'nenhum';
        $amizadeId = null;

        if ($amizade) {
            if ($amizade->status === 'aceita') $status = 'amigo';
            elseif ($amizade->status === 'pendente') {
                $status = $amizade->friend_id === $user->id ? 'aguardando' : 'pendente';
                $amizadeId = $amizade->id;
            }
        }

        return view('amizades.perfilpublico', compact('usuario', 'status', 'amizadeId'));
    }

    // ===================================================
    // Procurar usuários
    // ===================================================

    public function procurar(Request $request)
    {
        $search = $request->input('search');
        $usuarios = User::where('id', '!=', auth()->id())
            ->when($search, fn($q) =>
                $q->where('nome', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
            )->paginate(12);

        return view('amizades.procurar', compact('usuarios', 'search'));
    }
}
