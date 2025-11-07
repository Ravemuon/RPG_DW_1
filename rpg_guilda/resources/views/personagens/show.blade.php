@extends('layouts.app')

@section('title', 'Ficha de ' . $personagem->nome)

@section('content')

<div class="container mx-auto p-4">
<div class="bg-white dark:bg-gray-900 shadow-2xl rounded-xl p-8 max-w-5xl mx-auto">

    <header class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-4 flex justify-between items-center">
        <h1 class="text-4xl font-extrabold text-indigo-600 dark:text-indigo-400">{{ $personagem->nome }}</h1>
        <div class="flex space-x-3">
            @can('update-personagem', $personagem)
                <a href="{{ route('personagens.edit', $personagem) }}"
                   class="px-4 py-2 bg-yellow-500 text-white font-semibold rounded-full shadow-md hover:bg-yellow-600 transition duration-150">
                    Editar Ficha
                </a>
            @endcan
        </div>
    </header>

    <section class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-2 space-y-6">
            {{-- Informações Básicas --}}
            <div class="bg-gray-50 dark:bg-gray-800 p-5 rounded-lg shadow-inner">
                <h2 class="text-2xl font-semibold mb-3 text-gray-800 dark:text-gray-200 border-b pb-2">Detalhes</h2>
                <p class="text-gray-700 dark:text-gray-300"><strong>Jogador:</strong> {{ $personagem->user->name }}</p>
                <p class="text-gray-700 dark:text-gray-300"><strong>Classe:</strong> {{ $personagem->classe->nome ?? 'N/A' }}</p>
                <p class="text-gray-700 dark:text-gray-300"><strong>Nível:</strong> {{ $personagem->nivel ?? 1 }}</p>
                <p class="text-gray-700 dark:text-gray-300">
                    <strong>Campanha:</strong>
                    @if ($personagem->campanha)
                        <a href="{{ route('campanhas.show', $personagem->campanha) }}" class="text-indigo-500 hover:text-indigo-700">{{ $personagem->campanha->nome }}</a>
                    @else
                        Nenhum
                    @endif
                </p>
            </div>

            {{-- Atributos/Perícias (Placeholder) --}}
            <div class="bg-gray-50 dark:bg-gray-800 p-5 rounded-lg shadow-inner">
                <h2 class="text-2xl font-semibold mb-3 text-gray-800 dark:text-gray-200 border-b pb-2">Perícias & Atributos</h2>
                <ul class="list-disc list-inside text-gray-700 dark:text-gray-300">
                    <li>Força: {{ $personagem->forca ?? 10 }}</li>
                    <li>Destreza: {{ $personagem->destreza ?? 10 }}</li>
                    <li>Inteligência: {{ $personagem->inteligencia ?? 10 }}</li>
                    <li>*... Lista de Perícias aqui ...*</li>
                </ul>
            </div>
        </div>

        {{-- Coluna da Imagem e Status --}}
        <div class="md:col-span-1 space-y-6">
            <div class="relative bg-gray-200 dark:bg-gray-700 h-64 rounded-lg flex items-center justify-center overflow-hidden">
                {{-- Placeholder para Imagem do Personagem  --}}
                <span class="text-gray-500 dark:text-gray-400 text-xl">Imagem do Personagem</span>
            </div>

            {{-- Status Vitais --}}
            <div class="bg-red-50 dark:bg-red-900 p-4 rounded-lg border border-red-200 dark:border-red-700">
                <p class="text-lg font-bold text-red-800 dark:text-red-300">HP: {{ $personagem->hp_atual ?? 10 }}/{{ $personagem->hp_max ?? 10 }}</p>
            </div>
        </div>
    </section>

    <section class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800 dark:text-gray-200">História & Notas</h2>
        <div class="bg-gray-50 dark:bg-gray-800 p-5 rounded-lg shadow-inner min-h-[150px]">
            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $personagem->historia ?? 'Nenhuma história registrada.' }}</p>
        </div>
    </section>
</div>


</div>
@endsection
