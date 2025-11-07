@extends('layouts.app')

@section('title', 'Classe: ' . $classe->nome)

@section('content')

<div class="container mx-auto p-4">
<div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 shadow-2xl rounded-lg p-8">

    <header class="mb-6 border-b pb-4 border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <h1 class="text-4xl font-extrabold text-gray-900 dark:text-gray-100">{{ $classe->nome }}</h1>
        @can('manage-classes')
            <a href="{{ route('classes.edit', $classe) }}"
               class="px-4 py-2 bg-yellow-500 text-white font-semibold rounded-md hover:bg-yellow-600 transition">
                Editar
            </a>
        @endcan
    </header>

    <section class="space-y-6 text-gray-700 dark:text-gray-300">

        <div>
            <h2 class="text-2xl font-semibold mb-2 text-gray-800 dark:text-gray-200">Descrição Geral</h2>
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md shadow-inner">
                <p class="whitespace-pre-wrap">{{ $classe->descricao ?? 'Nenhuma descrição detalhada fornecida.' }}</p>
            </div>
        </div>

        <div>
            <h2 class="text-2xl font-semibold mb-2 text-gray-800 dark:text-gray-200">Atributos Primários</h2>
            <p>Esta classe foca em: <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $classe->atributos_primarios ?? 'N/A' }}</span></p>
        </div>

        <div>
            <h2 class="text-2xl font-semibold mb-2 text-gray-800 dark:text-gray-200">Habilidades Iniciais (Placeholder)</h2>
            <ul class="list-disc list-inside ml-4">
                <li>Habilidade 1: Detalhe da primeira habilidade.</li>
                <li>Habilidade 2: Detalhe da segunda habilidade.</li>
                <li>*...*</li>
            </ul>
        </div>
    </section>

</div>


</div>
@endsection
