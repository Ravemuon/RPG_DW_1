<?php

namespace App\Http\Controllers;

use App\Models\Sistema;
use App\Models\Classe;
use App\Models\Origem;
use App\Models\Raca;
use App\Models\Pericia;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\ValidationException;

class SistemaController extends Controller
{

    /**
     * Lista todos os sistemas com suas relações (classes, origens, raças e perícias).
     */
    public function index()
    {
        // Carrega todos os sistemas e suas relações para exibição
        $sistemas = Sistema::with(['classes', 'origens', 'racas', 'pericias'])->get();
        return view('sistemas.index', compact('sistemas'));
    }

    /**
     * Exibe o formulário para criar um novo sistema.
     */
    public function create()
    {
        return view('sistemas.create');
    }

    /**
     * Armazena um novo sistema.
     */
    public function store(Request $request)
    {
        try {
            // Regras de validação atualizadas
            $validatedData = $request->validate([
                'nome' => 'required|string|max:100|unique:sistemas,nome',
                'descricao' => 'nullable|string',
                'foco' => 'nullable|string|max:100',
                'mecanica_principal' => 'nullable|string|max:50',
                'complexidade' => 'nullable|string|max:50',

                // Novos campos da migration
                'usa_sanidade' => 'nullable|boolean',
                'formula_pontos_vida' => 'nullable|string|max:200',
                'recursos' => 'nullable|json', // Se vier como string JSON do formulário
                'regras_opcionais' => 'nullable|json', // Se vier como string JSON do formulário

                // Campos que serão usados para montar o JSON 'atributos'
                'max_atributos' => 'required|integer|min:0|max:6', // 0 para sistemas sem atributos.
                'atributo1_chave' => 'nullable|string|max:50', // Assumindo que você agora tem uma 'chave'
                'atributo1_nome' => 'nullable|string|max:50',
                'atributo2_chave' => 'nullable|string|max:50',
                'atributo2_nome' => 'nullable|string|max:50',
                'atributo3_chave' => 'nullable|string|max:50',
                'atributo3_nome' => 'nullable|string|max:50',
                'atributo4_chave' => 'nullable|string|max:50',
                'atributo4_nome' => 'nullable|string|max:50',
                'atributo5_chave' => 'nullable|string|max:50',
                'atributo5_nome' => 'nullable|string|max:50',
                'atributo6_chave' => 'nullable|string|max:50',
                'atributo6_nome' => 'nullable|string|max:50',
            ]);

            // Lógica para montar o campo 'atributos' (JSON)
            $atributos = [];
            $maxAtributos = (int) $validatedData['max_atributos'];
            for ($i = 1; $i <= $maxAtributos; $i++) {
                $chave = $validatedData["atributo{$i}_chave"] ?? null;
                $nome = $validatedData["atributo{$i}_nome"] ?? null;

                if ($chave && $nome) {
                    $atributos[$chave] = $nome;
                }
            }

            // Prepara os dados para criação (removendo os campos auxiliares)
            $dataToCreate = array_merge(
                $request->only([
                    'nome', 'descricao', 'foco', 'mecanica_principal', 'complexidade',
                    'usa_sanidade', 'formula_pontos_vida', 'recursos', 'regras_opcionais'
                ]),
                ['atributos' => $atributos]
            );

            // Se 'recursos' e 'regras_opcionais' vierem como strings JSON, eles precisam ser decodificados
            if (isset($dataToCreate['recursos']) && is_string($dataToCreate['recursos'])) {
                $dataToCreate['recursos'] = json_decode($dataToCreate['recursos'], true);
            }
             if (isset($dataToCreate['regras_opcionais']) && is_string($dataToCreate['regras_opcionais'])) {
                $dataToCreate['regras_opcionais'] = json_decode($dataToCreate['regras_opcionais'], true);
            }
             // Lembre-se que o campo 'usa_sanidade' deve vir do form, talvez como checkbox (null ou 'on')
            $dataToCreate['usa_sanidade'] = $request->has('usa_sanidade');


            // Cria o novo sistema no banco de dados
            Sistema::create($dataToCreate);

            return redirect()->route('sistemas.index')->with('success', 'Sistema criado com sucesso!');
        } catch (ValidationException $e) {
            // Se ocorrer erro de validação, retorna com erros
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    // ... show, edit ...

    /**
     * Exibe o formulário para editar um sistema.
     */
    public function edit(Sistema $sistema)
    {
         // Para facilitar a edição, você pode querer desempacotar os atributos JSON
        // em variáveis separadas aqui antes de enviar para a view,
        // mas a implementação da view não está visível.
        return view('sistemas.edit', compact('sistema'));
    }

    /**
     * Atualiza as informações de um sistema existente.
     */
    public function update(Request $request, Sistema $sistema)
    {
        try {
             // Regras de validação atualizadas
            $validatedData = $request->validate([
                'nome' => 'required|string|max:100|unique:sistemas,nome,' . $sistema->id,
                'descricao' => 'nullable|string',
                'foco' => 'nullable|string|max:100',
                'mecanica_principal' => 'nullable|string|max:50',
                'complexidade' => 'nullable|string|max:50',

                // Novos campos da migration
                'usa_sanidade' => 'nullable|boolean',
                'formula_pontos_vida' => 'nullable|string|max:200',
                'recursos' => 'nullable|json', // Se vier como string JSON do formulário
                'regras_opcionais' => 'nullable|json', // Se vier como string JSON do formulário

                // Campos que serão usados para montar o JSON 'atributos'
                'max_atributos' => 'required|integer|min:0|max:6',
                'atributo1_chave' => 'nullable|string|max:50',
                'atributo1_nome' => 'nullable|string|max:50',
                'atributo2_chave' => 'nullable|string|max:50',
                'atributo2_nome' => 'nullable|string|max:50',
                'atributo3_chave' => 'nullable|string|max:50',
                'atributo3_nome' => 'nullable|string|max:50',
                'atributo4_chave' => 'nullable|string|max:50',
                'atributo4_nome' => 'nullable|string|max:50',
                'atributo5_chave' => 'nullable|string|max:50',
                'atributo5_nome' => 'nullable|string|max:50',
                'atributo6_chave' => 'nullable|string|max:50',
                'atributo6_nome' => 'nullable|string|max:50',
            ]);

            // Lógica para montar o campo 'atributos' (JSON)
            $atributos = [];
            $maxAtributos = (int) $validatedData['max_atributos'];
            for ($i = 1; $i <= $maxAtributos; $i++) {
                 $chave = $validatedData["atributo{$i}_chave"] ?? null;
                $nome = $validatedData["atributo{$i}_nome"] ?? null;

                if ($chave && $nome) {
                    $atributos[$chave] = $nome;
                }
            }

            // Prepara os dados para atualização (removendo os campos auxiliares)
            $dataToUpdate = array_merge(
                $request->only([
                    'nome', 'descricao', 'foco', 'mecanica_principal', 'complexidade',
                    'usa_sanidade', 'formula_pontos_vida', 'recursos', 'regras_opcionais'
                ]),
                ['atributos' => $atributos]
            );

             // Se 'recursos' e 'regras_opcionais' vierem como strings JSON, eles precisam ser decodificados
            if (isset($dataToUpdate['recursos']) && is_string($dataToUpdate['recursos'])) {
                $dataToUpdate['recursos'] = json_decode($dataToUpdate['recursos'], true);
            }
             if (isset($dataToUpdate['regras_opcionais']) && is_string($dataToUpdate['regras_opcionais'])) {
                $dataToUpdate['regras_opcionais'] = json_decode($dataToUpdate['regras_opcionais'], true);
            }
             // Lembre-se que o campo 'usa_sanidade' deve vir do form, talvez como checkbox (null ou 'on')
            $dataToUpdate['usa_sanidade'] = $request->has('usa_sanidade');


            // Atualiza o sistema com os dados validados
            $sistema->update($dataToUpdate);

            return redirect()->route('sistemas.index')->with('success', 'Sistema atualizado com sucesso!');
        } catch (ValidationException $e) {
            // Se ocorrer erro de validação, retorna com erros
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Remove um sistema do banco de dados.
     */
    public function destroy(Sistema $sistema)
    {
        // Exclui o sistema e suas relações
        $sistema->delete();

        return redirect()->route('sistemas.index')->with('success', 'Sistema excluído com sucesso!');
    }

    /**
     * Redireciona para a página de classes do sistema.
     */
    public function classes(Sistema $sistema)
    {
        return redirect()->route('sistemas.classes.index', $sistema);
    }

    /**
     * Redireciona para a página de origens do sistema.
     */
    public function origens(Sistema $sistema)
    {
        return redirect()->route('sistemas.origens.index', $sistema);
    }

    /**
     * Redireciona para a página de raças do sistema.
     */
    public function racas(Sistema $sistema)
    {
        return redirect()->route('sistemas.racas.index', $sistema);
    }

    /**
     * Redireciona para a página de perícias do sistema.
     */
    public function pericias(Sistema $sistema)
    {
        return redirect()->route('sistemas.pericias.index', $sistema);
    }

    /**
     * Gera um PDF com todos os sistemas cadastrados.
     */
    public function exportarPdf()
    {
        // Carrega todos os sistemas para o PDF
        $sistemas = Sistema::all();
        $pdf = Pdf::loadView('sistemas.pdf', compact('sistemas'));
        return $pdf->download('sistemas.pdf');
    }

    /**
     * Gera um PDF de um único sistema.
     */
    public function visualizarPdf(Sistema $sistema)
    {
        // Carrega as relações do sistema para detalhar no PDF
        $sistema->load(['classes', 'origens', 'racas', 'pericias']);
        $pdf = Pdf::loadView('sistemas.pdf-unico', compact('sistema'));

        // Exibe o PDF diretamente no navegador
        return $pdf->stream("sistema_{$sistema->id}.pdf");
    }
}
