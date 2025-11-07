@extends('layouts.app')

@section('title', 'Editar Classe: ' . $classe->nome)

@section('content')

<div class="container mx-auto p-4">
<div class="max-w-5xl mx-auto bg-white dark:bg-gray-800 shadow-xl rounded-xl p-8">
<h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6 border-b pb-2">Editar Classe: <span class="text-indigo-600 dark:text-indigo-400">{{ $classe->nome }}</span></h1>

    <form action="{{ route('classes.update', $classe) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <h2 class="text-xl font-semibold text-indigo-600 dark:text-indigo-400">Informações Gerais</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nome --}}
                <div>
                    <label for="nome" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome da Classe</label>
                    <input type="text" name="nome" id="nome" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-700 dark:text-gray-100"
                           value="{{ old('nome', $classe->nome) }}" placeholder="Ex: Guerreiro, Ocultista">
                    @error('nome') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Sistema RPG --}}
                <div>
                    <label for="sistemaRPG" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sistema RPG Associado</label>
                    <input type="text" name="sistemaRPG" id="sistemaRPG" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-700 dark:text-gray-100"
                           value="{{ old('sistemaRPG', $classe->sistemaRPG) }}" placeholder="Ex: D&D 5e, Ordem Paranormal, Chamado de Cthulhu">
                    @error('sistemaRPG') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Descrição --}}
            <div>
                <label for="descricao" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descrição e Características</label>
                <textarea name="descricao" id="descricao" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-700 dark:text-gray-100"
                          placeholder="Descreva o papel da classe no jogo.">{{ old('descricao', $classe->descricao) }}</textarea>
                @error('descricao') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <h2 class="text-xl font-semibold text-teal-600 dark:text-teal-400 mt-8 pt-4 border-t dark:border-gray-700">Atributos Iniciais (Numéricos)</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Preencha apenas os campos relevantes para o **Sistema RPG** desta classe. Deixe os outros em branco.</p>

            {{-- Atributos Comuns (D20-like/Genérico) --}}
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h3 class="font-medium text-gray-700 dark:text-gray-300 mb-3">D&D/Genérico</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                    @php
                        $d20_attrs = ['forca', 'destreza', 'constituicao', 'inteligencia', 'sabedoria', 'carisma'];
                    @endphp
                    @foreach ($d20_attrs as $attr)
                        <div>
                            <label for="{{ $attr }}" class="block text-xs font-medium text-gray-500 dark:text-gray-400 capitalize">{{ $attr }}</label>
                            <input type="number" name="{{ $attr }}" id="{{ $attr }}"
                                class="w-full px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 dark:text-gray-100"
                                value="{{ old($attr, $classe->$attr) }}" min="0">
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Atributos Ordem Paranormal/Sistema Custom --}}
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h3 class="font-medium text-gray-700 dark:text-gray-300 mb-3">Ordem/Vigor/Geral</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                    @php
                        $op_attrs = ['agilidade', 'intelecto', 'presenca', 'vigor', 'nex', 'sanidade', 'pontos_vida'];
                    @endphp
                    @foreach ($op_attrs as $attr)
                        <div>
                            <label for="{{ $attr }}" class="block text-xs font-medium text-gray-500 dark:text-gray-400 capitalize">{{ $attr }}</label>
                            <input type="number" name="{{ $attr }}" id="{{ $attr }}"
                                class="w-full px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 dark:text-gray-100"
                                value="{{ old($attr, $classe->$attr) }}" min="0">
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Atributos Chamado de Cthulhu --}}
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h3 class="font-medium text-gray-700 dark:text-gray-300 mb-3">Chamado de Cthulhu (CTH)</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                    @php
                        $cth_attrs = ['forca_cth', 'destreza_cth', 'constituicao_cth', 'inteligencia_cth', 'poder', 'aparencia', 'educacao', 'tamanho', 'sanidade_cth'];
                    @endphp
                    @foreach ($cth_attrs as $attr)
                        <div>
                            <label for="{{ $attr }}" class="block text-xs font-medium text-gray-500 dark:text-gray-400 capitalize">{{ str_replace('_cth', ' (CTH)', $attr) }}</label>
                            <input type="number" name="{{ $attr }}" id="{{ $attr }}"
                                class="w-full px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 dark:text-gray-100"
                                value="{{ old($attr, $classe->$attr) }}" min="0">
                        </div>
                    @endforeach
                </div>
            </div>

            <h2 class="text-xl font-semibold text-purple-600 dark:text-purple-400 mt-8 pt-4 border-t dark:border-gray-700">Aspectos Customizados (JSON)</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Insira dados JSON brutos para aspectos, poderes e atributos customizados. Deixe em branco se não for aplicável. <br>Exemplo: <code>["Elemento: Fogo", "Magia: Voo"]</code></p>

            {{-- JSON Fields --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @php
                    // Função auxiliar para obter o valor formatado para o textarea
                    $getJsonValue = fn($field) => old($field) ? json_encode(old($field)) : ($classe->$field ? json_encode($classe->$field) : '');
                @endphp
                <div>
                    <label for="aspects" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Aspects (JSON Array)</label>
                    <textarea name="aspects" id="aspects" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-700 dark:text-gray-100">{{ $getJsonValue('aspects') }}</textarea>
                    @error('aspects') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="stunts" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stunts (JSON Array)</label>
                    <textarea name="stunts" id="stunts" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-700 dark:text-gray-100">{{ $getJsonValue('stunts') }}</textarea>
                    @error('stunts') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="poderes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Poderes (JSON Array)</label>
                    <textarea name="poderes" id="poderes" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-700 dark:text-gray-100">{{ $getJsonValue('poderes') }}</textarea>
                    @error('poderes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="atributos_custom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Atributos Customizados (JSON Object)</label>
                    <textarea name="atributos_custom" id="atributos_custom" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-700 dark:text-gray-100">{{ $getJsonValue('atributos_custom') }}</textarea>
                    @error('atributos_custom') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label for="fate_points" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fate Points (Se aplicável)</label>
                    <input type="number" name="fate_points" id="fate_points"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-700 dark:text-gray-100"
                        value="{{ old('fate_points', $classe->fate_points) }}" min="0">
                    @error('fate_points') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-end space-x-3 border-t pt-4">
            <a href="{{ route('classes.index') }}"
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
