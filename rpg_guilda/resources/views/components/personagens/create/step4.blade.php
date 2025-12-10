@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        {{-- COLUNA DA BARRA DE PROGRESSO --}}
        <div class="col-md-4">
            @include('personagens.create._progress_bar', ['data' => $data])
        </div>
        
        {{-- COLUNA DO FORMULÁRIO --}}
        <div class="col-md-8">
            <div class="card shadow mb-4 border-secondary">
                <div class="card-header bg-secondary text-white">
                    <h4 class="mb-0">5. Perícias & Inventário</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('personagens.storeStep5') }}">
                        @csrf

                        {{-- SELEÇÃO DE PERÍCIAS --}}
                        <div class="mb-5">
                            <h5 class="mb-3 text-secondary">
                                <i class="fas fa-fw fa-scroll me-2"></i> Seleção de Perícias de Classe
                            </h5>
                            @if ($classe && $classe->limite_pericias_selecionaveis > 0)
                                <p class="alert alert-info">
                                    Sua classe **({{ $classe->nome }})** permite selecionar exatamente 
                                    **{{ $classe->limite_pericias_selecionaveis }}** perícia(s) dentre as opções abaixo.
                                </p>

                                <div class="row" id="pericias-list">
                                    @php 
                                        // Filtra apenas as perícias disponíveis para a classe (EXIGE que a model Classe tenha o campo 'pericias_disponiveis')
                                        $periciasDisponiveisIds = json_decode($classe->pericias_disponiveis ?? '[]', true);
                                    @endphp

                                    @foreach ($periciasSistema as $pericia)
                                        @if (in_array($pericia->id, $periciasDisponiveisIds))
                                            @php
                                                $isChecked = in_array($pericia->id, $periciasSalvas);
                                            @endphp
                                            <div class="col-md-6 col-lg-4 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input pericia-checkbox" 
                                                           type="checkbox" 
                                                           value="{{ $pericia->id }}" 
                                                           id="pericia_{{ $pericia->id }}"
                                                           data-limite="{{ $classe->limite_pericias_selecionaveis }}"
                                                           {{ $isChecked ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="pericia_{{ $pericia->id }}">
                                                        {{ $pericia->nome }}
                                                        <small class="text-muted d-block">{{ $pericia->atributo_base }}</small>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                <input type="hidden" name="pericias_classe_selecionadas" id="pericias_classe_selecionadas_input" value="{{ old('pericias_classe_selecionadas', json_encode($periciasSalvas)) }}">
                                
                                @error('pericias_classe_selecionadas')
                                    <div class="alert alert-danger mt-3">{{ $message }}</div>
                                @enderror

                            @else
                                <p class="alert alert-success">
                                    Sua classe **({{ $classe->nome }})** não exige seleção de perícias neste momento.
                                </p>
                                <input type="hidden" name="pericias_classe_selecionadas" value="">
                            @endif
                        </div>
                        
                        <hr>
                        
                        {{-- INVENTÁRIO --}}
                        <div class="mb-4">
                            <h5 class="mb-3 text-secondary">
                                <i class="fas fa-fw fa-sack-dollar me-2"></i> Inventário
                            </h5>
                            <label for="inventario" class="form-label">Inventário (Opcional)</label>
                            <textarea class="form-control @error('inventario') is-invalid @enderror" 
                                      id="inventario" 
                                      name="inventario" 
                                      rows="4">{{ old('inventario', $data['inventario'] ?? '') }}</textarea>
                            @error('inventario')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="form-text text-muted">Liste aqui todos os seus itens, moedas, etc.</small>
                        </div>
                        
                        {{-- EQUIPAMENTO --}}
                        <div class="mb-4">
                            <label for="equipamento" class="form-label">Equipamento Vestido/Usado (Opcional)</label>
                            <textarea class="form-control @error('equipamento') is-invalid @enderror" 
                                      id="equipamento" 
                                      name="equipamento" 
                                      rows="3">{{ old('equipamento', $data['equipamento'] ?? '') }}</textarea>
                            @error('equipamento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="form-text text-muted">Armadura, armas equipadas e outros itens usados.</small>
                        </div>

                        {{-- Navegação --}}
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('personagens.step4') }}" class="btn btn-outline-secondary">
                                &laquo; Voltar (Passo 4)
                            </a>
                            <button type="submit" class="btn btn-secondary">Salvar e Finalizar (Passo 6) &raquo;</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.querySelectorAll('.pericia-checkbox');
        const hiddenInput = document.getElementById('pericias_classe_selecionadas_input');
        
        // Determina o limite de seleção baseado no primeiro checkbox (todos devem ter o mesmo)
        const limite = checkboxes.length > 0 ? parseInt(checkboxes[0].getAttribute('data-limite')) : 0;
        
        function updateHiddenInput() {
            const selected = [];
            checkboxes.forEach(cb => {
                if (cb.checked) {
                    selected.push(parseInt(cb.value));
                }
            });
            hiddenInput.value = JSON.stringify(selected);
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checkedCount = document.querySelectorAll('.pericia-checkbox:checked').length;
                
                if (limite > 0 && checkedCount > limite) {
                    this.checked = false; // Desmarca se exceder o limite
                    alert(`Você só pode selecionar **${limite}** perícia(s) de classe.`);
                }
                
                updateHiddenInput();
            });
        });
        
        // Inicializa o campo oculto
        updateHiddenInput();
    });
</script>
@endpush

@endsection