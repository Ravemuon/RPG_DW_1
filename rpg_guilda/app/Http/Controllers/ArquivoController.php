<?php

namespace App\Http\Controllers;

use App\Models\Arquivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArquivoController extends Controller
{
    /**
     * Upload de arquivo ou cadastro de URL
     * Pode ser chamado de qualquer página (chat, mapa, etc.)
     */
    public static function upload(Request $request, $campanha_id = null)
    {
        $request->validate([
            'arquivo' => 'nullable|file|max:10240', // max 10MB
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

        $arquivo = Arquivo::create([
            'usuario_id' => Auth::id(),
            'campanha_id' => $campanha_id,
            'nome_original' => $nome_original,
            'caminho' => $caminho,
            'tipo' => $tipo,
            'tamanho' => $tamanho
        ]);

        return $arquivo;
    }
}
