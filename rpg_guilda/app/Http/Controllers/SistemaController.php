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
            // Valida os dados enviados para criar o sistema
            $validatedData = $request->validate([
                'nome' => 'required|string|max:100|unique:sistemas,nome',
                'descricao' => 'nullable|string',
                'foco' => 'nullable|string|max:100',
                'mecanica_principal' => 'nullable|string|max:50',
                'complexidade' => 'nullable|string|max:50',
                'regras_opcionais' => 'nullable|json',
                'max_atributos' => 'required|integer|min:1|max:6',
                'atributo1_nome' => 'nullable|string|max:50',
                'atributo2_nome' => 'nullable|string|max:50',
                'atributo3_nome' => 'nullable|string|max:50',
                'atributo4_nome' => 'nullable|string|max:50',
                'atributo5_nome' => 'nullable|string|max:50',
                'atributo6_nome' => 'nullable|string|max:50',
                'pagina' => 'nullable|string|max:50',
            ]);

            // Cria o novo sistema no banco de dados
            Sistema::create($validatedData);

            return redirect()->route('sistemas.index')->with('success', 'Sistema criado com sucesso!');
        } catch (ValidationException $e) {
            // Se ocorrer erro de validação, retorna com erros
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Exibe os detalhes de um sistema específico.
     */
    public function show(Sistema $sistema)
    {
        // Carrega as relações do sistema, como classes, origens, raças e perícias
        $sistema->load(['classes', 'origens', 'racas', 'pericias']);
        return view('sistemas.show', compact('sistema'));
    }

    /**
     * Exibe o formulário para editar um sistema.
     */
    public function edit(Sistema $sistema)
    {
        return view('sistemas.edit', compact('sistema'));
    }

    /**
     * Atualiza as informações de um sistema existente.
     */
    public function update(Request $request, Sistema $sistema)
    {
        try {
            // Valida os dados para atualização
            $validatedData = $request->validate([
                'nome' => 'required|string|max:100|unique:sistemas,nome,' . $sistema->id,
                'descricao' => 'nullable|string',
                'foco' => 'nullable|string|max:100',
                'mecanica_principal' => 'nullable|string|max:50',
                'complexidade' => 'nullable|string|max:50',
                'regras_opcionais' => 'nullable|json',
                'max_atributos' => 'required|integer|min:1|max:6',
                'atributo1_nome' => 'nullable|string|max:50',
                'atributo2_nome' => 'nullable|string|max:50',
                'atributo3_nome' => 'nullable|string|max:50',
                'atributo4_nome' => 'nullable|string|max:50',
                'atributo5_nome' => 'nullable|string|max:50',
                'atributo6_nome' => 'nullable|string|max:50',
                'pagina' => 'nullable|string|max:50',
            ]);

            // Atualiza o sistema com os dados validados
            $sistema->update($validatedData);

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
