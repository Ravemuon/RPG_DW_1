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
     * Mapear status do pivot para texto legível.
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
     * Redireciona para minhas campanhas.
     */
    public function index()
    {
        return redirect()->route('campanhas.minhas');
    }

    /**
     * Exibe as campanhas do usuário logado (criadas ou que ele participa).
     */
    public function minhas()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

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
     * Exibe todas as campanhas públicas e as privadas do usuário.
     */
    public function todas(Request $request)
    {
        $user = Auth::user();
        $query = Campanha::with(['criador', 'sistema', 'jogadores']);

        // Filtro de privacidade: só mostra públicas OU privadas que o usuário participa/é mestre
        $query->where(function ($q) use ($user) {
            $q->where('privada', false); // Campanhas Públicas

            if ($user) {
                $q->orWhere('criador_id', $user->id) // Ou campanhas do mestre (usuário logado)
                  ->orWhereHas('jogadores', fn($q2) => $q2->where('user_id', $user->id)); // Ou campanhas que o usuário participa
            }
        });

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('descricao', 'like', "%{$search}%");
            });
        }

        $todasCampanhas = $query->orderBy('created_at', 'desc')->paginate(12);

        return view('campanhas.todas', compact('todasCampanhas'));
    }

    /**
     * Exibe a área de gerenciamento da campanha para o mestre.
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
     * Deleta uma campanha.
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
     * Exibe o formulário para criar uma nova campanha.
     */
    public function create()
    {
        $sistemas = Sistema::all();
        return view('campanhas.create', compact('sistemas'));
    }

    /**
     * Salva uma nova campanha no banco de dados.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'sistema_id' => 'required|exists:sistemas,id',
            'descricao' => 'nullable|string',
            'privada' => 'nullable|boolean',
            'status' => 'required|in:ativa,inativa',
            // O código de convite é opcional, mas deve ser único
            'codigo_convite' => 'nullable|string|max:10|unique:campanhas',
        ]);

        $isPrivate = $request->boolean('privada');
        $inviteCode = $request->codigo_convite;

        // Se for privada e nenhum código for fornecido, gera um código aleatório (6 caracteres maiúsculos)
        if ($isPrivate && empty($inviteCode)) {
            $inviteCode = Str::upper(Str::random(6));
            // Garante que o código gerado é único (para evitar colisões)
            while (Campanha::where('codigo_convite', $inviteCode)->exists()) {
                 $inviteCode = Str::upper(Str::random(6));
            }
        } elseif (!$isPrivate) {
            // Se for pública, garante que o código seja nulo
            $inviteCode = null;
        }

        $campanha = Campanha::create([
            'nome' => $request->nome,
            'sistema_id' => $request->sistema_id,
            'criador_id' => auth()->id(),
            'descricao' => $request->descricao,
            'privada' => $isPrivate, // Recebe o booleano diretamente
            'status' => $request->status,
            'codigo_convite' => $inviteCode,
        ]);

        // Mestre é automaticamente anexado como 'mestre'
        $campanha->jogadores()->attach(auth()->id(), ['status' => 'mestre']);

        Notificacao::create([
            'usuario_id' => auth()->id(),
            'mensagem' => "Você criou a campanha '{$campanha->nome}'.",
            'lida' => false,
        ]);

        return redirect()->route('campanhas.show', $campanha->id)
                            ->with('success', 'Campanha criada com sucesso!');
    }

    /**
     * Exibe o formulário para edição de uma campanha.
     */
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
     * Atualiza as informações de uma campanha.
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
            // O código de convite é opcional, mas deve ser único (exceto para a campanha atual)
            'codigo_convite' => 'nullable|string|max:10|unique:campanhas,codigo_convite,'.$campanha->id,
        ]);

        $isPrivate = $request->boolean('privada');
        $inviteCode = $request->codigo_convite;

        // Se for privada e nenhum código for fornecido, gera um código aleatório (6 caracteres maiúsculos)
        if ($isPrivate && empty($inviteCode)) {
            $inviteCode = Str::upper(Str::random(6));
             // Garante que o código gerado é único (para evitar colisões)
            while (Campanha::where('codigo_convite', $inviteCode)->exists()) {
                 $inviteCode = Str::upper(Str::random(6));
            }
        } elseif (!$isPrivate) {
            // Se for pública, garante que o código seja nulo
            $inviteCode = null;
        }

        $campanha->update([
            'nome' => $request->nome,
            'sistema_id' => $request->sistema_id,
            'descricao' => $request->descricao,
            'privada' => $isPrivate, // Recebe o booleano
            'status' => $request->status,
            'codigo_convite' => $inviteCode,
        ]);

        return redirect()->route('campanhas.show', $campanha->id)
                            ->with('success', 'Campanha atualizada com sucesso!');
    }

    /**
     * Exibe a tela de visualização de uma campanha.
     */
    public function show(Campanha $campanha)
    {
        $user = Auth::user();
        $isMestre = $user && $campanha->criador_id === $user->id;
        $statusPivot = $user ? $campanha->jogadores()->where('user_id', $user->id)->first()?->pivot->status : null;

        // Controle de acesso para campanha privada
        // Permite acesso se for pública, ou se o usuário for mestre/jogador ativo/admin.
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

    /**
     * Envia uma solicitação de entrada para o mestre da campanha.
     */
    public function solicitarEntrada(Campanha $campanha)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Você precisa estar logado.');
        }

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
     * Permite que um usuário entre em uma campanha privada usando um código de convite.
     */
    public function entrarComCodigo(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:10',
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Você precisa estar logado para entrar na campanha.');
        }

        // Busca a campanha pelo código de convite, garantindo que seja uma campanha privada
        $campanha = Campanha::where('codigo_convite', Str::upper($request->codigo))
                            ->where('privada', true)
                            ->first();

        if (!$campanha) {
            return redirect()->back()->with('error', 'Código de convite inválido ou campanha não encontrada.');
        }

        // Checa se o usuário já participa da campanha
        $jaParticipa = $campanha->jogadores()->where('user_id', $user->id)->exists();
        if ($jaParticipa) {
            return redirect()->route('campanhas.show', $campanha->id)->with('info', 'Você já está associado a esta campanha.');
        }

        // Anexa o usuário diretamente como 'ativo' (o código é a aprovação)
        $campanha->jogadores()->attach($user->id, ['status' => 'ativo']);

        // Notificação para o mestre
        Notificacao::create([
            'usuario_id' => $campanha->criador_id,
            'mensagem' => "🎉 O jogador **{$user->nome}** entrou na campanha **{$campanha->nome}** usando o código de convite.",
        ]);

        // Notificação para o jogador
        Notificacao::create([
            'usuario_id' => $user->id,
            'mensagem' => "✅ Você entrou na campanha privada **{$campanha->nome}** usando o código de convite.",
        ]);

        return redirect()->route('campanhas.show', $campanha->id)->with('success', 'Você entrou na campanha com sucesso!');
    }

    /**
     * O mestre aprova, rejeita ou remove um jogador da campanha.
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

    /**
     * Gerencia ações de usuário (aprovação, rejeição, remoção) na área do mestre.
     */
    public function gerenciarUsuario(Request $request, Campanha $campanha)
    {
        $mestre = Auth::user();
        if ($mestre->id !== $campanha->criador_id) {
            return redirect()->back()->with('error', 'Apenas o mestre pode gerenciar usuários.');
        }

        // Exemplo: Delega a lógica de status para aprovarUsuario se for o caso
        if ($request->filled('status') && in_array($request->status, ['ativo', 'pendente', 'rejeitado', 'remover'])) {
            return $this->aprovarUsuario($request, $campanha);
        }

        // Lógica de gerenciamento adicional aqui, se houver.

        return redirect()->route('campanhas.mestre', $campanha->id)->with('info', 'Ação de gerenciamento de usuário concluída.');
    }

    /**
     * Adiciona um amigo do mestre à campanha como jogador ativo.
     */
    public function adicionarAmigo(Request $request, Campanha $campanha)
    {
        $mestre = Auth::user();
        if ($mestre->id !== $campanha->criador_id) {
            return redirect()->back()->with('error', 'Apenas o mestre pode adicionar amigos.');
        }

        $request->validate([
            'amigo_id' => 'required|exists:users,id',
        ]);

        $amigo = User::findOrFail($request->amigo_id);

        // Anexa o usuário como 'ativo' (assumindo que convite de mestre é aprovação imediata)
        $campanha->jogadores()->attach($amigo->id, ['status' => 'ativo']);

        Notificacao::create([
            'usuario_id' => $amigo->id,
            'mensagem' => "🎉 Você foi adicionado à campanha **{$campanha->nome}** pelo mestre.",
        ]);

        return redirect()->back()->with('success', "O jogador {$amigo->nome} foi adicionado à campanha.");
    }
}
