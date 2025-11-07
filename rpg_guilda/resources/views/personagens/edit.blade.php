@extends('layouts.app')

@section('title', 'Editar Personagem: ' . $personagem->nome)

@section('content')

<div class="container mx-auto p-4">
<div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 shadow-xl rounded-xl p-8">
<h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6 border-b pb-2">Editar Personagem: <span class="text-green-600 dark:text-green-400">{{ $personagem->nome }}</span></h1>

    @php
        // Prepare lista de IDs de origens associadas para checagem
        $origensAtuais = $personagem->origens->pluck('id')->toArray();
    @endphp

    {{-- O action deve apontar para o método update do controller --}}
    <form action="{{ route('personagens.update', $personagem) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <h2 class="text-xl font-semibold text-indigo-600 dark:text-indigo-400">Detalhes Principais</h2>

            {{-- Nome do Personagem --}}
            <div>
                <label for="nome" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome Completo</label>
                <input type="text" name="nome" id="nome" required
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-700 dark:text-gray-100"
                       value="{{ old('nome', $personagem->nome) }}">
                @error('nome') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Classe (FK pelo Nome da Classe) --}}
                <div>
                    <label for="classe" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Classe</label>
                    {{-- Assumindo que $classes está disponível no controller (Nome como valor) --}}
                    <select name="classe" id="classe" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">Selecione a Classe</option>
                        @foreach ($classes as $classe)
                            <option value="{{ $classe->nome }}"
                                {{ old('classe', $personagem->classe) == $classe->nome ? 'selected' : '' }}>
                                {{ $classe->nome }} ({{ $classe->sistemaRPG ?? 'Sistema Genérico' }})
                            </option>
                        @endforeach
                    </select>
                    @error('classe') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Campanha --}}
                <div>
                    <label for="campanha_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Campanha</label>
                    {{-- Assumindo que $campanhas está disponível --}}
                    <select name="campanha_id" id="campanha_id" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">Selecione a Campanha</option>
                        @foreach ($campanhas as $campanha)
                            <option value="{{ $campanha->id }}"
                                {{ old('campanha_id', $personagem->campanha_id) == $campanha->id ? 'selected' : '' }}>
                                {{ $campanha->nome }}
                            </option>
                        @endforeach
                    </select>
                    @error('campanha_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Origens (Múltipla Seleção) --}}
            <div class="space-y-2 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow-inner">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Origens (Bônus de Atributos)</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-48 overflow-y-auto p-2 border border-gray-200 dark:border-gray-600 rounded-lg">
                    {{-- Assumindo que $origens está disponível no controller --}}
                    @foreach ($origens as $origem)
                        <div class="flex items-center">
                            <input id="origem-{{ $origem->id }}" name="origens[]" type="checkbox" value="{{ $origem->id }}"
                                   class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600"
                                   {{ in_array($origem->id, (array)old('origens', $origensAtuais)) ? 'checked' : '' }}>
                            <label for="origem-{{ $origem->id }}" class="ml-2 block text-sm text-gray-900 dark:text-gray-100 cursor-pointer">
                                {{ $origem->nome }}
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('origens') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Descrição/História --}}
            <div>
                <label for="descricao" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">História de Fundo / Descrição</label>
                <textarea name="descricao" id="descricao" rows="5"
                          class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-700 dark:text-gray-100"
                          placeholder="Descreva a história e a aparência física do seu personagem.">{{ old('descricao', $personagem->descricao) }}</textarea>
                @error('descricao') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Status Ativo --}}
            <div class="flex items-center pt-4 border-t dark:border-gray-700">
                <input id="ativo" name="ativo" type="checkbox"
                       class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600"
                       value="1" {{ old('ativo', $personagem->ativo) ? 'checked' : '' }}>
                <label for="ativo" class="ml-2 block text-sm font-medium text-gray-900 dark:text-gray-100">
                    Personagem Ativo (Aparece na lista de seleção para campanhas)
                </label>
                @error('ativo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-8 flex justify-end space-x-3 border-t pt-4">
            <a href="{{ route('personagens.show', $personagem) }}"
               class="px-5 py-2 text-gray-600 dark:text-gray-400 font-semibold rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-150">
                Cancelar
            </a>
            <button type="submit"
                    class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Salvar Alterações
            </button>
        </div>
    </form>
</div>


</div>
@endsection
