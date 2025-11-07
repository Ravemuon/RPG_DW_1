<div class="mb-5">
    <h4 class="fw-bold text-warning mb-3">➕ Adicionar amigos à campanha</h4>
    <form action="{{ route('campanhas.usuarios.adicionar', $campanha->id) }}" method="POST" class="row g-3">
        @csrf
        <div class="col-md-6">
            <select name="user_id" class="form-select" required>
                <option value="">Selecione um amigo</option>
                @foreach($amigos as $amigo)
                    @if(!$campanha->jogadores->contains($amigo->id))
                        <option value="{{ $amigo->id }}">{{ $amigo->nome }}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-outline-success w-100">Enviar convite</button>
        </div>
    </form>
</div>
