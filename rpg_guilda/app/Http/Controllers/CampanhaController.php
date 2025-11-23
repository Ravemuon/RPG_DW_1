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
    /**
     * Mapear status do pivot para texto legível
     */
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

    /**
     * Redireciona para minhas campanhas
     */
    public function index()
    {
        return redirect()->route('campanhas.minhas');
    }

    /**
     * Minhas campanhas (criado ou participa)
     */
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

    /**
     * Todas campanhas públicas e privadas (acesso controlado)
     */
    public function todas(Request $request)
    {
        $query = Campanha::with(['criador', 'sistema', 'jogadores']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('nome', 'like', "%{$search}%")
                  ->orWhere('descricao', 'like', "%{$search}%");
        }

        $todasCampanhas = $query->orderBy('created_at', 'desc')->paginate(12);

        return view('campanhas.todas', compact('todasCampanhas'));
    }

    /**
     * Área do mestre
     */
    public function mestre($id)
    {
        $campanha = Campanha::with(['jogadores', 'missoes', 'sessoes', 'personagens'])->findOrFail($id);

        if (auth()->id() !== $campanha->criador_id) {
            abort(403, 'Acesso negado: apenas o mestre pode acessar esta área.');
        }

        return view('campanhas.mestre', compact('campanha'));
    }

    /**
     * Deletar campanha
     */
    public function destroy($id)
    {
        $campanha = Campanha::findOrFail($id);

        if (auth()->id() !== $campanha->criador_id && auth()->user()->tipo !== 'administrador') {
            return redirect()->back()->with('error', 'Você não tem permissão para excluir esta campanha.');
        }

        $campanha->delete();

        return redirect()->route('campanhas.todas')->with('success', 'Campanha excluída com sucesso!');
    }

    /**
     * Tela criar campanha
     */
    public function create()
    {
        $sistemas = Sistema::all();
        return view('campanhas.create', compact('sistemas'));
    }

    /**
     * Salvar campanha
     */
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
            'codigo_convite' => $request->codigo_convite ?: ($request->boolean('privada') ? strtoupper(Str::random(6)) : null),
        ]);

        $campanha->jogadores()->attach(auth()->id(), ['status' => 'ativo']);

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
        // Apenas o criador ou admin pode editar
        if(auth()->id() !== $campanha->criador_id && auth()->user()->tipo !== 'administrador') {
            return redirect()->back()->with('error', 'Você não tem permissão para editar esta campanha.');
        }

        $sistemas = Sistema::all();
        return view('campanhas.edit', compact('campanha', 'sistemas'));
    }

    /**
     * Atualizar campanha
     */
    public function update(Request $request, Campanha $campanha)
    {
        // Apenas o criador ou admin pode atualizar
        if(auth()->id() !== $campanha->criador_id && auth()->user()->tipo !== 'administrador') {
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

        $campanha->update([
            'nome' => $request->nome,
            'sistema_id' => $request->sistema_id,
            'descricao' => $request->descricao,
            'privada' => $request->boolean('privada'),
            'status' => $request->status,
            'codigo_convite' => $request->codigo_convite ?: ($request->boolean('privada') ? strtoupper(Str::random(6)) : null),
        ]);

        return redirect()->route('campanhas.show', $campanha->id)
                         ->with('success', 'Campanha atualizada com sucesso!');
    }

    /**
     * Visualizar campanha
     */
    public function show(Campanha $campanha)
    {
        $user = Auth::user();
        $isMestre = $user && $campanha->criador_id === $user->id;
        $statusPivot = $user ? $campanha->jogadores()->where('user_id', $user->id)->first()?->pivot->status : null;

        // Controle de acesso para campanha privada
        if ($campanha->privada && (!$user || (!$isMestre && $statusPivot !== 'ativo'))) {
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

    /**
     * Solicitar entrada em campanha
     */
    public function solicitarEntrada(Campanha $campanha)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login')->with('error', 'Você precisa estar logado.');

        // Checa se já participa ou já solicitou
        $jaParticipa = $campanha->jogadores()->where('user_id', $user->id)->exists();
        if ($jaParticipa) {
            return redirect()->route('campanhas.show', $campanha->id)
                            ->with('info', 'Você já possui uma solicitação ou participa desta campanha.');
        }

        // Cria a solicitação com status pendente
        $campanha->jogadores()->attach($user->id, ['status' => 'pendente']);

        // Notificação para o mestre
        Notificacao::create([
            'usuario_id' => $campanha->criador_id,
            'mensagem' => "📩 O jogador **{$user->nome}** solicitou participar da campanha **{$campanha->nome}**.",
        ]);

        // Notificação para o jogador
        Notificacao::create([
            'usuario_id' => $user->id,
            'mensagem' => "⏳ Sua solicitação para entrar na campanha **{$campanha->nome}** foi enviada e aguarda aprovação do mestre.",
        ]);

        return redirect()->route('campanhas.show', $campanha->id)
                        ->with('success', 'Solicitação enviada! Aguarde aprovação do mestre.');
    }


    /**
     * Aprovar ou rejeitar jogador
     */
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
}
