
<table class="table table-striped table-hover align-middle shadow-sm rounded overflow-hidden">
    <thead class="table-primary">
        <tr>
            <th scope="col" class="text-center">#</th>
            <th scope="col">Nome</th>
            <th scope="col">Complexidade</th>
            <th scope="col">Foco</th>
            <th scope="col">Mecânica</th>
            <th scope="col" class="text-center">Ações</th>
        </tr>
    </thead>

    <tbody>
        @forelse($sistemas as $sistema)
            <tr>
                <td class="fw-bold text-primary text-center">{{ $sistema->id }}</td>
                <td class="fw-semibold">{{ $sistema->nome }}</td>
                <td>{{ $sistema->complexidade ?? '—' }}</td>
                <td>{{ $sistema->foco ?? '—' }}</td>
                <td>{{ $sistema->mecanica_principal ?? '—' }}</td>

                <td class="text-center">
                    {{-- 👁️ Ver --}}
                    <a href="{{ route('sistemas.show', $sistema) }}"
                       class="btn btn-sm btn-outline-primary rounded-pill fw-semibold"
                       title="Ver Detalhes" aria-label="Ver detalhes do sistema {{ $sistema->nome }}">
                        👁️ Ver
                    </a>

                    {{-- ✏️ Editar --}}
                    <a href="{{ route('sistemas.edit', $sistema) }}"
                       class="btn btn-sm btn-outline-warning rounded-pill fw-semibold"
                       title="Editar Sistema"
                       aria-label="Editar sistema {{ $sistema->nome }}">
                        ✏️ Editar
                    </a>

                    {{-- 🗑️ Excluir --}}
                    <form action="{{ route('sistemas.destroy', $sistema) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Tem certeza que deseja excluir o sistema \"{{ $sistema->nome }}\"?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill fw-semibold"
                                title="Excluir Sistema"
                                aria-label="Excluir sistema {{ $sistema->nome }}">
                            🗑️ Excluir
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-3">
                    <i class="bi bi-emoji-frown me-1"></i> Nenhum sistema cadastrado.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
