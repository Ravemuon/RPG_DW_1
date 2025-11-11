@extends('layouts.app')

@section('title', 'Editar Usuário')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">✏️ Editar Usuário</h4>
            <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">⬅️ Voltar</a>
        </div>

        <div class="card-body">
            <form action="{{ route('usuarios.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label fw-bold">Nome</label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control"
                        value="{{ old('name', $user->name ?? '') }}"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-bold">E-mail</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control"
                        value="{{ old('email', $user->email ?? '') }}"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-bold">Senha (opcional)</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control"
                        placeholder="Deixe em branco para manter a senha atual"
                    >
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success">
                        💾 Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
