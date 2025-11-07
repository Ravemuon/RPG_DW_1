@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Detalhes do Sistema: {{ $sistema->nome }}</h1>
        <div>
            <a href="{{ route('sistemas.edit', $sistema) }}" class="btn btn-warning">Editar</a>
            <form action="{{ route('sistemas.destroy', $sistema) }}" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir este sistema?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Excluir</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Informações Principais</h5>
            <dl class="row">
                <dt class="col-sm-3">ID:</dt>
                <dd class="col-sm-9">{{ $sistema->id }}</dd>

                <dt class="col-sm-3">Nome:</dt>
                <dd class="col-sm-9">{{ $sistema->nome }}</dd>

                <dt class="col-sm-3">Descrição:</dt>
                <dd class="col-sm-9">{!! nl2br(e($sistema->descricao)) !!}</dd>

                <dt class="col-sm-3">Foco:</dt>
                <dd class="col-sm-9">{{ $sistema->foco ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Mecânica Principal:</dt>
                <dd class="col-sm-9">{{ $sistema->mecanica_principal ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Complexidade:</dt>
                <dd class="col-sm-9">{{ $sistema->complexidade ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Página de Ref.:</dt>
                <dd class="col-sm-9">{{ $sistema->pagina ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Criado em:</dt>
                <dd class="col-sm-9">{{ $sistema->created_at->format('d/m/Y H:i:s') }}</dd>

                <dt class="col-sm-3">Atualizado em:</dt>
                <dd class="col-sm-9">{{ $sistema->updated_at->format('d/m/Y H:i:s') }}</dd>
            </dl>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Atributos (Máx: {{ $sistema->max_atributos }})</h5>
            <ul class="list-group list-group-flush">
                @for ($i = 1; $i <= 6; $i++)
                    @php $attr_name = "atributo{$i}_nome"; @endphp
                    @if ($sistema->$attr_name)
                        <li class="list-group-item">
                            <strong>Atributo {{ $i }}:</strong> {{ $sistema->$attr_name }}
                        </li>
                    @endif
                @endfor
            </ul>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Regras Opcionais (JSON)</h5>
            @if ($sistema->regras_opcionais)
                <pre class="bg-light p-3 border rounded"><code>{{ json_encode($sistema->regras_opcionais, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
            @else
                <p>Nenhuma regra opcional definida.</p>
            @endif
        </div>
    </div>

    <a href="{{ route('sistemas.index') }}" class="btn btn-secondary mt-4">Voltar para a Lista</a>
</div>
@endsection
