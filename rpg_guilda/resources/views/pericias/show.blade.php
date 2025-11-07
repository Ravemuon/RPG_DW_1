@extends('layouts.app')

@section('title', $pericia->nome)

@section('content')

<div class="container mx-auto p-4">
<div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 shadow-xl rounded-lg p-8">
<div class="flex justify-between items-center mb-6 border-b pb-4 border-gray-200 dark:border-gray-700">
<h1 class="text-4xl font-extrabold text-gray-900 dark:text-gray-100">{{ $pericia->nome }}</h1>
<div class="flex space-x-2">
@can('manage-pericias')
<a href="{{ route('pericias.edit', $pericia) }}"
class="px-3 py-1 bg-yellow-500 text-white text-sm font-semibold rounded-md hover:bg-yellow-600 transition">
Editar
</a>
@endcan
</div>
</div>

    <div class="text-lg text-gray-700 dark:text-gray-300 space-y-4">
        <p><strong>ID do Sistema:</strong> <span class="text-indigo-600 dark:text-indigo-400">{{ $pericia->sistema_id ?? 'N/A' }}</span></p>

        <h2 class="text-2xl font-semibold mt-6 mb-2 text-gray-800 dark:text-gray-200">Descrição</h2>
        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md border border-gray-200 dark:border-gray-600">
            <p class="whitespace-pre-wrap">{{ $pericia->descricao ?? 'Nenhuma descrição fornecida.' }}</p>
        </div>
    </div>

</div>


</div>
@endsection
