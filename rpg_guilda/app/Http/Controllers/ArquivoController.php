<?php

namespace App\Http\Controllers;

use App\Models\Arquivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArquivoController extends Controller
{
    /**
     * Upload universal de arquivo ou URL
     * Pode ser usado para usuário, chat ou campanha
     *
     * @param Request $request
     * @param int|null $usuario_id
     * @param int|null $campanha_id
     * @param int|null $chat_id
     * @return Arquivo
     */
    public static function upload(Request $request, $usuario_id = null, $campanha_id = null, $chat_id = null)
    {
        $request->validate([
            'arquivo' => 'nullable|file|max:10240', // até 10MB
            'url' => 'nullable|url',
        ]);

        $caminho = null;
        $nome_original = null;
        $tamanho = null;
        $tipo = null;

        if ($request->hasFile('arquivo')) {
            $file = $request->file('arquivo');
            $caminho = $file->store('uploads', 'public');
            $nome_original = $file->getClientOriginalName();
            $tamanho = $file->getSize();
            $tipo = $file->getClientOriginalExtension();
        } elseif ($request->url) {
            $caminho = $request->url;
            $nome_original = $request->url;
            $tipo = 'url';
        }

        // Se nenhum arquivo ou URL foi enviado
        if (!$caminho) {
            throw new \Exception('Nenhum arquivo ou URL válido fornecido.');
        }

        $arquivo = Arquivo::create([
            'usuario_id' => $usuario_id ?? Auth::id(),
            'campanha_id' => $campanha_id,
            'chat_id' => $chat_id,
            'nome_original' => $nome_original,
            'caminho' => $caminho,
            'tipo' => $tipo,
            'tamanho' => $tamanho
        ]);

        return $arquivo;
    }
}
