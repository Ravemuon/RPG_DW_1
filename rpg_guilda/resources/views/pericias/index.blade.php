@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="fw-bold m-0">🎯 Perícias de {{ $sistema->nome }}</h1>

        <!-- Botão para a Página de Detalhes do Sistema -->
        <a href="{{ route('sistemas.show', $sistema->id) }}" class="btn btn-secondary">
            <i class="bi bi-book"></i> Ver Sistema
        </a>

        <a href="{{ route('sistemas.pericias.create', $sistema->id) }}" class="btn btn-primary mb-3">
            <i class="bi bi-plus-circle me-1"></i> Nova Perícia
        </a>

    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($sistema->pericias->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>Nome</th>
                        <th>Sistema</th>
                        <th>Automática</th>
                        <th>Fórmula</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sistema->pericias as $pericia)
                        <tr>
                            <td class="fw-semibold">{{ $pericia->nome }}</td>
                            <td>{{ $pericia->sistemaRPG }}</td>
                            <td>{{ $pericia->automatica ? '✅ Sim' : '❌ Não' }}</td>
                            <td>{{ $pericia->formula ?? '—' }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('pericias.show', $pericia->id) }}" class="btn btn-sm btn-outline-primary" title="Ver Detalhes">
                                        👁️
                                    </a>
                                    <a href="{{ route('pericias.edit', $pericia->id) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                        ✏️
                                    </a>
                                    <form action="{{ route('pericias.destroy', $pericia->id) }}" method="POST" onsubmit="return confirm('Deseja realmente excluir esta perícia?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-info">Nenhuma perícia cadastrada ainda.</div>
    @endif

    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary mt-3">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>
@endsection
