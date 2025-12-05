<?php

namespace App\Http\Controllers;

use App\Models\Personagem;
use App\Models\Campanha;
use App\Models\Raca;
use App\Models\Classe;
use App\Models\Origem;
use App\Models\Sistema;
use App\Models\Pericia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreStep1Request;
use App\Http\Requests\StoreStep2Request;
use App\Http\Requests\StoreStep3Request;
use App\Http\Requests\StoreStep4Request;
use App\Http\Requests\StoreStep5Request;

class PersonagemCreatorController extends Controller
{
    const SESSION_KEY = 'personagem_data';
    const ATRIBUTOS_PADRAO = ['forca', 'destreza', 'constituicao', 'inteligencia', 'sabedoria', 'carisma'];

    // ============================================
    // UTILS
    // ============================================

    protected function checkSession()
    {
        if (!Session::has(self::SESSION_KEY) || !is_array(Session::get(self::SESSION_KEY))) {
            return redirect()->route('personagens.create')->with('error', 'Processo de criação não iniciado.');
        }

        return Session::get(self::SESSION_KEY);
    }

    protected function clearSession()
    {
        Session::forget(self::SESSION_KEY);
    }

    protected function getBaseAttributes(int $sistemaId): array
    {
        $sistema = Sistema::find($sistemaId);
        if ($sistema && method_exists($sistema, 'getAtributosBase')) {
            return $sistema->getAtributosBase();
        }
        return self::ATRIBUTOS_PADRAO;
    }

    protected function getAtributosValidationRules(int $sistemaId, string $baseKey): array
    {
        $atributosEsperados = $this->getBaseAttributes($sistemaId);
        $rules = [
            $baseKey => ['required', 'array', function($attribute, $value, $fail) use ($atributosEsperados) {
                if (count($value) !== count($atributosEsperados)) {
                    $fail("A lista de atributos deve conter exatamente " . count($atributosEsperados) . " valores.");
                }
            }],
        ];

        foreach ($atributosEsperados as $atributo) {
            $rules["{$baseKey}.{$atributo}"] = ['required','integer','min:1','max:20'];
        }

        return $rules;
    }

    private function calcularProgresso(Personagem $personagem)
    {
        $basico = !empty($personagem->nome) && !empty($personagem->campanha_id) && !empty($personagem->nivel);
        $raca_classe_origem = !empty($personagem->raca_id) && !empty($personagem->classe_id) && !empty($personagem->bonus_proficiencia);
        $atributosArray = is_string($personagem->atributos) ? json_decode($personagem->atributos,true):($personagem->atributos??[]);
        $atributos = !empty($atributosArray);
        $vida_sanidade_sorte = !empty($personagem->vida);
        $periciasArray = is_string($personagem->pericias)?json_decode($personagem->pericias,true):($personagem->pericias??[]);
        $pericias = !empty($periciasArray);
        $inventario_equipamento = true;

        $etapas = [$basico,$raca_classe_origem,$atributos,$vida_sanidade_sorte,$pericias,$inventario_equipamento];
        $completas = count(array_filter($etapas));
        $total = count($etapas);
        $completo = $completas === $total;

        return [
            'basico'=>$basico,
            'raca_classe_origem'=>$raca_classe_origem,
            'atributos'=>$atributos,
            'vida_sanidade_sorte'=>$vida_sanidade_sorte,
            'pericias'=>$pericias,
            'inventario_equipamento'=>$inventario_equipamento,
            'completo'=>$completo,
            'porcentagem'=>round(($completas/$total)*100),
        ];
    }

    // ============================================
    // CREATE - SESSION BASED
    // ============================================

    public function create(Request $request)
    {
        $campanhaId = $request->query('campanha');
        $campanha = Campanha::find($campanhaId);
        if (!$campanha) return redirect()->route('campanhas.index')->with('error','Selecione uma campanha válida.');

        $data = Session::get(self::SESSION_KEY, []);
        $data = array_merge($data, [
            'campanha_id'=>$campanha->id,
            'sistema_id'=>$campanha->sistema_id,
            'user_id'=>Auth::id(),
        ]);
        Session::put(self::SESSION_KEY,$data);

        $sistemas = Sistema::all();
        $campanhas = Campanha::all();

        return view('personagens.create.step1',compact('campanha','sistemas','campanhas','data'));
    }

    public function storeStep1(StoreStep1Request $request)
    {
        $sessionData = $this->checkSession();
        if (!is_array($sessionData)) return $sessionData;

        $validatedData = $request->validated();

        $imageTempPath = null;
        if ($request->hasFile('imagem_upload')) {
            $imageTempPath = $request->file('imagem_upload')->store('temp/personagens','public');
        }

        $personagemData = array_merge($sessionData,[
            'nome'=>$validatedData['nome'],
            'nivel'=>$validatedData['nivel'],
            'xp'=>$validatedData['xp'],
            'descricao'=>$validatedData['descricao']??null,
            'historia'=>$validatedData['historia']??null,
            'personalidade'=>$validatedData['personalidade']??null,
            'pagina'=>$validatedData['pagina']??null,
            'ativo'=>$request->has('ativo_checkbox_only'),
            'imagem_temp_path'=>$imageTempPath,
        ]);

        Session::put(self::SESSION_KEY,$personagemData);

        return redirect()->route('personagens.step2')->with('success','Dados básicos salvos.');
    }

    public function step2()
    {
        $sessionData = $this->checkSession();
        if (!is_array($sessionData)) return $sessionData;
        if (empty($sessionData['nome'])) return redirect()->route('personagens.create',['campanha'=>$sessionData['campanha_id']])->with('error','Complete o Passo 1.');

        $sistemaId = $sessionData['sistema_id'];
        $racas = Raca::where('sistema_id',$sistemaId)->orderBy('nome')->get();
        $classes = Classe::where('sistema_id',$sistemaId)->orderBy('nome')->get();
        $origens = Origem::where('sistema_id',$sistemaId)->orderBy('nome')->get();

        return view('personagens.create.step2',compact('sessionData','racas','classes','origens'));
    }

    public function storeStep2(StoreStep2Request $request)
    {
        $sessionData = $this->checkSession();
        if (!is_array($sessionData)) return $sessionData;

        $validatedData = $request->validated();

        $personagemData = array_merge($sessionData,[
            'raca_id'=>$validatedData['raca_id'],
            'classe_id'=>$validatedData['classe_id'],
            'origem_id'=>$validatedData['origem_id']??null,
            'bonus_proficiencia'=>$validatedData['bonus_proficiencia'],
        ]);

        Session::put(self::SESSION_KEY,$personagemData);

        return redirect()->route('personagens.step3')->with('success','Raça, Classe e Origem salvas.');
    }

    public function step3()
    {
        $sessionData = $this->checkSession();
        if (!is_array($sessionData)) return $sessionData;
        if (empty($sessionData['raca_id']) || empty($sessionData['classe_id'])) return redirect()->route('personagens.step2')->with('error','Complete o Passo 2.');

        $atributosSistema = $this->getBaseAttributes($sessionData['sistema_id']);
        $atributosSalvos = $sessionData['atributos']??[];

        return view('personagens.create.step3',[
            'data'=>$sessionData,
            'atributosSistema'=>$atributosSistema,
            'atributosSalvos'=>$atributosSalvos,
        ]);
    }

    public function storeStep3(StoreStep3Request $request)
    {
        $sessionData = $this->checkSession();
        if (!is_array($sessionData)) return $sessionData;

        $validatedData = $request->validated();

        $personagemData = array_merge($sessionData,[
            'atributos'=>$validatedData['atributos'],
        ]);

        Session::put(self::SESSION_KEY,$personagemData);

        return redirect()->route('personagens.step4')->with('success','Atributos salvos.');
    }

    public function step4()
    {
        $sessionData = $this->checkSession();
        if (!is_array($sessionData)) return $sessionData;
        if (empty($sessionData['atributos'])) return redirect()->route('personagens.step3')->with('error','Complete o Passo 3.');

        return view('personagens.create.step4',['data'=>$sessionData]);
    }

    public function storeStep4(StoreStep4Request $request)
    {
        $sessionData = $this->checkSession();
        if (!is_array($sessionData)) return $sessionData;

        $validatedData = $request->validated();

        $personagemData = array_merge($sessionData,[
            'vida'=>$validatedData['vida'],
            'sanidade'=>$validatedData['sanidade']??null,
            'sorte'=>$validatedData['sorte']??null,
        ]);

        Session::put(self::SESSION_KEY,$personagemData);

        return redirect()->route('personagens.step5')->with('success','Vida e Recursos salvos.');
    }

    public function step5()
    {
        $sessionData = $this->checkSession();
        if (!is_array($sessionData)) return $sessionData;
        if (!isset($sessionData['vida'])) return redirect()->route('personagens.step4')->with('error','Complete o Passo 4.');

        $periciasSistema = Pericia::where('sistema_id',$sessionData['sistema_id'])->orderBy('nome')->get();

        return view('personagens.create.step5',[
            'data'=>$sessionData,
            'periciasSistema'=>$periciasSistema,
        ]);
    }

    public function storeStep5(StoreStep5Request $request)
    {
        $sessionData = $this->checkSession();
        if (!is_array($sessionData)) return $sessionData;

        $validatedData = $request->validated();

        $personagemData = array_merge($sessionData,[
            'pericias'=>$validatedData['pericias']??[],
            'inventario'=>$validatedData['inventario']??null,
            'equipamento'=>$validatedData['equipamento']??null,
        ]);

        Session::put(self::SESSION_KEY,$personagemData);

        return redirect()->route('personagens.final')->with('success','Perícias e Inventário salvos.');
    }

    public function final()
    {
        $sessionData = $this->checkSession();
        if (!is_array($sessionData)) return $sessionData;

        if (empty($sessionData['nome']) || empty($sessionData['atributos']) || empty($sessionData['vida']) || !isset($sessionData['raca_id']) || !isset($sessionData['classe_id'])) {
            return redirect()->route('personagens.create',['campanha'=>$sessionData['campanha_id']])
                ->with('error','Faltam dados obrigatórios.');
        }

        $campanha = Campanha::find($sessionData['campanha_id']);
        $sistema = Sistema::find($sessionData['sistema_id']);
        $raca = Raca::find($sessionData['raca_id']);
        $classe = Classe::find($sessionData['classe_id']);
        $origem = $sessionData['origem_id'] ? Origem::find($sessionData['origem_id']):null;
        $atributos = $sessionData['atributos']??[];
        $pericias = $sessionData['pericias']??[];

        return view('personagens.create.stepfinal',compact('sessionData','campanha','sistema','raca','classe','origem','atributos','pericias'));
    }

    public function storeFinal(Request $request)
    {
        $sessionData = $this->checkSession();
        if (!is_array($sessionData)) return $sessionData;

        $finalData = $sessionData;
        $finalData['user_id'] = Auth::id();

        if (isset($finalData['imagem_temp_path']) && Storage::disk('public')->exists($finalData['imagem_temp_path'])) {
            try {
                $directory = 'personagens/'.Auth::id();
                if (!Storage::disk('public')->exists($directory)) Storage::disk('public')->makeDirectory($directory);
                $newFileName = time().'-'.uniqid().'.'.pathinfo($finalData['imagem_temp_path'],PATHINFO_EXTENSION);
                $newPath = $directory.'/'.$newFileName;
                Storage::disk('public')->move($finalData['imagem_temp_path'],$newPath);
                $finalData['imagem'] = $newPath;
            } catch (\Exception $e) {
                \Log::error('Erro ao mover imagem: '.$e->getMessage());
                $finalData['imagem'] = null;
            }
        } else $finalData['imagem'] = null;

        unset($finalData['imagem_temp_path']);

        try {
            $personagem = DB::transaction(function() use($finalData){
                $finalData['atributos'] = json_encode($finalData['atributos']??[]);
                $finalData['pericias'] = json_encode($finalData['pericias']??[]);
                return Personagem::create($finalData);
            });

            $this->clearSession();

            return redirect()->route('personagens.show',$personagem)->with('success','Personagem criado com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao salvar personagem: '.$e->getMessage(),$finalData??[]);
            if (isset($sessionData['imagem_temp_path'])) Storage::disk('public')->delete($sessionData['imagem_temp_path']);
            return back()->with('error','Falha ao salvar personagem.');
        }
    }

    // ============================================
    // EDIT STEPS 1-5
    // ============================================

    public function editStep1(Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) abort(403);
        return view('personagens.edit.step1',['personagem'=>$personagem]);
    }

    public function updateStep1(StoreStep1Request $request, Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) abort(403);

        $validatedData = $request->validated();

        // Handle image update
        $oldImage = $personagem->imagem;
        if ($request->hasFile('imagem_upload')) {
            $newPath = $request->file('imagem_upload')->store('personagens/'.Auth::id(),'public');
            if ($oldImage && Storage::disk('public')->exists($oldImage)) Storage::disk('public')->delete($oldImage);
            $validatedData['imagem'] = $newPath;
        } elseif ($request->boolean('remove_imagem') && $oldImage) {
            if (Storage::disk('public')->exists($oldImage)) Storage::disk('public')->delete($oldImage);
            $validatedData['imagem'] = null;
        } else {
            $validatedData['imagem'] = $oldImage;
        }

        $personagem->update($validatedData);

        return redirect()->route('personagens.editStep2',$personagem)->with('success','Passo 1 atualizado.');
    }

    public function editStep2(Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) abort(403);

        $sistemaId = $personagem->sistema_id;
        $racas = Raca::where('sistema_id',$sistemaId)->orderBy('nome')->get();
        $classes = Classe::where('sistema_id',$sistemaId)->orderBy('nome')->get();
        $origens = Origem::where('sistema_id',$sistemaId)->orderBy('nome')->get();

        return view('personagens.edit.step2',compact('personagem','racas','classes','origens'));
    }

    public function updateStep2(StoreStep2Request $request, Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) abort(403);
        $validated = $request->validated();
        $personagem->update([
            'raca_id'=>$validated['raca_id'],
            'classe_id'=>$validated['classe_id'],
            'origem_id'=>$validated['origem_id']??null,
            'bonus_proficiencia'=>$validated['bonus_proficiencia'],
        ]);
        return redirect()->route('personagens.editStep3',$personagem)->with('success','Passo 2 atualizado.');
    }

    public function editStep3(Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) abort(403);
        $atributosSistema = $this->getBaseAttributes($personagem->sistema_id);
        $atributosSalvos = json_decode($personagem->atributos??'{}',true);
        return view('personagens.edit.step3',['personagem'=>$personagem,'atributosSistema'=>$atributosSistema,'atributosSalvos'=>$atributosSalvos]);
    }

    public function updateStep3(StoreStep3Request $request, Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) abort(403);
        $validated = $request->validated();
        $personagem->atributos = json_encode($validated['atributos']);
        $personagem->save();
        return redirect()->route('personagens.editStep4',$personagem)->with('success','Passo 3 atualizado.');
    }

    public function editStep4(Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) abort(403);
        return view('personagens.edit.step4',['personagem'=>$personagem]);
    }

    public function updateStep4(StoreStep4Request $request, Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) abort(403);
        $validated = $request->validated();
        $personagem->update($validated);
        return redirect()->route('personagens.editStep5',$personagem)->with('success','Passo 4 atualizado.');
    }

    public function editStep5(Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) abort(403);
        $periciasSistema = Pericia::where('sistema_id',$personagem->sistema_id)->orderBy('nome')->get();
        $pericias = json_decode($personagem->pericias??'[]',true);
        return view('personagens.edit.step5',compact('personagem','periciasSistema','pericias'));
    }

    public function updateStep5(StoreStep5Request $request, Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) abort(403);
        $validated = $request->validated();
        $personagem->pericias = json_encode($validated['pericias']??[]);
        $personagem->inventario = $validated['inventario']??null;
        $personagem->equipamento = $validated['equipamento']??null;
        $personagem->save();
        return redirect()->route('personagens.editOverview',$personagem)->with('success','Passo 5 atualizado.');
    }

    // ============================================
    // SIMPLE EDIT
    // ============================================

    public function simpleEdit(Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) abort(403);

        $sistema = $personagem->campanha->sistema;
        $racas = Raca::where('sistema_id',$sistema->id)->orderBy('nome')->get();
        $classes = Classe::where('sistema_id',$sistema->id)->orderBy('nome')->get();
        $origens = Origem::where('sistema_id',$sistema->id)->orderBy('nome')->get();

        return view('personagens.edit.simple_edit',compact('personagem','racas','classes','origens'));
    }

    public function updateSimpleEdit(Request $request, Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) abort(403);
        $validated = $request->validate([
            'nome'=>['required','string','max:100'],
            'raca_id'=>['nullable','exists:racas,id'],
            'classe_id'=>['nullable','exists:classes,id'],
            'origem_id'=>['nullable','exists:origens,id'],
        ]);

        $personagem->update($validated);

        return redirect()->route('personagens.show',$personagem)->with('success','Nome, raça, classe e origem atualizados.');
    }

    // ============================================
    // SORTING METHODS (AJAX)
    // ============================================

    public function sortearAtributos(Request $request)
    {
        if (!$request->has('sistema_id')) return response()->json(['error'=>'Sistema ID necessário'],400);

        $atributosSistema = $this->getBaseAttributes($request->input('sistema_id'));
        $result = [];
        foreach($atributosSistema as $attr){
            $dados = [];
            for($i=0;$i<4;$i++) $dados[]=rand(1,6);
            sort($dados);
            unset($dados[0]);
            $result[$attr]=array_sum($dados);
        }

        return response()->json(['success'=>true,'atributos'=>$result]);
    }

    public function sortearVida(Request $request)
    {
        if (!$request->has(['classe_id','atributos'])) return response()->json(['error'=>'Classe ID e atributos necessários'],400);

        $atributos = $request->input('atributos');
        $conMod = floor((($atributos['constituicao']??10)-10)/2);
        $vidaSorteada = max(1,rand(1,8)+$conMod);

        return response()->json(['success'=>true,'vida'=>$vidaSorteada]);
    }
}
