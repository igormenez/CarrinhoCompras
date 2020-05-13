@extends('layouts.app')

@section('content')

<div class="container text-center">
    <h1>Compras</h1>
</div>
    
    @if(session('mensage'))
    <div class="alert alert-success">
            {{ session('mensage') }}
        </div>
    @endif
<hr>
<div class="container" >
<div class="container text-center" >
   <h4> Compras Efetuadas</h4>
</div>

@forelse ($compras as $compra)
<h5>Pedido: {{$compra->id}}</h5>
<h5>Data: {{$compra->created_at->format('d/m/Y H:i')}}</h5>

<form action="{{ route('carrinho.cancelar') }}" method="POST">
@csrf
<input type="hidden" name="pedido_id" value="{{$compra->id}}">


    <table class="table">
        <thead>
            <tr>
                <th></th>
                <th>Produto</th>
                <th>Valor</th>
                <th>Desconto</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php
            $total_pedido = 0;   
            @endphp
            @foreach ($compra->pedido_produto_item as $pedido_produto)
                @php
                    $total_produto = $pedido_produto->valor - $pedido_produto->desconto;
                    $total_pedido += $total_produto;
                @endphp
                <tr>
                <td>
                    @if ($pedido_produto->status == 'PA')
                    <input type="checkbox" id="item-{{$pedido_produto->id}}" name="id[]" value="{{$pedido_produto->id}}">
                    <label for="item-{{$pedido_produto->id}}">selecionar</label>                 
                        
                    @else
                        <h6>Produto Cancelado</h6>
                    
                    @endif
                </td>
                <td>{{$pedido_produto->produto->nome}}</td>
                <td>R$: {{number_format($pedido_produto->valor,2,',','.')}}</td>
                <td>R$: {{number_format($pedido_produto->desconto,2,',','.')}}</td>
                <td>R$: {{number_format($total_produto,2,',','.')}}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>
                    <button type="submit" class="btn btn-danger">Cancelar</button>
                </td>
            </tr>
        </tfoot>
    </table>
</form>   
<div class="container text-center">
<h3>Total da compra:{{number_format($total_pedido,2,',','.')}}</h3>
</div>
@empty
    <div class="container text-center">
        <h3>Nenhum Pedido Concluido</h3>
    </div>



@endforelse
</div>
<hr>
<hr>
<hr>





<div class="container" >
    <div class="container text-center" >
       <h4> Compras Canceladas</h4>
    </div>
    
    @forelse ($cancelados as $compra)
    <h5>Pedido: {{$compra->id}}</h5>
    <h5>Data: {{$compra->created_at->format('d/m/Y H:i')}}</h5>
    
    <table class="table">
        <thead>
            <tr>
                <th>Quantidade</th>
                <th>Produto</th>
                <th>Valor</th>
                <th>Desconto</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php
             $total_pedido = 0;   
            @endphp
            @foreach ($compra->pedido_produto_item as $pedido_produto)
                @php
                    $total_produto = $pedido_produto->valor - $pedido_produto->desconto;
                    $total_pedido += $total_produto;
                @endphp
                <tr>
                <td>{{$pedido_produto->qtd}}</td>
                <td>{{$pedido_produto->produto->nome}}</td>
                <td>R$: {{number_format($pedido_produto->valor,2,',','.')}}</td>
                <td>R$: {{number_format($pedido_produto->desconto,2,',','.')}}</td>
                <td>R$: {{number_format($total_produto,2,',','.')}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="container text-center">
    <h3>Total da compra:{{number_format($total_pedido,2,',','.')}}</h3>
    </div>
    @empty
        <div class="container text-center">
            <h3>Nenhum Pedido Cancelado</h3>
        </div>
    
    
    
    @endforelse
    </div>
    
@endsection