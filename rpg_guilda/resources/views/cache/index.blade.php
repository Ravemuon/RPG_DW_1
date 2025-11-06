@extends('layouts.app')

@section('title', 'Gerenciamento de Cache')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Cache</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('cache.store') }}" class="mb-4">
        @csrf
        <div class="row g-2">
            <div class="col-md-3">
                <input type="text" name="key" class="form-control" placeholder="Chave" required>
            </div>
            <div class="col-md-4">
                <textarea name="value" class="form-control" placeholder="Valor" required></textarea>
            </div>
            <div class="col-md-2">
                <input type="number" name="expiration" class="form-control" placeholder="Expiração (seg)" required>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">Salvar</button>
            </div>
        </div>
    </form>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Chave</th>
                <th>Valor</th>
                <th>Expiração</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($caches as $cache)
            <tr>
                <td>{{ $cache->key }}</td>
                <td>{{ $cache->value }}</td>
                <td>{{ $cache->expiration }}</td>
                <td>
                    <form action="{{ route('cache.destroy', $cache->key) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Remover</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
