@extends('layouts.app')

@section('title', 'Criar Nova Perícia')

@section('content')

<div class="container mx-auto p-4">
<div class="max-w-xl mx-auto bg-white dark:bg-gray-800 shadow-xl rounded-lg p-6">
<h1 class="text-3xl font-bold mb-6 text-gray-800 dark:text-gray-100">Criar Nova Perícia</h1>

    <form action="{{ route('pericias.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label for="nome" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome da Perícia</label>
            <input type="text" name="nome" id="nome" required
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                   value="{{ old('nome') }}">
            @error('nome')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="descricao" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descrição</label>
            <textarea name="descricao" id="descricao" rows="4"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('descricao') }}</textarea>
            @error('descricao')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-md shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                Salvar Perícia
            </button>
        </div>
    </form>
</div>


</div>
@endsection
