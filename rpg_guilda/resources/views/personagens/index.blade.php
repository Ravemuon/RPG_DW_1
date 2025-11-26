@extends('layouts.app')

@section('title', 'Meus Personagens')

@section('content')

<div class="container mx-auto py-6 px-4 lg:px-8">

    {{-- Cabeçalho --}}
    <div class="flex justify-between items-center mb-10">
        <div>
            <h1 class="text-4xl font-extrabold bg-linear-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                Meus Personagens
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">
                Gerencie seus heróis, aventureiros e lendas.
            </p>
        </div>

        <a href="{{ route('personagens.create', ['campanha' => request()->query('campanha')]) }}"
           class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:bg-indigo-700 hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
            + Criar Personagem
        </a>
    </div>

    {{-- Mensagem de sucesso --}}
    @if (session('success'))
        <div class="bg-green-100 dark:bg-green-900 border-l-4 border-green-600 text-green-700 dark:text-green-200 p-4 mb-8 rounded-lg shadow-md">
            {{ session('success') }}
        </div>
    @endif

    {{-- Nenhum personagem --}}
    @if ($personagens->isEmpty())
        <div class="bg-white dark:bg-gray-800 p-12 rounded-2xl shadow-2xl text-center border border-gray-200 dark:border-gray-700">
            <p class="text-gray-600 dark:text-gray-400 text-xl font-medium">
                Você ainda não criou nenhum personagem.
            </p>
            <p class="text-indigo-500 mt-2 font-semibold">Que tal criar seu primeiro herói?</p>
        </div>
    @else

        {{-- GRID DE PERSONAGENS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">

            @foreach ($personagens as $personagem)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden transform transition-all duration-500 hover:scale-[1.03] hover:shadow-indigo-500/40 hover:-translate-y-1">

                    {{-- Imagem --}}
                    <div class="relative h-48">
                        @if ($personagem->imagem)
                            <img src="{{ Storage::url($personagem->imagem) }}"
                                 alt="Imagem de {{ $personagem->nome }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-300 dark:bg-gray-700">
                                <span class="text-gray-600 dark:text-gray-300 text-lg font-bold">Sem Imagem</span>
                            </div>
                        @endif

                        {{-- Tag raça --}}
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full bg-indigo-600 text-white text-xs font-bold shadow-lg">
                            {{ $personagem->raca->nome ?? 'Raça?' }}
                        </span>

                        {{-- Pontos de vida --}}
                        <span class="absolute bottom-3 right-3 px-3 py-1 rounded-lg bg-red-600 text-white text-sm font-bold shadow-lg">
                            PV: {{ $personagem->pv_atual ?? 0 }}/{{ $personagem->pv_maximo ?? 0 }}
                        </span>
                    </div>

                    {{-- Informações --}}
                    <div class="p-6">
                        <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white mb-1 truncate">
                            {{ $personagem->nome }}
                        </h2>

                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            {{ $personagem->classe->nome ?? 'Classe?' }} —
                            <span class="font-semibold">Nível {{ $personagem->nivel ?? 1 }}</span>
                        </p>

                        {{-- Sistema e campanha --}}
                        <div class="flex justify-between text-sm mb-4">
                            <span class="text-gray-700 dark:text-gray-300">
                                Campanha:
                                <span class="font-semibold text-indigo-600 dark:text-indigo-400">
                                    {{ $personagem->campanha->nome ?? 'Solo' }}
                                </span>
                            </span>

                            <span class="text-gray-700 dark:text-gray-300">
                                Sistema:
                                <span class="font-semibold text-indigo-600 dark:text-indigo-400">
                                    {{ $personagem->sistema->nome ?? 'N/A' }}
                                </span>
                            </span>
                        </div>

                        {{-- 🔧 Ações --}}
                        <div class="flex justify-end items-center space-x-4">

                            <a href="{{ route('personagens.show', $personagem) }}"
                               class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-semibold transition duration-200">
                                Ver
                            </a>

                            <a href="{{ route('personagens.edit', $personagem) }}"
                               class="text-yellow-600 hover:text-yellow-800 font-semibold transition duration-200">
                                Editar
                            </a>

                            <form action="{{ route('personagens.destroy', $personagem) }}"
                                  method="POST"
                                  onsubmit="return confirm('Tem certeza que deseja excluir {{ $personagem->nome }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-600 hover:text-red-800 font-semibold transition duration-200">
                                    Deletar
                                </button>
                            </form>

                        </div>
                    </div>
                </div>
            @endforeach

        </div>

    @endif

</div>

@endsection
