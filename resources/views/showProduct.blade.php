@extends('layouts.app')

@section('content')

@if(@isset($produto))
<div class="container" >
<table class="table" >
    <thead>
        <tr>
            <th>Nome</th>
            <th>Descrição</th>
            <th>valor</th>
        </tr>        
    </thead>

    <tbody>
        <tr>
            <td>{{$produto->nome}}</td>
            <td>{{$produto->descricao}}</td>
            <td>{{$produto->valor }}</td>
        </tr>
    </tbody>
</table>
</div>
<div class="container text-center" >
    <form action="{{route('carrinho.adicionar')}}" method="POST" >
        @csrf
    <input type="hidden" name="id" value="{{$produto->id}}">
    <button type="submit" class="btn btn-primary">Adicionar ao carrinho</button>
    </form>
    </div>
@endif

@endsection