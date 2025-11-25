<?php

namespace App\Http\Controllers;

use App\Models\Personagem;
use App\Models\Pericia;
use App\Models\Raca;
use App\Models\Classe;
use App\Models\Origem;
use App\Models\Sistema;
use App\Models\Campanha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PersonagemController extends Controller
{
    // Custo de pontos para Point Buy (8-15) - Adaptável
    // Exemplo: 8=0, 9=1, 10=2, 11=3, 12=4, 13=5, 14=7, 15=9
    const POINT_BUY_COST = [
        8 => 0, 9 => 1, 10 => 2, 11 => 3, 12 => 4, 13 => 5, 14 => 7, 15 => 9,
    ];
    // Limite total de pontos
    const POINT_BUY_LIMIT = 27;

    public function index()
    {
        // Carrega personagens do usuário logado com suas relações
        $personagens = Personagem::with(['raca','classe','origem','sistema','campanha'])
            ->where('user_id', Auth::id())
            ->get();

        return view('personagens.index', compact('personagens'));
    }

    public function create(Request $request)
    {
        // Garante que a campanha foi passada via query string
        $campanha_id = $request->query('campanha');
        if (!$campanha_id) {
            return redirect()->route('campanhas.index')->with('error', 'Selecione uma campanha para criar um personagem.');
        }

        // Carrega a campanha e as opções de criação
        $campanha = Campanha::with('sistema.racas','sistema.classes','sistema.origens')->findOrFail($campanha_id);

        $racas = $campanha->sistema->racas;
        $classes = $campanha->sistema->classes;
        $origens = $campanha->sistema->origens;

        // Decodifica a lista de atributos do sistema (ex: ['forca', 'destreza', ...])
        $atributosSistema = $campanha->sistema->atributos ? json_decode($campanha->sistema->atributos, true) : [
            'forca' => 'Força',
            'destreza' => 'Destreza',
            'constituicao' => 'Constituição',
            'inteligencia' => 'Inteligência',
            'sabedoria' => 'Sabedoria',
            'carisma' => 'Carisma',
        ];

        // Passa o custo do Point Buy para a view
        $pointBuyCosts = self::POINT_BUY_COST;
        $pointBuyLimit = self::POINT_BUY_LIMIT;

        return view('personagens.create', compact('campanha','racas','classes','origens','atributosSistema', 'pointBuyCosts', 'pointBuyLimit'));
    }

    public function store(Request $request)
    {
        // 1. Definição e Validação dos Atributos
        $sistema = Sistema::findOrFail($request->sistema_id);
        $atributosPermitidos = $sistema->atributos ? array_keys(json_decode($sistema->atributos, true)) : ['forca', 'destreza', 'constituicao', 'inteligencia', 'sabedoria', 'carisma'];

        $rules = [
            'campanha_id' => 'required|exists:campanhas,id',
            'raca_id'     => 'required|exists:racas,id',
            'classe_id'   => 'required|exists:classes,id',
            'origem_id'   => 'nullable|exists:origens,id',
            'sistema_id'  => 'required|exists:sistemas,id',
            'nome'        => 'required|string|max:100',
            'imagem'      => 'nullable|image|max:2048',
            // Regras de validação para os atributos base
            'pv_inicial'  => 'nullable|integer|min:1',
            'nivel'       => 'nullable|integer|min:1|max:20',
        ];

        // Adiciona validação de Point Buy para cada atributo
        $minScore = min(array_keys(self::POINT_BUY_COST));
        $maxScore = max(array_keys(self::POINT_BUY_COST));

        foreach ($atributosPermitidos as $attr) {
            $rules[$attr] = ['required', 'integer', "min:$minScore", "max:$maxScore"];
        }

        $request->validate($rules);

        // 2. Validação do Custo Total do Point Buy
        $baseAtributos = $request->only($atributosPermitidos);
        $pontosGastos = 0;
        foreach ($baseAtributos as $score) {
            if (isset(self::POINT_BUY_COST[$score])) {
                $pontosGastos += self::POINT_BUY_COST[$score];
            } else {
                return back()->withInput()->withErrors(['atributos' => "Valor de atributo inválido: $score."]);
            }
        }

        if ($pontosGastos > self::POINT_BUY_LIMIT) {
            return back()->withInput()->withErrors(['pontos_compra' => "Você excedeu o limite de pontos (máximo: " . self::POINT_BUY_LIMIT . "). Pontos gastos: $pontosGastos."]);
        }

        // 3. Criação do Personagem
        $data = $request->only([
            'campanha_id','raca_id','classe_id','origem_id','sistema_id','nome','descricao','historia','personalidade','inventario'
        ]);
        $data['user_id'] = Auth::id();
        $data['nivel'] = $request->filled('nivel') ? $request->nivel : 1;
        $data['base_atributos'] = json_encode($baseAtributos); // Salva a alocação de pontos do jogador

        if($request->hasFile('imagem')){
            $data['imagem'] = $request->file('imagem')->store('personagens','public');
        }

        $personagem = Personagem::create($data);

        // 4. Cálculo de Atributos Finais, PV e Atribuição de Perícias

        // Atributos Finais (Base + Bônus de Raça/Classe)
        $personagem->atributos = json_encode($this->calcularAtributos($personagem));

        // PV Máximo (pode ser um valor fixo, ou calculado baseado em Constituicao/Classe)
        // Por simplicidade, usamos o valor do request, ou 10 + modificador de CON
        $pvBase = $request->filled('pv_inicial') ? $request->pv_inicial : 10;
        $atributosFinais = json_decode($personagem->atributos, true);
        $modConstituicao = floor(($atributosFinais['constituicao'] ?? 10) / 2) - 5;
        $personagem->pv_maximo = max(1, $pvBase + $modConstituicao);
        $personagem->pv_atual = $personagem->pv_maximo;

        // Atribuição de Perícias Iniciais (Assumindo que Classes e Origens podem ter IDs de Perícias)
        $this->atribuirPericiasIniciais($personagem);

        $personagem->save();

        return redirect()->route('personagens.index')
            ->with('success','Personagem criado com sucesso e prontinho para a aventura!');
    }

    /**
     * Calcula os atributos finais (Base + Bônus de Raça/Classe/Origem).
     * @param Personagem $personagem
     * @return array
     */
    private function calcularAtributos(Personagem $personagem): array
    {
        // 1. Pega os atributos base definidos pelo jogador (Point Buy)
        $baseAtributos = json_decode($personagem->base_atributos, true) ?? [];
        $atributosFinais = $baseAtributos;

        // Se não houver atributos base (Point Buy não usado ou novo sistema), usa default 10
        if (empty($atributosFinais)) {
             $atributosFinais = ['forca'=>10,'destreza'=>10,'constituicao'=>10,'inteligencia'=>10,'sabedoria'=>10,'carisma'=>10];
        }

        // 2. Aplica bônus de Raça, Classe e Origem
        foreach(['raca', 'classe', 'origem'] as $rel){
            $personagem->loadMissing($rel); // Garante que a relação está carregada

            $modificadores = $personagem->$rel->atributos_bonus ?? $personagem->$rel->modificadores_atributos ?? [];

            if (!empty($modificadores) && is_array($modificadores)){
                foreach($modificadores as $key => $val) {
                    // Garante que o atributo base exista antes de adicionar o bônus
                    if(isset($atributosFinais[$key])) {
                        $atributosFinais[$key] += (int)$val;
                    }
                }
            }
        }

        return $atributosFinais;
    }

    /**
     * Atribui perícias ao personagem com base na Classe e Origem.
     * @param Personagem $personagem
     * @return void
     */
    private function atribuirPericiasIniciais(Personagem $personagem)
    {
        $personagem->loadMissing('classe', 'origem');
        $periciasParaAdicionar = [];

        // Atribui perícias da Classe
        // Assumindo que $personagem->classe->pericias_iniciais é um JSON de IDs de Perícias
        $classePericias = json_decode($personagem->classe->pericias_iniciais ?? '[]', true);
        $periciasParaAdicionar = array_merge($periciasParaAdicionar, $classePericias);

        // Atribui perícias da Origem (se houver)
        $origemPericias = json_decode($personagem->origem->pericias_iniciais ?? '[]', true);
        $periciasParaAdicionar = array_merge($periciasParaAdicionar, $origemPericias);

        // Remove duplicatas
        $periciasParaAdicionar = array_unique(array_filter($periciasParaAdicionar));

        // Sincroniza as perícias com o personagem (assumindo N:N com IDs de pericias)
        // Se a tabela pivô tiver outros campos (ex: 'bonus'), isso deve ser ajustado.
        // Aqui, assumimos apenas um anexo simples.
        if (!empty($periciasParaAdicionar)) {
            $personagem->pericias()->sync($periciasParaAdicionar);
        }
    }


    // Os métodos show, edit, update e destroy permanecem em grande parte iguais.

    public function show(Personagem $personagem)
    {
        $this->authorize('view', $personagem);
        // Garante que 'base_atributos' é carregado para visualização
        $personagem->load('raca','classe','origem','sistema','pericias','user');
        return view('personagens.show',compact('personagem'));
    }

    public function edit(Personagem $personagem)
    {
        $this->authorize('update',$personagem);
        $campanha = $personagem->campanha()->with('sistema.racas','sistema.classes','sistema.origens')->first();

        // Decodifica atributos do sistema para passar para a view
        $atributosSistema = $campanha->sistema->atributos ? json_decode($campanha->sistema->atributos, true) : [
            'forca' => 'Força',
            'destreza' => 'Destreza',
            'constituicao' => 'Constituição',
            'inteligencia' => 'Inteligência',
            'sabedoria' => 'Sabedoria',
            'carisma' => 'Carisma',
        ];

        return view('personagens.edit',[
            'personagem'=>$personagem,
            'campanha'=>$campanha,
            'racas'=>$campanha->sistema->racas,
            'classes'=>$campanha->sistema->classes,
            'origens'=>$campanha->sistema->origens,
            'sistemas'=>Sistema::all(),
            'atributosSistema' => $atributosSistema,
            'pointBuyCosts' => self::POINT_BUY_COST,
            'pointBuyLimit' => self::POINT_BUY_LIMIT,
        ]);
    }

    public function update(Request $request, Personagem $personagem)
    {
        $this->authorize('update',$personagem);

        // 1. Validação de Atributos Base (Revalida Point Buy)
        $sistema = $personagem->sistema;
        $atributosPermitidos = $sistema->atributos ? array_keys(json_decode($sistema->atributos, true)) : ['forca', 'destreza', 'constituicao', 'inteligencia', 'sabedoria', 'carisma'];

        $rules = [
            'raca_id'=>'required|exists:racas,id',
            'classe_id'=>'required|exists:classes,id',
            'origem_id'=>'nullable|exists:origens,id',
            'sistema_id'=>'required|exists:sistemas,id',
            'nome'=>'nullable|string|max:100',
            'imagem'=>'nullable|image|max:2048',
            'nivel' => 'nullable|integer|min:1|max:20',
        ];

        // Adiciona validação de Point Buy para cada atributo (se estiverem sendo enviados)
        $minScore = min(array_keys(self::POINT_BUY_COST));
        $maxScore = max(array_keys(self::POINT_BUY_COST));

        foreach ($atributosPermitidos as $attr) {
            if ($request->has($attr)) {
                $rules[$attr] = ['required', 'integer', "min:$minScore", "max:$maxScore"];
            }
        }

        $request->validate($rules);

        // 2. Validação do Custo Total do Point Buy (Apenas se o Point Buy for editado)
        $baseAtributos = [];
        $pontosGastos = 0;
        $hasPointBuyData = false;

        foreach ($atributosPermitidos as $attr) {
            if ($request->has($attr)) {
                $hasPointBuyData = true;
                $score = (int)$request->input($attr);
                $baseAtributos[$attr] = $score;
                if (isset(self::POINT_BUY_COST[$score])) {
                    $pontosGastos += self::POINT_BUY_COST[$score];
                } else {
                    return back()->withInput()->withErrors(['atributos' => "Valor de atributo inválido: $score."]);
                }
            }
        }

        if ($hasPointBuyData && $pontosGastos > self::POINT_BUY_LIMIT) {
            return back()->withInput()->withErrors(['pontos_compra' => "Você excedeu o limite de pontos (máximo: " . self::POINT_BUY_LIMIT . "). Pontos gastos: $pontosGastos."]);
        }

        // 3. Atualização de Dados
        $data = $request->only(['raca_id','classe_id','origem_id','sistema_id','nome','descricao','historia','personalidade','inventario']);
        $data['nivel'] = $request->filled('nivel') ? $request->nivel : $personagem->nivel;

        if($request->hasFile('imagem')){
            if($personagem->imagem) Storage::disk('public')->delete($personagem->imagem);
            $data['imagem'] = $request->file('imagem')->store('personagens','public');
        }

        if ($hasPointBuyData) {
             $data['base_atributos'] = json_encode($baseAtributos);
        }

        $personagem->update($data);

        // 4. Recálculo e Salvamento
        $personagem->atributos = json_encode($this->calcularAtributos($personagem));
        $this->atribuirPericiasIniciais($personagem); // Re-atribui em caso de mudança de Classe/Origem
        $personagem->save();

        return redirect()->route('personagens.index')
            ->with('success','Personagem atualizado com sucesso!');
    }

    public function destroy(Personagem $personagem)
    {
        $this->authorize('delete',$personagem);
        if($personagem->imagem) Storage::disk('public')->delete($personagem->imagem);
        $personagem->delete();
        return redirect()->route('personagens.index')->with('success','Personagem deletado com sucesso!');
    }

}
