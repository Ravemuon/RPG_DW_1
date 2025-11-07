@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="fw-bold">Detalhes do Sistema: {{ $sistema->nome }}</h1>

        @if(auth()->user()->is_admin)
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('sistemas.edit', $sistema) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Editar
                </a>

                <form action="{{ route('sistemas.destroy', $sistema) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este sistema?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Excluir
                    </button>
                </form>

                <!-- Botão Gerar PDF -->
                <a href="{{ route('sistemas.exportar-pdf-unico', $sistema->id) }}" target="_blank" class="btn btn-danger">
                    <i class="bi bi-file-earmark-pdf"></i> Gerar PDF
                </a>
            </div>
        @endif
    </div>

    <!-- Card com descrição, visível para todos -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title text-primary fw-bold">Descrição</h5>
            <p class="card-text">{!! nl2br(e($sistema->descricao)) !!}</p>
            <div class="text-end">
                <small class="text-muted">Criado em: {{ $sistema->created_at->format('d/m/Y') }}</small>
            </div>
        </div>
    </div>

    @if(auth()->user()->is_admin)
        <!-- Card com detalhes administrativos -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title text-secondary fw-bold">Informações Administrativas</h5>
                <dl class="row mb-0">
                    <dt class="col-sm-3">ID:</dt>
                    <dd class="col-sm-9">{{ $sistema->id }}</dd>

                    <dt class="col-sm-3">Foco:</dt>
                    <dd class="col-sm-9">{{ $sistema->foco ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Mecânica Principal:</dt>
                    <dd class="col-sm-9">{{ $sistema->mecanica_principal ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Complexidade:</dt>
                    <dd class="col-sm-9">{{ $sistema->complexidade ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Página de Ref.:</dt>
                    <dd class="col-sm-9">{{ $sistema->pagina ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Atualizado em:</dt>
                    <dd class="col-sm-9">{{ $sistema->updated_at->format('d/m/Y H:i:s') }}</dd>
                </dl>
            </div>
        </div>

        @if($sistema->max_atributos || $sistema->regras_opcionais)
            <!-- Card com atributos e regras opcionais -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title text-secondary fw-bold">Atributos e Regras Opcionais</h5>

                    @if($sistema->max_atributos)
                        <ul class="list-group list-group-flush mb-3">
                            @for ($i = 1; $i <= 6; $i++)
                                @php $attr_name = "atributo{$i}_nome"; @endphp
                                @if ($sistema->$attr_name)
                                    <li class="list-group-item">
                                        <strong>Atributo {{ $i }}:</strong> {{ $sistema->$attr_name }}
                                    </li>
                                @endif
                            @endfor
                        </ul>
                    @endif

                    @if($sistema->regras_opcionais)
                        <pre class="bg-light p-3 border rounded"><code>{{ json_encode($sistema->regras_opcionais, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                    @endif
                </div>
            </div>
        @endif
    @endif

    <!-- Botão voltar -->
    <div class="mt-3">
        <a href="{{ route('sistemas.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

</div>
@endsection
