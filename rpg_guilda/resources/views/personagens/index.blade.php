@extends('layouts.app')

@section('title','Meus Personagens')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">🧙 Meus Personagens</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('personagens.create') }}" class="btn btn-primary mb-3">
        <i class="bi bi-plus-circle"></i> Criar Novo Personagem
    </a>

    @if($personagens->isEmpty())
        <div class="alert alert-info">Você ainda não possui personagens.</div>
    @else
        <div class="row row-cols-1 row-cols-md-3 g-3">
            @foreach($personagens as $p)
                <div class="col">
                    <div class="card shadow-sm h-100">
                        <img src="{{ $p->imagem ? asset('storage/'.$p->imagem) : asset('images/default-avatar.png') }}" class="card-img-top" style="height:200px; object-fit:cover;">
                        <div class="card-body">
                            <h5 class="card-title">{{ $p->nome }}</h5>
                            <p class="card-text">
                                <strong>Raça:</strong> {{ $p->raca->nome ?? '-' }} <br>
                                <strong>Classe:</strong> {{ $p->classe->nome ?? '-' }} <br>
                                <strong>Origem:</strong> {{ $p->origem->nome ?? '-' }}
                            </p>
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('personagens.show',$p) }}" class="btn btn-sm btn-info">Ver</a>
                                <a href="{{ route('personagens.edit',$p) }}" class="btn btn-sm btn-warning">Editar</a>
                                <form action="{{ route('personagens.destroy',$p) }}" method="POST" onsubmit="return confirm('Deseja realmente deletar este personagem?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
