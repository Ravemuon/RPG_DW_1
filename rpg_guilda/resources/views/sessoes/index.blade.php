@extends('layouts.app')

@section('title', "Sessões — {$campanha->nome}")

@section('content')

@php
    use Illuminate\Support\Str;

    $search       = $search ?? '';
    $dateSearch   = $dateSearch ?? '';
    $statusFilter = $statusFilter ?? request('status', 'todas');

    $dashboard = $dashboardData ?? ['total' => 0,'concluidas'=>0,'agendadas'=>0];

    function get_status_badge_fa($status){
        return [
            'agendada'     => ['cor'=>'info','icone'=>'calendar-check','texto'=>'Agendada'],
            'em_andamento' => ['cor'=>'warning','icone'=>'hourglass-half','texto'=>'Em andamento'],
            'concluida'    => ['cor'=>'success','icone'=>'circle-check','texto'=>'Concluída'],
            'cancelada'    => ['cor'=>'danger','icone'=>'ban','texto'=>'Cancelada'],
        ][$status] ?? ['cor'=>'secondary','icone'=>'question','texto'=>'Desconhecido'];
    }
@endphp


<div class="container-fluid py-4 px-lg-5">

    {{-- HEADER --}}
    <div class="header-section mb-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('campanhas.index') }}" class="text-muted">Campanhas</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('campanhas.show',$campanha->id) }}" class="text-muted">{{ Str::limit($campanha->nome, 20) }}</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Sessões</li>
                    </ol>
                </nav>
                
                <div class="d-flex align-items-center gap-3">
                    <div class="header-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div>
                        <h1 class="fw-bold mb-1">Sessões</h1>
                        <p class="text-muted mb-0">{{ $campanha->nome }}</p>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('campanhas.show',$campanha->id) }}" class="btn btn-outline-secondary px-4">
                    <i class="fas fa-arrow-left me-2"></i> Voltar
                </a>
                @can('update',$campanha)
                <a href="{{ route('sessoes.create',$campanha->id) }}" class="btn btn-primary px-4 shadow-sm">
                    <i class="fas fa-plus-circle me-2"></i> Nova Sessão
                </a>
                @endcan
            </div>
        </div>
    </div>


    {{-- DASHBOARD --}}
    @if($dashboard['total']>0)
    <section class="mb-5">
        <h2 class="h5 text-muted fw-semibold mb-4">
            <i class="fas fa-chart-line me-2"></i>Resumo Geral
        </h2>

        <div class="row g-3">
            @foreach([
                ['Total',$dashboard['total'],'list-ul','#4361ee'],
                ['Concluídas',$dashboard['concluidas'],'circle-check','#4cc9f0'],
                ['Agendadas',$dashboard['agendadas'],'calendar-check','#7209b7'],
            ] as [$label,$value,$icon,$color])
            <div class="col-md-4">
                <div class="stats-card card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="stats-icon" style="background: {{ $color }}20; color: {{ $color }};">
                                <i class="fas fa-{{ $icon }}"></i>
                            </div>
                            <span class="badge bg-light text-dark">{{ $label }}</span>
                        </div>
                        <h2 class="fw-bold display-5" style="color: {{ $color }};">{{ $value }}</h2>
                        <p class="text-muted small mb-0">sessões {{ strtolower($label) }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif


    {{-- FILTROS --}}
    <section class="card border-0 shadow-sm mb-5">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3 mb-4">
                <div>
                    <h4 class="fw-bold mb-1"><i class="fas fa-search me-2 text-primary"></i>Filtrar Sessões</h4>
                    <p class="text-muted small mb-0">Encontre sessões específicas da campanha</p>
                </div>
                
                <div class="d-flex gap-2">
                    @if($search||$dateSearch||$statusFilter!='todas')
                    <a href="{{ route('sessoes.index',$campanha->id) }}" class="btn btn-outline-danger">
                        <i class="fas fa-times me-1"></i> Limpar
                    </a>
                    @endif
                </div>
            </div>

            <form method="GET" class="row g-3">
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <label class="form-label small text-muted">Buscar por título</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Ex: 'Primeira sessão'" 
                               class="form-control border-start-0">
                    </div>
                </div>

                <div class="col-xl-2 col-lg-3 col-md-6">
                    <label class="form-label small text-muted">Data específica</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-calendar text-muted"></i>
                        </span>
                        <input type="text" name="date_search" value="{{ $dateSearch }}" placeholder="2024-12-01" 
                               class="form-control border-start-0">
                    </div>
                </div>

                <div class="col-xl-2 col-lg-3 col-md-6">
                    <label class="form-label small text-muted">Status</label>
                    <select name="status" class="form-select">
                        @foreach(['todas'=>'Todas','agendada'=>'Agendada','em_andamento'=>'Em andamento','concluida'=>'Concluída','cancelada'=>'Cancelada'] as $key=>$txt)
                            <option value="{{ $key }}" @selected($statusFilter==$key)>{{ $txt }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xl-2 col-lg-2 col-md-6 d-flex align-items-end">
                    <button class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>
    </section>


    {{-- LISTAGEM --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Todas as Sessões</h3>
            <p class="text-muted mb-0">{{ $sessoes->total() }} sessões encontradas</p>
        </div>
        
        @if($sessoes->count())
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-sort me-2"></i>Ordenar
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#"><i class="fas fa-calendar me-2"></i>Data (mais recente)</a></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-calendar-alt me-2"></i>Data (mais antiga)</a></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-font me-2"></i>Título (A-Z)</a></li>
            </ul>
        </div>
        @endif
    </div>

    @if($sessoes->count())
    <div class="row g-4">
        @foreach($sessoes as $sessao)
        @php 
            $i = get_status_badge_fa($sessao->status); 
            $data = \Carbon\Carbon::parse($sessao->data_hora);
            $statusColors = [
                'agendada' => 'border-start border-info',
                'em_andamento' => 'border-start border-warning',
                'concluida' => 'border-start border-success',
                'cancelada' => 'border-start border-danger'
            ];
            $borderClass = $statusColors[$sessao->status] ?? 'border-start border-secondary';
        @endphp

        <div class="col-lg-4 col-md-6">
            <div class="card h-100 shadow-sm hover-shadow {{ $borderClass }}">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="badge bg-light text-dark mb-2">
                                <i class="fas fa-clock me-1"></i>{{ $data->format('d/m/Y') }}
                            </span>
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-clock me-1"></i>{{ $data->format('H:i') }}
                            </span>
                        </div>
                        <span class="badge bg-{{ $i['cor'] }} px-3 py-2">
                            <i class="fas fa-{{ $i['icone'] }} me-1"></i> {{ $i['texto'] }}
                        </span>
                    </div>

                    <h4 class="card-title fw-bold mb-3">{{ $sessao->titulo }}</h4>
                    
                    <p class="card-text text-muted mb-4">
                        {{ Str::limit($sessao->resumo ?? 'Sem descrição disponível', 120) }}
                    </p>

                    <div class="d-flex flex-wrap gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('sessoes.show',[$campanha->id,$sessao->id]) }}" 
                           class="btn btn-outline-primary btn-sm flex-fill">
                            <i class="fas fa-eye me-1"></i> Ver
                        </a>
                        
                        @can('update',$campanha)
                        <a href="{{ route('sessoes.edit',[$campanha->id,$sessao->id]) }}" 
                           class="btn btn-outline-warning btn-sm flex-fill">
                            <i class="fas fa-edit me-1"></i> Editar
                        </a>
                        @endcan
                        
                        @if($sessao->status=='concluida')
                        <a href="{{ route('sessoes.exportarPdf',[$campanha->id,$sessao->id]) }}" 
                           class="btn btn-outline-info btn-sm flex-fill">
                            <i class="fas fa-file-pdf me-1"></i> PDF
                        </a>
                        @endif
                        
                        @can('delete',$campanha)
                        <form method="POST" action="{{ route('sessoes.destroy',[$campanha->id,$sessao->id]) }}" class="flex-fill">
                            @csrf @method('DELETE')
                            <form action="{{ route('sessoes.destroy', ['campanha'=>$sessao->campanha->id,'sessao'=>$sessao->id]) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir a sessão {{ $sessao->titulo }}? Esta ação é irreversível.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-lg rounded-pill fw-bold">🗑️ Excluir Sessão</button>
                            </form>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-5">
        {{ $sessoes->appends(['search'=>$search,'date_search'=>$dateSearch,'status'=>$statusFilter])->links('pagination::bootstrap-5') }}
    </div>

    @else
    <div class="text-center py-5">
        <div class="empty-state">
            <div class="empty-state-icon mb-4">
                <i class="fas fa-calendar-times fa-3x text-muted"></i>
            </div>
            <h4 class="fw-bold mb-3">Nenhuma sessão encontrada</h4>
            <p class="text-muted mb-4">Não encontramos sessões correspondentes aos seus filtros.</p>
            <div class="d-flex justify-content-center gap-3">
                @if($search||$dateSearch||$statusFilter!='todas')
                <a href="{{ route('sessoes.index',$campanha->id) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Limpar filtros
                </a>
                @endif
                @can('update',$campanha)
                <a href="{{ route('sessoes.create',$campanha->id) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Criar Primeira Sessão
                </a>
                @endcan
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    :root {
        --primary-color: #4361ee;
        --secondary-color: #7209b7;
        --accent-color: #4cc9f0;
    }

    .header-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 2rem;
        border-radius: 1rem;
        margin-top: -1rem;
    }

    .header-icon {
        width: 60px;
        height: 60px;
        background: var(--primary-color);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }

    .stats-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }

    .stats-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .card {
        transition: all 0.3s ease;
        border-radius: 12px;
        border: 1px solid #e9ecef;
    }

    .card:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }

    .hover-shadow:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }

    .border-start {
        border-left-width: 4px !important;
    }

    .empty-state {
        max-width: 400px;
        margin: 0 auto;
    }

    .empty-state-icon {
        opacity: 0.5;
    }

    @media (max-width: 768px) {
        .header-section {
            padding: 1.5rem;
        }
        
        .stats-card .card-body {
            padding: 1.5rem !important;
        }
        
        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
    }
</style>

<script>
function confirmDelete(title, form) {
    Swal.fire({
        title: 'Tem certeza?',
        html: `A sessão <strong>"${title}"</strong> será excluída permanentemente.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}
</script>

@endsection