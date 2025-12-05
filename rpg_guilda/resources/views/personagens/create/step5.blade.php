@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        {{-- COLUNA DA BARRA DE PROGRESSO --}}
        <div class="col-md-4">
            {{-- Assume que a variável $data é passada pelo Controller --}}
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
                            
                            {{-- Verifica se a classe exige seleção de perícias --}}
                            @if ($classe && $classe->limite_pericias_selecionaveis > 0)
                                <p class="alert alert-info">
                                    Sua classe **({{ $classe->nome }})** permite selecionar exatamente 
                                    **{{ $classe->limite_pericias_selecionaveis }}** perícia(s) dentre as opções listadas abaixo.
                                </p>

                                <div class="row" id="pericias-list">
                                    @php 
                                        // EXIGE: que a model Classe tenha o campo 'pericias_disponiveis' (JSON array de IDs)
                                        $periciasDisponiveisIds = json_decode($classe->pericias_disponiveis ?? '[]', true);
                                        $periciasSalvas = json_decode(old('pericias_classe_selecionadas', $data['pericias_classe_selecionadas'] ?? '[]'), true);
                                    @endphp

                                    {{-- Itera sobre todas as perícias do sistema e filtra as disponíveis para a classe --}}
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
                                                        **{{ $pericia->nome }}**
                                                        <small class="text-muted d-block">({{ $pericia->atributo_base }})</small>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                
                                {{-- Campo oculto para armazenar o JSON de perícias selecionadas para a requisição --}}
                                <input type="hidden" 
                                       name="pericias_classe_selecionadas" 
                                       id="pericias_classe_selecionadas_input" 
                                       value="{{ old('pericias_classe_selecionadas', $data['pericias_classe_selecionadas'] ?? '[]') }}">
                                
                                @error('pericias_classe_selecionadas')
                                    <div class="alert alert-danger mt-3">{{ $message }}</div>
                                @enderror

                            @else
                                <p class="alert alert-secondary">
                                    Sua classe **({{ $classe->nome }})** não exige seleção de perícias de classe.
                                </p>
                                <input type="hidden" name="pericias_classe_selecionadas" value="[]">
                            @endif
                        </div>
                        
                        <hr>
                        
                        {{-- INVENTÁRIO & EQUIPAMENTO --}}
                        <h5 class="mb-3 text-secondary">
                            <i class="fas fa-fw fa-money-bill-transfer me-2"></i> Inventário & Equipamento
                        </h5>
                        
                        {{-- INVENTÁRIO --}}
                        <div class="mb-4">
                            <label for="inventario" class="form-label">Inventário e Itens</label>
                            <textarea class="form-control @error('inventario') is-invalid @enderror" 
                                      id="inventario" 
                                      name="inventario" 
                                      rows="4">{{ old('inventario', $data['inventario'] ?? '') }}</textarea>
                            @error('inventario')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="form-text text-muted">Liste aqui todos os seus itens, moedas e suprimentos.</small>
                        </div>
                        
                        {{-- EQUIPAMENTO --}}
                        <div class="mb-4">
                            <label for="equipamento" class="form-label">Equipamento Vestido/Usado</label>
                            <textarea class="form-control @error('equipamento') is-invalid @enderror" 
                                      id="equipamento" 
                                      name="equipamento" 
                                      rows="3">{{ old('equipamento', $data['equipamento'] ?? '') }}</textarea>
                            @error('equipamento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="form-text text-muted">Armadura, armas equipadas e itens de uso constante.</small>
                        </div>

                        {{-- Navegação --}}
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('personagens.step4', ['campanha' => $data['campanha_id']]) }}" class="btn btn-outline-secondary">
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
        
        if (checkboxes.length === 0) {
            return; // Sai se não houver perícias para selecionar
        }
        
        // Determina o limite de seleção baseado no primeiro checkbox (todos devem ter o mesmo)
        const limite = parseInt(checkboxes[0].getAttribute('data-limite')) || 0;
        
        function updateHiddenInput() {
            const selected = [];
            checkboxes.forEach(cb => {
                if (cb.checked) {
                    // Armazena o valor (ID da perícia) como número
                    selected.push(parseInt(cb.value)); 
                }
            });
            // Converte o array para string JSON e atualiza o campo oculto
            hiddenInput.value = JSON.stringify(selected);
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checkedCount = document.querySelectorAll('.pericia-checkbox:checked').length;
                
                // Aplica a restrição de limite
                if (limite > 0 && checkedCount > limite) {
                    this.checked = false; // Desmarca se exceder o limite
                    alert(`Você só pode selecionar **${limite}** perícia(s) de classe.`);
                }
                
                updateHiddenInput();
            });
        });
        
        // Inicializa o campo oculto com base nas seleções atuais (útil para old() ou dados salvos)
        updateHiddenInput();
    });
</script>
@endpush

@endsection