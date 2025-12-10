@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">

        {{-- COLUNA DO DASHBOARD --}}
        <div class="col-md-4 order-md-2">
            <div class="card shadow mb-4 border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">✨ Dashboard: Pré-visualização</h5>
                </div>
                <div class="card-body p-2">
                    @include('personagens.create._progress_bar', ['data' => $data])
                    <hr class="my-3">
                    @include('personagens.create._dashboard_preview', ['data' => $data, 'atributosPadrao' => $atributosSistema])
                    <p class="mt-3 text-muted small text-center">
                        Os dados são atualizados conforme você digita.
                    </p>
                </div>
            </div>
        </div>

        {{-- COLUNA PRINCIPAL DO FORMULÁRIO --}}
        <div class="col-md-8 order-md-1">
            <div class="card shadow mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">3. Atributos (Pontuações)</h4>
                </div>
                <div class="card-body">
                    <p>
                        Defina ou sorteie os valores para os atributos do seu personagem. Valores padrão para este sistema:
                        @foreach ($atributosSistema as $attr)
                            <span class="badge bg-secondary">{{ strtoupper(substr($attr, 0, 3)) }}</span>
                        @endforeach
                    </p>

                    <form method="POST" action="{{ route('personagens.storeStep3') }}" id="atributos-form">
                        @csrf

                        @php
                            $atributosAtuais = $atributosSalvos ?? [];
                        @endphp

                        <div id="atributos-list" class="row">
                            @foreach ($atributosSistema as $atributo)
                                <div class="col-md-4 mb-3">
                                    <label for="attr-{{ $atributo }}" class="form-label">{{ ucfirst($atributo) }}</label>
                                    <input type="number" 
                                           class="form-control atributo-input @error('atributos.' . $atributo) is-invalid @enderror" 
                                           id="attr-{{ $atributo }}" 
                                           name="atributos[{{ $atributo }}]" 
                                           value="{{ old('atributos.' . $atributo, $atributosAtuais[$atributo] ?? 10) }}" 
                                           min="1" max="20" required 
                                           data-atributo="{{ $atributo }}">
                                    @error('atributos.' . $atributo)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('personagens.step2') }}" class="btn btn-outline-secondary">
                                &laquo; Voltar (Passo 2)
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Salvar e Próximo (Passo 4) &raquo;
                            </button>
                        </div>

                        <div class="mt-3 text-center">
                            <button type="button" id="btn-sortear" class="btn btn-sm btn-info">
                                🎲 Sortear Atributos (4d6)
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    // Atualiza dashboard em tempo real
    function updateDashboard(atributo, valor) {
        const displayElement = document.getElementById('dashboard-attr-' + atributo);
        if (displayElement) {
            displayElement.textContent = valor;
            const mod = Math.floor((valor - 10) / 2);
            const modDisplay = document.getElementById('dashboard-mod-' + atributo);
            if (modDisplay) {
                modDisplay.textContent = (mod >= 0 ? '+' : '') + mod;
            }
        }
    }

    // Input manual
    document.querySelectorAll('.atributo-input').forEach(input => {
        input.addEventListener('input', function() {
            const atributo = this.dataset.atributo;
            const valor = parseInt(this.value) || 0;
            updateDashboard(atributo, valor);
        });
    });

    // Sortear atributos via AJAX
    document.getElementById('btn-sortear').addEventListener('click', function() {
        const btn = this;
        btn.disabled = true;

        fetch('{{ route('personagens.sortearAtributos') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ sistema_id: {{ $data['sistema_id'] }} })
        })
        .then(res => res.json())
        .then(data => {
            if (data.atributos) {
                @foreach ($atributosSistema as $atributo)
                    const inputEl = document.getElementById('attr-{{ $atributo }}');
                    const valorSorteado = data.atributos['{{ $atributo }}'];
                    if (inputEl) {
                        inputEl.value = valorSorteado;
                        updateDashboard('{{ $atributo }}', valorSorteado);
                    }
                @endforeach
            } else {
                alert('Erro ao sortear atributos.');
            }
            btn.disabled = false;
        })
        .catch(err => {
            console.error(err);
            alert('Erro na comunicação com o servidor.');
            btn.disabled = false;
        });
    });
</script>
@endpush
@endsection
