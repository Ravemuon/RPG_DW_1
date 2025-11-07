@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Sistema: {{ $sistema->nome }}</h1>

    @include('sistemas._form', ['sistema' => $sistema, 'action' => route('sistemas.update', $sistema), 'method' => 'PUT'])

</div>
@endsection
