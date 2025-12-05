<?php

namespace App\Http\Controllers;

use App\Models\Campanha;
use App\Models\Sistema;
use App\Models\User;
use App\Models\Notificacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CampanhaController extends Controller
{
    private function mapStatus(string $statusPivot): string
    {
        return match ($statusPivot) {
            'ativo' => 'Jogador Ativo',
            'mestre' => 'Mestre',
            'pendente' => 'Solicitação Enviada',
            'rejeitado' => 'Rejeitado',
            default => 'Desconhecido',
        };
    }

    private function gerarCodigoUnico(): string
    {
        do {
            $codigo = Str::upper(Str::random(6));
        } while(Campanha::where('codigo_convite', $codigo)->exists());
        return $codigo;
    }

    private function podeGerenciar(Campanha $campanha): bool
    {
        $user = auth()->user();
        return $user && ($user->id === $campanha->criador_id || $user->tipo === 'administrador');
    }

    public function index()
    {
        return redirect()->route('campanhas.minhas');
    }

    public function minhas()
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $campanhasMestre = Campanha::where('criador_id', $user->id)
            ->with('sistema', 'criador')
            ->get();

        $campanhasJogador = $user->campanhas()
            ->wherePivot('status', 'ativo')
            ->with('sistema', 'criador')
            ->get();

        $minhasCampanhas = $campanhasMestre->merge($campanhasJogador);

        return view('campanhas.minhas', compact('minhasCampanhas'));
    }

    public function todas(Request $request)
    {
        $user = Auth::user();
        $query = Campanha::with(['criador', 'sistema', 'jogadores']);

        $query->where(function ($q) use ($user) {
            $q->where('privada', false);

            if ($user) {
                $q->orWhere('criador_id', $user->id)
                  ->orWhereHas('jogadores', fn($q2) => $q2->where('user_id', $user->id));
            }
        });

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(fn($q) => $q->where('nome', 'like', "%{$search}%")
                                     ->orWhere('descricao', 'like', "%{$search}%"));
        }

        $todasCampanhas = $query->orderBy('created_at', 'desc')->paginate(12);

        return view('campanhas.todas', compact('todasCampanhas'));
    }

    public function mestre($id)
    {
        $campanha = Campanha::with(['jogadores', 'missoes', 'sessoes', 'personagens'])->findOrFail($id);

        if (auth()->id() !== $campanha->criador_id) abort(403, 'Acesso negado: apenas o mestre pode acessar esta área.');

        return view('campanhas.mestre', compact('campanha'));
    }

    public function destroy($id)
    {
        $campanha = Campanha::findOrFail($id);

        if (!$this->podeGerenciar($campanha)) {
            return redirect()->back()->with('error', 'Você não tem permissão para excluir esta campanha.');
        }

        $campanha->delete();

        return redirect()->route('campanhas.todas')->with('success', 'Campanha excluída com sucesso!');
    }

    public function create()
    {
        $sistemas = Sistema::all();
        return view('campanhas.create', compact('sistemas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'sistema_id' => 'required|exists:sistemas,id',
            'descricao' => 'nullable|string',
            'privada' => 'nullable|boolean',
            'status' => 'required|in:ativa,inativa',
            'codigo_convite' => 'nullable|string|max:10|unique:campanhas',
        ]);

        $isPrivate = $request->boolean('privada');
        $inviteCode = $isPrivate && empty($request->codigo_convite) ? $this->gerarCodigoUnico() : $request->codigo_convite;

        if (!$isPrivate) $inviteCode = null;

        $campanha = Campanha::create([
            'nome' => $request->nome,
            'sistema_id' => $request->sistema_id,
            'criador_id' => auth()->id(),
            'descricao' => $request->descricao,
            'privada' => $isPrivate,
            'status' => $request->status,
            'codigo_convite' => $inviteCode,
        ]);

        $campanha->jogadores()->attach(auth()->id(), ['status' => 'mestre']);

        Notificacao::create([
            'usuario_id' => auth()->id(),
            'mensagem' => "Você criou a campanha '{$campanha->nome}'.",
            'lida' => false,
        ]);

        return redirect()->route('campanhas.show', $campanha->id)
                         ->with('success', 'Campanha criada com sucesso!');
    }

    public function edit(Campanha $campanha)
    {
        if (!$this->podeGerenciar($campanha)) {
            return redirect()->back()->with('error', 'Você não tem permissão para editar esta campanha.');
        }

        $sistemas = Sistema::all();
        return view('campanhas.edit', compact('campanha', 'sistemas'));
    }

    public function update(Request $request, Campanha $campanha)
    {
        if (!$this->podeGerenciar($campanha)) {
            return redirect()->back()->with('error', 'Você não tem permissão para atualizar esta campanha.');
        }

        $request->validate([
            'nome' => 'required|string|max:100',
            'sistema_id' => 'required|exists:sistemas,id',
            'descricao' => 'nullable|string',
            'privada' => 'nullable|boolean',
            'status' => 'required|in:ativa,inativa',
            'codigo_convite' => 'nullable|string|max:10|unique:campanhas,codigo_convite,'.$campanha->id,
        ]);

        $isPrivate = $request->boolean('privada');
        $inviteCode = $isPrivate && empty($request->codigo_convite) ? $this->gerarCodigoUnico() : $request->codigo_convite;

        if (!$isPrivate) $inviteCode = null;

        $campanha->update([
            'nome' => $request->nome,
            'sistema_id' => $request->sistema_id,
            'descricao' => $request->descricao,
            'privada' => $isPrivate,
            'status' => $request->status,
            'codigo_convite' => $inviteCode,
        ]);

        return redirect()->route('campanhas.show', $campanha->id)
                         ->with('success', 'Campanha atualizada com sucesso!');
    }

    public function show(Campanha $campanha)
    {
        $user = Auth::user();
        $isMestre = $user && $campanha->criador_id === $user->id;
        $statusPivot = $user ? $campanha->jogadores->firstWhere('id', $user->id)?->pivot->status : null;

        if ($campanha->privada && (!$user || (!$isMestre && $statusPivot !== 'ativo' && $user->tipo !== 'administrador'))) {
            return redirect()->route($user ? 'campanhas.todas' : 'login')
                             ->with('error', 'Acesso negado. Esta é uma campanha privada.');
        }

        $campanha->load(['jogadores.personagens', 'sessoes', 'missoes', 'personagens', 'criador', 'sistema']);
        $personagens = $campanha->jogadores->flatMap->personagens->where('campanha_id', $campanha->id);

        $amigos = collect();
        if ($user && $isMestre) {
            $amigos = $user->amigos()
                ->whereDoesntHave('campanhas', fn($q) => $q->where('campanha_id', $campanha->id))
                ->get();
        }

        return view('campanhas.show', compact('campanha', 'isMestre', 'amigos', 'statusPivot', 'personagens'));
    }

    public function solicitarEntrada(Campanha $campanha)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login')->with('error', 'Você precisa estar logado.');

        $jaParticipa = $campanha->jogadores()->where('user_id', $user->id)->exists();
        if ($jaParticipa) return redirect()->route('campanhas.show', $campanha->id)
                                         ->with('info', 'Você já possui uma solicitação ou participa desta campanha.');

        $campanha->jogadores()->attach($user->id, ['status' => 'pendente']);

        Notificacao::create([
            'usuario_id' => $campanha->criador_id,
            'mensagem' => "📩 O jogador **{$user->nome}** solicitou participar da campanha **{$campanha->nome}**.",
        ]);

        Notificacao::create([
            'usuario_id' => $user->id,
            'mensagem' => "⏳ Sua solicitação para entrar na campanha **{$campanha->nome}** foi enviada e aguarda aprovação do mestre.",
        ]);

        return redirect()->route('campanhas.show', $campanha->id)
                         ->with('success', 'Solicitação enviada! Aguarde aprovação do mestre.');
    }

    public function entrarComCodigo(Request $request)
    {
        $request->validate(['codigo' => 'required|string|max:10']);

        $user = Auth::user();
        if (!$user) return redirect()->route('login')->with('error', 'Você precisa estar logado para entrar na campanha.');

        $campanha = Campanha::where('codigo_convite', Str::upper($request->codigo))
                            ->where('privada', true)
                            ->first();

        if (!$campanha) return redirect()->back()->with('error', 'Código de convite inválido ou campanha não encontrada.');

        $jaParticipa = $campanha->jogadores()->where('user_id', $user->id)->exists();
        if ($jaParticipa) return redirect()->route('campanhas.show', $campanha->id)
                                           ->with('info', 'Você já está associado a esta campanha.');

        $campanha->jogadores()->attach($user->id, ['status' => 'ativo']);

        Notificacao::create([
            'usuario_id' => $campanha->criador_id,
            'mensagem' => "🎉 O jogador **{$user->nome}** entrou na campanha **{$campanha->nome}** usando o código de convite.",
        ]);

        Notificacao::create([
            'usuario_id' => $user->id,
            'mensagem' => "✅ Você entrou na campanha privada **{$campanha->nome}** usando o código de convite.",
        ]);

        return redirect()->route('campanhas.show', $campanha->id)->with('success', 'Você entrou na campanha com sucesso!');
    }

    public function aprovarUsuario(Request $request, Campanha $campanha)
    {
        $mestre = Auth::user();
        if ($mestre->id !== $campanha->criador_id) {
            return redirect()->back()->with('error', 'Apenas o mestre pode gerenciar jogadores.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|string|in:ativo,pendente,rejeitado,remover',
        ]);

        $jogador = User::findOrFail($request->user_id);
        $status = $request->status;

        if ($status === 'remover') {
            $campanha->jogadores()->detach($jogador->id);
            Notificacao::create([
                'usuario_id' => $jogador->id,
                'mensagem' => "🚪 Você foi removido da campanha **{$campanha->nome}**.",
            ]);
            return redirect()->back()->with('success', 'Jogador removido com sucesso!');
        }

        $campanha->jogadores()->updateExistingPivot($jogador->id, ['status' => $status]);
        Notificacao::create([
            'usuario_id' => $jogador->id,
            'mensagem' => "Seu status na campanha **{$campanha->nome}** foi atualizado para {$this->mapStatus($status)}.",
        ]);

        return redirect()->back()->with('success', 'Status do jogador atualizado com sucesso!');
    }

    public function gerenciarUsuario(Request $request, Campanha $campanha)
    {
        $mestre = Auth::user();
        if ($mestre->id !== $campanha->criador_id) {
            return redirect()->back()->with('error', 'Apenas o mestre pode gerenciar usuários.');
        }

        if ($request->filled('status') && in_array($request->status, ['ativo', 'pendente', 'rejeitado', 'remover'])) {
            return $this->aprovarUsuario($request, $campanha);
        }

        return redirect()->route('campanhas.mestre', $campanha->id)->with('info', 'Ação de gerenciamento de usuário concluída.');
    }

    public function adicionarAmigo(Request $request, Campanha $campanha)
    {
        $mestre = Auth::user();
        if ($mestre->id !== $campanha->criador_id) {
            return redirect()->back()->with('error', 'Apenas o mestre pode adicionar amigos.');
        }

        $request->validate(['amigo_id' => 'required|exists:users,id']);

        $amigo = User::findOrFail($request->amigo_id);

        if ($campanha->jogadores()->where('user_id', $amigo->id)->exists()) {
            return redirect()->back()->with('info', 'Este jogador já está na campanha.');
        }

        $campanha->jogadores()->attach($amigo->id, ['status' => 'ativo']);

        Notificacao::create([
            'usuario_id' => $amigo->id,
            'mensagem' => "🎉 Você foi adicionado à campanha **{$campanha->nome}** pelo mestre.",
        ]);

        return redirect()->back()->with('success', "O jogador {$amigo->nome} foi adicionado à campanha.");
    }
}
