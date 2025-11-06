@extends('layouts.app')

@section('title', 'Livro do Aventureiro – Perfil')

@section('content')
<div class="container py-4">
    <div class="text-center mb-5">
        <h1 class="text-warning fw-bold">📜 Livro do Aventureiro</h1>
        <p class="text-light">Suas conquistas, campanhas e jornadas.</p>
    </div>

    <div class="card bg-dark border-warning shadow-lg mb-5">
        <div class="card-header text-center text-warning">
            <h3>Informações do Usuário</h3>
        </div>
        <div class="card-body">
            <p><strong>Nome:</strong> {{ $usuario->name }}</p>
            <p><strong>Email:</strong> {{ $usuario->email }}</p>
            <p><strong>Tipo de Usuário:</strong>
                @if($usuario->is_admin ?? false)
                    👑 Administrador
                @elseif($usuario->tipo === 'mestre')
                    🧙 Mestre
                @else
                    🎮 Jogador
                @endif
            </p>
            <p><strong>Personagens Criados:</strong> {{ $personagemCount }}</p>
        </div>
    </div>

    <div class="card bg-dark border-warning shadow-lg">
        <div class="card-header text-center text-warning">
            <h3>Campanhas Ativas</h3>
        </div>
        <div class="card-body">
            @if($campanhas->isEmpty())
                <p class="text-center text-secondary">Você ainda não participa de nenhuma campanha. ⚔️</p>
            @else
                <div class="table-responsive">
                    <table class="table table-dark table-bordered">
                        <thead>
                            <tr class="text-warning">
                                <th>Nome</th>
                                <th>Sistema</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($campanhas as $campanha)
                                <tr>
                                    <td>{{ $campanha->nome }}</td>
                                    <td>{{ $campanha->sistema }}</td>
                                    <td>
                                        @if($campanha->status === 'ativa')
                                            <span class="badge bg-success">Ativa</span>
                                        @elseif($campanha->status === 'pausada')
                                            <span class="badge bg-warning text-dark">Pausada</span>
                                        @else
                                            <span class="badge bg-secondary">Encerrada</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
