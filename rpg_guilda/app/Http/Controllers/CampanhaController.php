<?php

namespace App\Http\Controllers;

use App\Models\Campanha;
use App\Models\Sistema;
use App\Models\Notificacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampanhaController extends Controller
{
    // ===================================================
    // 🔹 Mapeia status do pivot para exibição
    // ===================================================
    private function mapStatus(string $statusPivot): string
    {
        return match ($statusPivot) {
            'ativo' => 'Jogador Ativo',
            'mestre' => 'Mestre (Pivô)',
            'pendente' => 'Solicitação Enviada',
            'rejeitado' => 'Rejeitado',
            default => 'Desconhecido',
        };
    }

    // ===================================================
    // 🔹 Lista todas as campanhas do usuário logado
    // ===================================================
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return view('campanhas.minhas', ['campanhas' => collect()]);
        }

        $campanhasMestre = Campanha::where('criador_id', $user->id)
            ->with('sistema')
            ->get();

        $campanhasJogador = $user->campanhas()
            ->where('criador_id', '!=', $user->id)
            ->with('sistema')
            ->get();

        $campanhas = $campanhasMestre->merge($campanhasJogador)
            ->sortByDesc(fn($c) => $c->criador_id === $user->id);

        return view('campanhas.minhas', compact('campanhas'));
    }

    // ===================================================
    // 🔹 Formulário de criação
    // ===================================================
    public function create()
    {
        $sistemas = Sistema::all();
        return view('campanhas.create', compact('sistemas'));
    }

    // ===================================================
    // 🔹 Armazena nova campanha
    // ===================================================
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'sistema_id' => 'required|exists:sistemas,id',
            'descricao' => 'nullable|string',
            'privada' => 'nullable|boolean',
            'codigo_convite' => 'nullable|string|max:10',
        ]);

        $campanha = Campanha::create([
            'nome' => $request->nome,
            'sistema_id' => $request->sistema_id,
            'criador_id' => auth()->id(),
            'descricao' => $request->descricao,
            'privada' => $request->has('privada'),
            'codigo_convite' => $request->codigo_convite ?: ($request->has('privada') ? strtoupper(substr(md5(uniqid()), 0, 6)) : null),
        ]);

        // 🔔 Notificação opcional: Campanha criada (para o mestre)
        Notificacao::create([
            'usuario_id' => auth()->id(),
            'mensagem' => "Você criou a campanha '{$campanha->nome}'.",
            'lida' => false,
        ]);

        return redirect()->route('campanhas.show', $campanha->id)
                         ->with('success', 'Campanha criada com sucesso!');
    }

    // ===================================================
    // 🔹 Exibe detalhes da campanha
    // ===================================================
    public function show(Campanha $campanha)
    {
        $user = Auth::user();
        $isMestre = $user && $campanha->criador_id === $user->id;
        $statusPivot = $user ? $campanha->jogadores()->where('user_id', $user->id)->first()?->pivot->status : null;

        if ($campanha->privada && (!$user || (!$isMestre && !in_array($statusPivot, ['ativo','pendente'])))) {
            return redirect()->route('campanhas.todas')->with('error', 'Acesso negado a esta campanha privada.');
        }

        $campanha->load(['jogadores.personagens', 'sessoes', 'criador', 'sistema', 'missoes']);

        $amigos = collect();
        if ($user && $isMestre) {
            $amigos = $user->amigos()
                ->whereDoesntHave('campanhas', function($q) use ($campanha) {
                    $q->where('campanha_id', $campanha->id);
                })
                ->get();
        }

        return view('campanhas.show', compact('campanha', 'isMestre', 'amigos', 'statusPivot'));
    }

    // ===================================================
    // 🔹 Formulário de edição
    // ===================================================
    public function edit(Campanha $campanha)
    {
        $this->authorize('update', $campanha);

        $campanha->load('jogadores', 'sistema');
        $sistemas = Sistema::all();
        return view('campanhas.edit', compact('campanha', 'sistemas'));
    }

    // ===================================================
    // 🔹 Atualiza campanha
    // ===================================================
    public function update(Request $request, Campanha $campanha)
    {
        $this->authorize('update', $campanha);

        $request->validate([
            'nome' => 'required|string|max:100',
            'sistema_id' => 'required|exists:sistemas,id',
            'descricao' => 'nullable|string',
            'privada' => 'nullable|boolean',
            'codigo_convite' => 'nullable|string|max:20',
        ]);

        $campanha->update([
            'nome' => $request->nome,
            'sistema_id' => $request->sistema_id,
            'descricao' => $request->descricao,
            'privada' => $request->has('privada'),
            'codigo_convite' => $request->codigo_convite ?: ($request->has('privada') ? strtoupper(substr(md5(uniqid()), 0, 6)) : null),
        ]);

        Notificacao::create([
            'usuario_id' => auth()->id(),
            'mensagem' => "Você atualizou a campanha '{$campanha->nome}'.",
            'lida' => false,
        ]);

        return redirect()->route('campanhas.edit', $campanha->id)
                         ->with('success', 'Campanha atualizada com sucesso!');
    }

    // ===================================================
    // 🔹 Deleta campanha
    // ===================================================
    public function destroy(Campanha $campanha)
    {
        $this->authorize('delete', $campanha);

        $campanha->delete();

        Notificacao::create([
            'usuario_id' => auth()->id(),
            'mensagem' => "Você deletou a campanha '{$campanha->nome}'.",
            'lida' => false,
        ]);

        return redirect()->route('campanhas.index')
                         ->with('success', 'Campanha deletada com sucesso!');
    }

    // ===================================================
    // 🔹 Lista todas campanhas públicas (com busca e privadas que o usuário participa)
    // ===================================================
    public function todas(Request $request)
    {
        $user = auth()->user();
        $search = $request->query('search');

        $campanhas = Campanha::with('sistema', 'criador')
            ->where(function($q) use ($search) {
                if ($search) {
                    $q->where('nome', 'like', "%{$search}%");
                }
            })
            ->where(function($q) use ($user) {
                $q->where('privada', false);

                if ($user) {
                    $q->orWhereHas('jogadores', function($q2) use ($user) {
                        $q2->where('user_id', $user->id);
                    });
                }
            })
            ->get();

        $campanhasPorSistema = $campanhas->groupBy(fn($c) => $c->sistema->nome ?? 'Sistema Desconhecido');

        return view('campanhas.todas', compact('campanhasPorSistema'));
    }

    // ===================================================
    // 🔹 Usuário entra na campanha (pública ou privada com código)
    // ===================================================
    public function entrar(Request $request, Campanha $campanha)
    {
        $user = auth()->user();
        if (!$user) return redirect()->route('campanhas.todas')->with('error', 'Você precisa estar logado.');

        if ($campanha->jogadores->contains($user->id)) {
            return redirect()->route('campanhas.show', $campanha->id)
                             ->with('info', 'Você já participa desta campanha.');
        }

        if ($campanha->privada) {
            $request->validate([
                'codigo' => 'required|string'
            ]);

            if ($request->codigo !== $campanha->codigo_convite) {
                return redirect()->back()->withErrors(['codigo' => 'Código inválido.']);
            }
        }

        $campanha->jogadores()->attach($user->id, ['status' => 'pendente']);

        Notificacao::create([
            'usuario_id' => $campanha->criador_id,
            'mensagem' => "{$user->nome} solicitou entrar na sua campanha '{$campanha->nome}'.",
            'lida' => false,
        ]);

        return redirect()->route('campanhas.show', $campanha->id)
                         ->with('success', 'Solicitação enviada com sucesso!');
    }

    // ===================================================
    // 🔹 Gerencia status de usuários (aprovar/rejeitar)
    // ===================================================
    public function gerenciarUsuario(Request $request, Campanha $campanha)
    {
        $this->authorize('update', $campanha);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:ativo,rejeitado',
        ]);

        $userId = $request->user_id;
        $status = $request->status;

        $campanha->jogadores()->updateExistingPivot($userId, ['status' => $status]);

        $usuario = $campanha->jogadores()->find($userId);

        $mensagem = $status === 'ativo'
                    ? "Sua solicitação para entrar na campanha '{$campanha->nome}' foi aprovada!"
                    : "Sua solicitação para entrar na campanha '{$campanha->nome}' foi rejeitada.";

        Notificacao::create([
            'usuario_id' => $usuario->id,
            'mensagem' => $mensagem,
            'lida' => false,
        ]);

        return redirect()->route('campanhas.show', $campanha->id)
                         ->with('success', "Status do usuário atualizado com sucesso!");
    }

    // ===================================================
    // 🔹 Adiciona amigos à campanha
    // ===================================================
    public function adicionarAmigo(Request $request, Campanha $campanha)
    {
        $this->authorize('update', $campanha);

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userId = $request->user_id;

        if ($campanha->jogadores->contains($userId)) {
            return redirect()->route('campanhas.show', $campanha->id)
                             ->with('info', 'Este usuário já participa da campanha.');
        }

        $campanha->jogadores()->attach($userId, ['status' => 'ativo']);

        Notificacao::create([
            'usuario_id' => $userId,
            'mensagem' => "Você foi adicionado à campanha '{$campanha->nome}' por {$campanha->criador->nome}.",
            'lida' => false,
        ]);

        return redirect()->route('campanhas.show', $campanha->id)
                         ->with('success', 'Amigo adicionado à campanha com sucesso!');
    }

    // ===================================================
    // 🔹 Lista campanhas do usuário
    // ===================================================
    public function minhas()
    {
        $user = Auth::user();

        $campanhasMestre = Campanha::where('criador_id', $user->id)->get();
        $campanhasJogador = $user->campanhas()->where('criador_id', '!=', $user->id)->get();

        return view('campanhas.minhas', compact('campanhasMestre', 'campanhasJogador'));
    }
}
