<?php

namespace App\Http\Controllers;

use App\Models\Campanha;
use App\Models\Notificacao;
use App\Models\Sistema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class CampanhaController extends Controller
{
    // ===================================================
    // 🔹 Mapeia status do pivot para exibição
    // ===================================================
    private function mapStatus(string $statusPivo): string
    {
        return match ($statusPivo) {
            'ativo' => 'Jogador Ativo',
            'mestre' => 'Mestre (Pivô)',
            'pendente' => 'Solicitação Enviada',
            default => 'Desconhecido',
        };
    }

    // ===================================================
    // 🔹 Lista campanhas do usuário logado
    // ===================================================
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return view('campanhas.minhas', ['campanhas' => collect()]);
        }

        $campanhasMestre = Campanha::where('criador_id', $user->id)
            ->with('sistema')
            ->get()
            ->map(fn($campanha) => $campanha->statusUsuario = 'Mestre' ? $campanha : $campanha);

        $campanhasJogador = $user->campanhas()
            ->where('criador_id', '!=', $user->id)
            ->with('sistema')
            ->get()
            ->map(fn($campanha) => $campanha->statusUsuario = $this->mapStatus($campanha->pivot->status ?? 'Desconhecido') ? $campanha : $campanha);

        $campanhas = $campanhasMestre->merge($campanhasJogador)
            ->sortByDesc(fn($c) => $c->statusUsuario === 'Mestre');

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
        return view('campanhas.show', compact('campanha', 'isMestre'));
    }

    // ===================================================
    // 🔹 Formulário de edição
    // ===================================================
    public function edit(Campanha $campanha)
    {
        $user = Auth::user();
        if (!$user || ($campanha->criador_id !== $user->id && $user->tipo !== 'administrador')) {
            return redirect()->route('campanhas.show', $campanha->id)
                             ->with('error', 'Você não tem permissão para editar esta campanha.');
        }

        $campanha->load('jogadores', 'sistema');
        $sistemas = Sistema::all();
        return view('campanhas.edit', compact('campanha', 'sistemas'));
    }

    // ===================================================
    // 🔹 Atualiza campanha
    // ===================================================
    public function update(Request $request, Campanha $campanha)
    {
        $user = Auth::user();
        if (!$user || ($campanha->criador_id !== $user->id && $user->tipo !== 'administrador')) {
            return redirect()->route('campanhas.show', $campanha->id)
                             ->with('error', 'Você não tem permissão para atualizar esta campanha.');
        }

        $request->validate([
            'nome' => 'required|string|max:100',
            'sistema_id' => 'required|exists:sistemas,id',
            'descricao' => 'nullable|string',
            'privada' => 'nullable|boolean',
            'codigo_convite' => 'nullable|string|max:20'
        ]);

        $campanha->update([
            'nome' => $request->nome,
            'sistema_id' => $request->sistema_id,
            'descricao' => $request->descricao,
            'privada' => $request->has('privada'),
            'codigo_convite' => $request->codigo_convite ?: ($request->has('privada') ? strtoupper(substr(md5(uniqid()), 0, 6)) : null),
        ]);

        return redirect()->route('campanhas.edit', $campanha->id)
                         ->with('success', 'Campanha atualizada com sucesso!');
    }

    // ===================================================
    // 🔹 Deleta campanha
    // ===================================================
    public function destroy(Campanha $campanha)
    {
        $user = Auth::user();
        if (!$user || ($campanha->criador_id !== $user->id && $user->tipo !== 'administrador')) {
            return redirect()->route('campanhas.show', $campanha->id)
                             ->with('error', 'Você não tem permissão para deletar esta campanha.');
        }

        $campanha->delete();
        return redirect()->route('campanhas.index')
                         ->with('success', 'Campanha deletada com sucesso!');
    }

    // ===================================================
    // 🔹 Chat da campanha
    // ===================================================
    public function chat(Campanha $campanha)
    {
        $user = Auth::user();

        // Verifica se o usuário tem acesso ao chat
        if (!$user || ($campanha->privada && $campanha->criador_id !== $user->id && !$campanha->jogadores->contains($user->id))) {
            return redirect()->route('campanhas.todas')->with('error', 'Acesso negado ao chat desta campanha.');
        }

        // Pega as mensagens do chat da campanha
        $mensagens = $campanha->chat?->mensagens ?? collect();

        return view('campanhas.chat', compact('campanha', 'mensagens'));
    }

    public function enviarMensagem(Request $request, Campanha $campanha)
    {
        $user = Auth::user();

        // Verifica se o usuário pode enviar mensagens
        if (!$user || ($campanha->privada && $campanha->criador_id !== $user->id && !$campanha->jogadores->contains($user->id))) {
            return redirect()->route('campanhas.todas')->with('error', 'Acesso negado ao chat desta campanha.');
        }

        // Validação da mensagem
        $request->validate([
            'mensagem' => 'required|string|max:1000'
        ]);

        // Cria o chat da campanha se ainda não existir
        if (!$campanha->chat) {
            $chat = Chat::create([
                'campanha_id' => $campanha->id,
                'nome' => "Chat da campanha {$campanha->nome}"
            ]);
        } else {
            $chat = $campanha->chat;
        }

        // Cria a mensagem no chat
        $mensagem = $chat->mensagens()->create([
            'user_id' => $user->id,
            'mensagem' => $request->mensagem
        ]);

        // Notifica os outros jogadores (exceto o remetente)
        $usuarios = $campanha->jogadores->where('id', '!=', $user->id);
        foreach ($usuarios as $usuario) {
            $usuario->notify(new NovaMensagemChat($mensagem));
        }

        return redirect()->route('campanhas.chat', $campanha->id)->with('success', 'Mensagem enviada!');
    }
    // ===================================================
    // Lista todas campanhas públicas
    // ===================================================
    public function todas()
    {
        $user = auth()->user();

        // Pega todas as campanhas ativas ou públicas
        $campanhas = Campanha::with('sistema', 'criador')
            ->where('privada', false)
            ->orWhere(function($query) use ($user) {
                if ($user) {
                    // Inclui campanhas privadas que o usuário participa
                    $query->whereHas('jogadores', function($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
                }
            })
            ->get();

        // Agrupa campanhas por sistema
        $campanhasPorSistema = $campanhas->groupBy(function($campanha) {
            return $campanha->sistema->nome ?? 'Sistema Desconhecido';
        });

        return view('campanhas.todas', compact('campanhasPorSistema'));
    }
    public function entrar(Campanha $campanha)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('campanhas.index')->with('error', 'Você precisa estar logado para entrar em uma campanha.');
        }

        if ($campanha->jogadores->contains($user->id)) {
            return redirect()->route('campanhas.show', $campanha->id)->with('info', 'Você já participa desta campanha.');
        }

        $campanha->jogadores()->attach($user->id, ['status' => 'ativo']);
        return redirect()->route('campanhas.show', $campanha->id)->with('success', 'Você entrou na campanha!');
    }



}
