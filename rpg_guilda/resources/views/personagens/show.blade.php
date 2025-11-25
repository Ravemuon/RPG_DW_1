@extends('layouts.app')

@section('title', $personagem->nome ?? 'Ficha de Personagem')

@section('content')
<div class="container my-5">
    <div class="card shadow">
        <div class="card-body">
            <div class="d-flex align-items-start gap-4">
                <div style="width:220px;">
                    @if($personagem->imagem)
                        <img src="{{ asset('storage/' . $personagem->imagem) }}" alt="Imagem de {{ $personagem->nome }}" class="img-fluid rounded">
                    @else
                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded" style="height:220px;">
                            <strong class="fs-5">Sem imagem</strong>
                        </div>
                    @endif
                </div>

                <div class="flex-grow-1">
                    <h2 class="mb-1">{{ $personagem->nome }}</h2>
                    <div class="small text-muted mb-3">
                        @if($personagem->classe) <span>{{ $personagem->classe->nome }}</span> @endif
                        @if($personagem->raca) • <span>{{ $personagem->raca->nome }}</span> @endif
                        @if($personagem->origem) • <span>{{ $personagem->origem->nome }}</span> @endif
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-2">Atributos</h6>
                            @php $atributos = json_decode($personagem->atributos ?? '{}', true) ?? []; @endphp
                            <div class="d-flex flex-column gap-2 mb-3">
                                @foreach($atributos['final'] ?? ($atributos ?? []) as $k => $v)
                                    <div class="d-flex justify-content-between border p-2 rounded">
                                        <div class="fw-bold">{{ strtoupper(substr($k,0,3)) }}</div>
                                        <div>{{ $v }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="mb-2">Perícias & Equip.</h6>
                            @php
                                $pericias = $personagem->pericias ?? [];
                                $equip = json_decode($personagem->inventario ?? '[]', true);
                                $selectedEquipment = $personagem->selected_equipment ?? null;
                                if(!$selectedEquipment) $selectedEquipment = [];
                                else $selectedEquipment = (is_string($selectedEquipment) ? json_decode($selectedEquipment,true) : $selectedEquipment);
                            @endphp

                            <div class="mb-2">
                                <strong>Perícias:</strong>
                                @if($pericias && count($pericias))
                                    <ul class="small mb-0">
                                        @foreach($pericias as $p)
                                            <li>{{ $p->nome ?? $p }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="small text-muted">Nenhuma perícia registrada.</div>
                                @endif
                            </div>

                            <div class="mt-3">
                                <strong>Equipamentos selecionados:</strong>
                                @if(is_array($selectedEquipment) && count($selectedEquipment))
                                    <ul class="small mb-0">
                                        @foreach($selectedEquipment as $k => $it)
                                            <li>{{ $it }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="small text-muted">Nenhum equipamento selecionado (ou não registrado).</div>
                                @endif
                            </div>

                        </div>

                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-4">
                            <h6>Descrição</h6>
                            <p class="small">{{ $personagem->descricao ?? '—' }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6>Personalidade</h6>
                            <p class="small">{{ $personagem->personalidade ?? '—' }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6>História</h6>
                            <p class="small">{{ $personagem->historia ?? '—' }}</p>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <h6>Inventário</h6>
                        <p class="small">{{ $personagem->inventario ?? '—' }}</p>
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <a href="{{ route('personagens.edit', $personagem) }}" class="btn btn-sm btn-primary">Editar</a>
                        <a href="{{ route('personagens.index') }}" class="btn btn-sm btn-outline-secondary">Voltar</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
