<!-- O partial de formulário recebe o objeto $classe se estiver em modo de edição. Se for criação, $classe será nulo. -->

<!-- Variável auxiliar para preenchimento. Usa $classe->nome se existir, senão usa string vazia. -->

@php
$nome = old('nome', $classe->nome ?? '');
$descricao = old('descricao', $classe->descricao ?? '');
@endphp

<div class="mb-3">
<label for="nome" class="form-label">Nome da Classe <span class="text-danger">*</span></label>
<input
type="text"
class="form-control @error('nome') is-invalid @enderror"
id="nome"
name="nome"
value="{{ $nome }}"
required
placeholder="Ex: Guerreiro, Mago, Bardo"
>
@error('nome')
<div class="invalid-feedback">
{{ $message }}
</div>
@enderror
</div>

<div class="mb-3">
<label for="descricao" class="form-label">Descrição da Classe</label>
<textarea
class="form-control @error('descricao') is-invalid @enderror"
id="descricao"
name="descricao"
rows="5"
placeholder="Descreva brevemente a função e características desta classe."
>{{ $descricao }}</textarea>
@error('descricao')
<div class="invalid-feedback">
{{ $message }}
</div>
@enderror
</div>
