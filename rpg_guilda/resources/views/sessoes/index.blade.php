@extends('layouts.app')

@section('title', 'Sessões da Campanha: ' . $campanha->titulo)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold">
            🎲 Sessões da Campanha: <span class="text-primary">{{ $campanha->titulo }}</span>
        </h2>

        {{-- Apenas o criador ou admin pode criar novas sessões --}}
        @if(auth()->check() && (auth()->user()->id === $campanha->criador_id || auth()->user()->tipo === 'administrador'))
            <a href="{{ route('sessoes.create', ['campanha' => $campanha->id]) }}" class="btn btn-warning">
                ➕ Criar Sessão
            </a>
        @endif
    </div>

    {{-- Exibir mensagens de sucesso --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Verifica se há sessões --}}
    @if($sessoes->isEmpty())
        <div class="text-center text-muted mt-5">
            <p>Nenhuma sessão criada ainda.</p>
            @if(auth()->check() && (auth()->user()->id === $campanha->criador_id || auth()->user()->tipo === 'administrador'))
                <a href="{{ route('sessoes.create', ['campanha' => $campanha->id]) }}" class="btn btn-outline-warning mt-2">
                    ➕ Criar primeira sessão
                </a>
            @endif
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Título</th>
                        <th>Data e Hora</th>
                        <th>Status</th>
                        <th>Participantes</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sessoes as $index => $sessao)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-semibold">{{ $sessao->titulo }}</td>
                            <td>{{ \Carbon\Carbon::parse($sessao->data_hora)->format('d/m/Y H:i') }}</td>
                            <td>
                                @php
                                    $cores = [
                                        'agendada' => 'primary',
                                        'em_andamento' => 'warning',
                                        'concluida' => 'success',
                                        'cancelada' => 'danger'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $cores[$sessao->status] ?? 'secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $sessao->status)) }}
                                </span>
                            </td>
                            <td>
                                {{ $sessao->personagens->count() }}
                            </td>
                            <td class="text-end">
                                <a href="{{ route('sessoes.show', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}" class="btn btn-sm btn-outline-primary">
                                    👁️ Ver
                                </a>

                                @if(auth()->check() && (auth()->user()->id === $campanha->criador_id || auth()->user()->tipo === 'administrador'))
                                    <a href="{{ route('sessoes.edit', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}" class="btn btn-sm btn-outline-secondary">
                                        ✏️ Editar
                                    </a>

                                    <form action="{{ route('sessoes.destroy', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja deletar esta sessão?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            🗑️ Excluir
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
