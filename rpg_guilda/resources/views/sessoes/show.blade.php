@extends('layouts.app')

@section('title', 'Relatório de Sessão: ' . $sessao->titulo)

@section('content')

<div class="container mx-auto p-4">
<div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 shadow-2xl rounded-lg p-8">

    <header class="mb-8 border-b pb-4 border-gray-200 dark:border-gray-700">
        <h1 class="text-4xl font-extrabold text-gray-900 dark:text-gray-100">{{ $sessao->titulo }}</h1>
        <p class="text-lg text-indigo-600 dark:text-indigo-400 mt-2">
            Campanha: <a href="{{ route('campanhas.show', $sessao->campanha) }}" class="hover:underline">{{ $sessao->campanha->nome }}</a>
        </p>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Sessão realizada em: {{ \Carbon\Carbon::parse($sessao->data_sessao)->format('d/m/Y') }}
        </p>
    </header>

    <section class="space-y-6 text-gray-700 dark:text-gray-300">
        <div>
            <h2 class="text-2xl font-semibold mb-2 text-gray-800 dark:text-gray-200">Resumo da Aventura</h2>
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md shadow-inner">
                <p class="whitespace-pre-wrap">{{ $sessao->resumo ?? 'Nenhum resumo detalhado fornecido.' }}</p>
            </div>
        </div>

        <div>
            <h2 class="text-2xl font-semibold mb-2 text-gray-800 dark:text-gray-200">Personagens Envolvidos (Placeholder)</h2>
            <ul class="list-disc list-inside ml-4">
                <li>*... Personagem 1 ...*</li>
                <li>*... Personagem 2 ...*</li>
            </ul>
        </div>

        <div>
            <h2 class="text-2xl font-semibold mb-2 text-gray-800 dark:text-gray-200">Recompensas Distribuídas (Placeholder)</h2>
            <p>Cada jogador recebeu <strong>{{ $sessao->xp_ganho ?? '100' }} XP</strong> e <strong>{{ $sessao->ouro_ganho ?? '50' }} Ouro</strong>.</p>
        </div>
    </section>

    <footer class="mt-8 pt-4 border-t border-gray-200 dark:border-gray-700 text-right">
        <p class="text-sm text-gray-500 dark:text-gray-400">Relatório criado pelo Mestre em {{ $sessao->created_at->format('d/m/Y H:i') }}</p>
    </footer>
</div>


</div>
@endsection
