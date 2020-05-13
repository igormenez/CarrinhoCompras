@extends('layouts.app')

@section('content')

<div class="container text-center">
<h1>Carrinho</h1>
</div>

@if(session('mensage'))
<div class="alert alert-success">
        {{ session('mensage') }}
    </div>
@endif

@forelse ($pedidos as $pedido)
<div class="container" >
    <div class="container text-center" >
    <h1>Numero do Pedido: {{$pedido->id}}</h1>
    
    </div>
    <hr>
    <table class="table">
        <thead>
            <tr>
                <th>Qtd</th>
                <th>Produto</th>
                <th>Valor Unid</th>
                <th>Desconto</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
            @php
            $total_pedido = 0;
            @endphp    
        
            @foreach ($pedido->pedido_produtos as $pedido_produto)
                
            
            <tr>
                <td>
                    <a href="#" onclick="carrinhoRemoverProduto({{$pedido->id}},{{$pedido_produto->produto_id}},1)"><ion-icon name="remove-circle-outline"></ion-icon></a>
                    {{$pedido_produto->qtd}}
                    <a href="#" onclick="carrinhoAdicionarProduto({{$pedido_produto->produto_id}})"><ion-icon name="add-circle-outline"></ion-icon></a>                    
                    <a href="#" onclick="carrinhoRemoverProduto({{$pedido->id}},{{$pedido_produto->produto_id}},0)">Remover Produto</a>

                </td>
                <td>{{$pedido_produto->produto->nome}}</td>
                <td>R$: {{number_format($pedido_produto->produto->valor,2,',','.')}}</td>
                <td>R$: {{number_format($pedido_produto->descontos,2,',','.')}}</td>
                @php
                $total_produto = $pedido_produto->valores - $pedido_produto->descontos;
                $total_pedido += $total_produto;
                @endphp  
                <td>R$: {{number_format($total_produto,2,',','.')}}</td>

            </tr>
        </tbody>
        @endforeach
    </table>
</div>
<div class="container text-center" >
<h1>Valor total da Compra: R$: {{number_format($total_pedido,2,',','.')}}</h1>
<br>
<form action="{{route('carrinho.desconto')}}" method="POST">
@csrf
<input type="hidden" name="pedido_id" value="{{$pedido->id}}">
<label for="cupom">digite o cupom:</label>
<input type="text" id="cupom" name="cupom">
<button type="submit" class="btn btn-primary" >Aplicar cupom</button>
</form>
<form action="{{route('carrinho.concluir')}}" method="POST">
@csrf
<input type="hidden" name="pedido_id" value="{{$pedido->id}}">
<button type="submit" class="btn btn-primary" >Concluir compra</button>
</form>
</div>


    
@empty
<hr>
<h1>Carrinho Vazio</h1>    
@endforelse
<hr>
<div class="container" >
<a href="{{ route('home') }}" class="btn btn-primary">Continuar comprando</a>

</div>

<form action="{{route('carrinho.remover')}}" method="POST" id="form-remover-produto">
@csrf
@method('DELETE')
<input type="hidden" name="pedido_id">
<input type="hidden" name="produto_id">
<input type="hidden" name="item">
</form>
<form action="{{ route('carrinho.adicionar') }}" method="POST" id="form-adicionar-produto">
@csrf
<input type="hidden" name="id">
</form>
@push('scripts')
    <script type="text/javascript" src="{{ asset('js/carrinho.js') }}" ></script>
@endpush

@endsection