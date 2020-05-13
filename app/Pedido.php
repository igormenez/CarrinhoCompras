<?php

namespace App;

use Illuminate\Support\Facades\DB;


use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = ['user_id','status'];
    
    public function pedido_produtos()
    {
        return $this->hasMany('App\PedidoProduto')
        ->select( DB::raw('produto_id, SUM(desconto) as descontos, SUM(valor) as valores, 
        count(1) as qtd'))
        ->groupBy('produto_id')->orderBy('produto_id','desc');
    }

    public function pedido_produto_item()
    {
        return $this->hasMany('App\PedidoProduto');
    }

    public function buscarPedido($values)
    {
        $pedido = self::where($values)->first(['id']);
        if(isset($pedido->id)){
            return $pedido->id;
        }else{
            return null;
        }
        
    }
}
