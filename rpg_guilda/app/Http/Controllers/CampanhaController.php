<?php

namespace App\Http\Controllers;

use App\Models\Campanha;
use App\Models\Sistema;
use App\Models\User;
use App\Models\Notificacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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

    public function index() { return redirect()->route('campanhas.minhas'); }

    public function minhas()
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $campanhasMestre = Campanha::where('criador_id', $user->id)->with('sistema')->get();
        $campanhasJogador = $user->campanhas()->with('criador', 'sistema')->get();

        $minhasCampanhas = $campanhasMestre->merge($campanhasJogador);

        return view('campanhas.minhas', compact('minhasCampanhas'));
    }

    public function todas(Request $request)
    {
        $query = Campanha::with('criador', 'sistema');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('nome', 'like', "%{$search}%")
                ->orWhere('descricao', 'like', "%{$search}%");
        }

        if (auth()->check()) {
            $userId = auth()->id();
            $query->where(function($q) use ($userId) {
                $q->where('privada', false)
                ->orWhereHas('jogadores', function($q2) use ($userId) {
                    $q2->where('user_id', $userId);
                });
            });
        } else {
            $query->where('privada', false);
        }

        $todasCampanhas = $query->orderBy('created_at', 'desc')->paginate(9);

        return view('campanhas.todas', compact('todasCampanhas'));
    }

    public function create() { return view('campanhas.create', ['sistemas' => Sistema::all()]); }

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

        $campanha = Campanha::create([
            'nome' => $request->nome,
            'sistema_id' => $request->sistema_id,
            'criador_id' => auth()->id(),
            'descricao' => $request->descricao,
            'privada' => $request->boolean('privada'),
            'status' => $request->status,
            'codigo_convite' => $request->codigo_convite ?: ($request->boolean('privada') ? strtoupper(substr(md5(uniqid()), 0, 6)) : null),
        ]);

        $campanha->jogadores()->attach(auth()->id(), ['status' => 'ativo']);

        Notificacao::create([
            'usuario_id' => auth()->id(),
            'mensagem' => "Você criou a campanha '{$campanha->nome}'.",
            'lida' => false,
        ]);

        return redirect()->route('campanhas.show', $campanha->id)->with('success', 'Campanha criada com sucesso!');
    }

    public function show(Campanha $campanha)
    {
        $user = Auth::user();
        $isMestre = $user && $campanha->criador_id === $user->id;
        $statusPivot = $user ? $campanha->jogadores()->where('user_id', $user->id)->first()?->pivot->status : null;

        if ($campanha->privada && (!$user || (!$isMestre && $statusPivot !== 'ativo'))) {
            $targetRoute = $user ? 'campanhas.todas' : 'login';
            return redirect()->route($targetRoute)->with('error', 'Acesso negado. Esta é uma campanha privada.');
        }

        $campanha->load([
            'jogadores.personagens' => fn($q) => $q->where('campanha_id', $campanha->id),
            'sessoes',
            'criador',
            'sistema'
        ]);

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
        if (!$user) {
            return redirect()->route('login')->with('error', 'Você precisa estar logado.');
        }

        if ($campanha->jogadores->contains($user->id)) {
            return redirect()->route('campanhas.show', $campanha->id)
                            ->with('info', 'Você já possui uma solicitação ou já participa desta campanha.');
        }

        $campanha->jogadores()->attach($user->id, ['status' => 'pendente']);

        Notificacao::create([
            'usuario_id' => $campanha->criador_id,
            'tipo' => 'Campanha',
            'mensagem' => "📩 O jogador **{$user->nome}** solicitou participar da campanha **{$campanha->nome}**.",
        ]);

        Notificacao::create([
            'usuario_id' => $user->id,
            'tipo' => 'Campanha',
            'mensagem' => "⏳ Sua solicitação para entrar na campanha **{$campanha->nome}** foi enviada e aguarda aprovação do mestre.",
        ]);

        return redirect()
            ->route('campanhas.show', $campanha->id)
            ->with('success', 'Solicitação enviada! Aguarde a aprovação do mestre.');
    }

    public function gerenciarUsuarios(Request $request, Campanha $campanha)
    {
        $this->authorize('update', $campanha);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:ativo,rejeitado,remover',
        ]);

        $userId = $request->user_id;
        $status = $request->status;
        $usuario = User::find($userId);

        if ($status === 'remover') {
            $campanha->jogadores()->detach($userId);
            $mensagem = "Jogador {$usuario->nome} removido.";
        } else {
            $campanha->jogadores()->updateExistingPivot($userId, ['status' => $status]);
            $mensagem = "Status de {$usuario->nome} atualizado para {$this->mapStatus($status)}.";
        }

        Notificacao::create([
            'usuario_id' => $usuario->id,
            'mensagem' => $status === 'remover' ? "Você foi removido da campanha '{$campanha->nome}'." : "Seu status na campanha '{$campanha->nome}' foi atualizado para {$this->mapStatus($status)}.",
            'lida' => false,
        ]);

        return redirect()->route('campanhas.mestre', $campanha->id)->with('success', $mensagem);
    }

    public function adicionarAmigo(Request $request, Campanha $campanha)
    {
        $this->authorize('update', $campanha);

        $request->validate(['user_id' => 'required|exists:users,id']);
        $userId = $request->user_id;

        if ($campanha->jogadores->contains($userId)) {
            return redirect()->route('campanhas.show', $campanha->id)->with('info', 'Este usuário já está na campanha.');
        }

        $campanha->jogadores()->attach($userId, ['status' => 'ativo']);

        Notificacao::create([
            'usuario_id' => $userId,
            'mensagem' => "Você foi adicionado à campanha '{$campanha->nome}'.",
            'lida' => false,
        ]);

        return redirect()->route('campanhas.show', $campanha->id)->with('success', 'Amigo adicionado à campanha com sucesso!');
    }

    public function mestre(Campanha $campanha)
    {
        $this->authorize('view', $campanha);

        $campanha->load(['sessoes', 'personagens', 'sistema']);

        return view('campanhas.mestre', compact('campanha'));
    }

    public function gerenciarUsuario(Request $request, Campanha $campanha)
    {
        $this->authorize('update', $campanha);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|string',
        ]);

        $userId = $validated['user_id'];
        $status = $validated['status'];

        if ($status === 'remover') {
            $campanha->jogadores()->detach($userId);
            return back()->with('success', 'Jogador removido da campanha.');
        }

        if (!in_array($status, ['ativo', 'pendente', 'rejeitado'])) {
            return back()->with('error', 'Status inválido.');
        }

        $campanha->jogadores()->updateExistingPivot($userId, ['status' => $status]);

        return back()->with('success', 'Status do jogador atualizado com sucesso!');
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

        $jogador = \App\Models\User::findOrFail($request->user_id);
        $status = $request->status;

        if ($status === 'remover') {
            $campanha->jogadores()->detach($jogador->id);

            Notificacao::create([
                'usuario_id' => $jogador->id,
                'tipo' => 'Campanha',
                'mensagem' => "🚪 Você foi removido da campanha **{$campanha->nome}** pelo mestre **{$mestre->nome}**.",
            ]);

            Notificacao::create([
                'usuario_id' => $mestre->id,
                'tipo' => 'Campanha',
                'mensagem' => "Você removeu **{$jogador->nome}** da campanha **{$campanha->nome}**.",
            ]);

            return redirect()->back()->with('success', "Jogador removido da campanha com sucesso!");
        }

        $campanha->jogadores()->updateExistingPivot($jogador->id, ['status' => $status]);

        $mensagemJogador = match ($status) {
            'ativo' => "🎉 Sua solicitação foi aprovada! Agora você faz parte da campanha **{$campanha->nome}**.",
            'rejeitado' => "🚫 Sua solicitação para participar da campanha **{$campanha->nome}** foi rejeitada pelo mestre.",
            'pendente' => "⏳ Sua solicitação para participar da campanha **{$campanha->nome}** está pendente de aprovação.",
            default => "Seu status na campanha **{$campanha->nome}** foi atualizado.",
        };

        Notificacao::create([
            'usuario_id' => $jogador->id,
            'tipo' => 'Campanha',
            'mensagem' => $mensagemJogador,
        ]);

        $mensagemMestre = match ($status) {
            'ativo' => "✅ Você aprovou {$jogador->nome} para participar da campanha {$campanha->nome}.",
            'rejeitado' => "🚫 Você rejeitou a solicitação de {$jogador->nome} para a campanha **{$campanha->nome}.",
            'pendente' => "⏳ Você deixou {$jogador->nome} com status pendente na campanha **{$campanha->nome}.",
            default => "Status de {$jogador->nome} atualizado na campanha {$campanha->nome}.",
        };

        Notificacao::create([
            'usuario_id' => $mestre->id,
            'tipo' => 'Campanha',
            'mensagem' => $mensagemMestre,
        ]);

        return redirect()->back()->with('success', 'Status do jogador atualizado com sucesso!');
    }
}
