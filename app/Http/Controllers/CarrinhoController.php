<?php

namespace App\Http\Controllers;

use App\Pedido;
use App\PedidoProduto;
use App\Produto;
use App\CupomDesconto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarrinhoController extends Controller
{
    protected $repositoryPedido;
    protected $repositoryProduto;
    protected $repositoryPedidoProduto;
    protected $repositoryCupom;

    public function __construct(Request $request, Pedido $pedido, Produto $produto, PedidoProduto $pedidoProduto, CupomDesconto $cupom)
    {
        $this->request = $request;
        $this->repositoryPedido = $pedido;
        $this->repositoryProduto = $produto;
        $this->repositoryPedidoProduto = $pedidoProduto;
        $this->repositoryCupom = $cupom;
        $this->middleware('auth');
    }

    public function index()
    {
        $pedidos = Pedido::where([
            'status' => 'RE',
            'user_id' => Auth::id()
        ])->get();
        
        
        return view('Carrinho.index',[
            'pedidos' => $pedidos,        
         ]);
    }

    public function adicionar(Request $request)
    {
        $idProduto = $request->only('id');
        
        

        $produto = $this->repositoryProduto->where('id',$idProduto)->first();
        if(empty($produto->id)){
            $mensage = 'Produto não encontrado';
            $produtos = $this->repositoryProduto->paginate();
            return view('home',[
                'produtos' => $produtos,
                'mensage' => $mensage
            ]);
        }

        $idUsuario = Auth::id();
        
        

        $idPedido = $this->repositoryPedido->buscarPedido([
            'user_id' => $idUsuario,
            'status' => 'RE'
        ]);
        
        
        if(!$idPedido)
        {
            $Pedido_novo = Pedido::create([
                'user_id' => $idUsuario,
                'status' => 'RE'
            ]);
            $idPedido = $Pedido_novo->id;
        }
        
        

        $this->repositoryPedidoProduto->create([
            'pedido_id' => $idPedido,
            'produto_id' => $produto->id,            
            'status' => 'RE',
            'valor' => $produto->valor
        ]);

        
        return redirect()->route('carrinho.index')->with('mensage','Produto adicionado com sucesso');
    }

    public function remover(Request $request)
    {
        $idPedido = $request->input('pedido_id');
        $idProduto = $request->input('produto_id');
        $quantidade_remover = $request->boolean('item');
        $idUsuario = Auth::id();

        $idPedido = $this->repositoryPedido->buscarPedido([
            'id' => $idPedido,
            'user_id' => $idUsuario,
            'status' => 'RE'
        ]); 
        if(empty($idPedido))
        {
           return redirect()->route('carrinho.index')->with('mensage','Pedido não encontrado');
        }

        $where_produto = [
            'pedido_id' => $idPedido,
            'produto_id' => $idProduto
        ];

        $produtoPedido = $this->repositoryPedidoProduto->where($where_produto)->orderBy('id','desc')->first();
        if(empty($produtoPedido->id))
        {
            return redirect()->route('carrinho.index')->with('mensage', 'Produto não encontrado');            
        }

        if($quantidade_remover)
        {
            $where_produto['id'] = $produtoPedido->id;
        }
        $this->repositoryPedidoProduto->where($where_produto)->delete();

        $checkPedido = $this->repositoryPedidoProduto->where([
            'pedido_id' => $produtoPedido->pedido_id
        ])->exists();
        if(!$checkPedido)
        {
            $this->repositoryPedido->where([
                'id' => $produtoPedido->pedido_id
            ])->delete();
        }

        
        return redirect()->route('carrinho.index')->with('mensage','Produto removido com sucesso');
        
    }


    public function concluir(Request $request)
    {
        $idPedido = $request->input('pedido_id');
        $idUsuario = Auth::id();

        $check_pedido = $this->repositoryPedido->where([
            'id' => $idPedido,
            'user_id' => $idUsuario,
            'status' => 'RE'
        ]);

        if(!$check_pedido)
        {
            return redirect()->route('carrinho.index')->with('mensage','Pedido não encontrado');
        }

        $check_produto = $this->repositoryPedidoProduto->where([
            'pedido_id' => $idPedido
        ])->exists();
        if(!$check_produto)
        {
            return redirect()->route('carrinho.index')->with('mensage','Produto não encontrado');
        }

        $this->repositoryPedidoProduto->where([
            'pedido_id' => $idPedido
        ])->update([
            'status' => 'PA'
        ]);

        $this->repositoryPedido->where([
            'id' => $idPedido
        ])->update([
            'status' => 'PA'
        ]);
        return redirect()->route('carrinho.compras')->with('mensage','Compra efetuada com sucesso');
    }

    public function compras(){
    
        $compras = $this->repositoryPedido->where([
            'status' =>'PA',
            'user_id' => Auth::id()
        ])->orderBy('created_at','desc')->get();

        $cancelados = $this->repositoryPedido->where([
            'status' => 'CA',
            'user_id' => Auth::id()
        ])->orderBy('created_at','desc')->get();

        return view('carrinho.compras',[
            'compras' => $compras,
            'cancelados' => $cancelados
        ]);
    }

    public function cancelar(Request $request)
    {
        $idPedido = $request->input('pedido_id');
        $idPedido_Produto = $request->input('id');
        $idUsuario = Auth::id();

        if(empty($idPedido_Produto))
        {
            return redirect()->route('carrinho.compras')->with('mensage','Nenhum item selecionado');
        }
        
        $check_pedido = $this->repositoryPedido->where([
            'id' => $idPedido,
            'user_id' => $idUsuario,
            'status' => 'PA'
        ])->exists();
        if(!$check_pedido){
            return redirect()->route('carrinho.compras')->with('mensage','Pedido não encontrado');
        }

        $check_Produtos = $this->repositoryPedidoProduto->where([
            'pedido_id' => $idPedido,
            'status' => 'PA'
        ])->whereIn('id',$idPedido_Produto)->exists();
        if(!$check_Produtos)
        {
            return redirect()->route('carrinho.compras')->with('mensage','Produto não encontrado');
        }

        $this->repositoryPedidoProduto->where([
            'pedido_id' => $idPedido,
            'status' => 'PA'
        ])->whereIn('id',$idPedido_Produto)->update([
            'status' => 'CA'
        ]);

        $check_pedido_cancel = $this->repositoryPedidoProduto->where([
            'pedido_id' => $idPedido,
            'status' => 'PA'
        ])->exists();
        if(!$check_pedido_cancel){
            $this->repositoryPedido->where([
                'id' => $idPedido
            ])->update([
                'status' => 'CA'
            ]);
            $mensage = 'Compra cancelada com sucesso';
        }else{
            $mensage = 'Produto cancelado com sucesso';
        }

        return redirect()->route('carrinho.compras')->with('mensage',$mensage);;
    }

    public function desconto(Request $request)
    {
        $idPedido = $request->input('pedido_id');
        $cupom = $request->input('cupom');
        $idUsuario = Auth::id();

        if(empty($cupom))
        {
            return redirect()->route('carrinho.index')->with('mensage','Cupom inválido');
        }

        $cupom = $this->repositoryCupom->where([
            'locaizador' => $cupom,
            'ativo' => 'S'
        ])->where('validade','>',date('Y-m-d H:i:s'))->first();
        if(empty($cupom->id))
        {
            return redirect()->route('carrinho.index')->with('mensage','Cupom não encontrado');
        }


        $check_pedido = $this->repositoryPedido->where([
            'id' => $idPedido,
            'user_id' => $idUsuario,
            'status' => 'RE'
        ])->exists();
        if(!$check_pedido){
            return redirect()->route('carrinho.index')->with('mensage','Pedido não encontrado');
        }
        
        $pedido_produtos = $this->repositoryPedidoProduto->where([
            'pedido_id' => $idPedido,
            'status' => 'RE'
        ])->get();

        if(empty($pedido_produtos))
        {
            return redirect()->route('carrinho.index')->with('mensage','Produto não encontrado');
        }

        $aplicou_desconto = false;
        foreach($pedido_produtos as $pedido_produto){
            switch($cupom->modo_desconto)
            {
                case 'percent':
                $valor_desconto = ($pedido_produto->valor * $cupom->desconto) / 100;
            break;
            default:
                $valor_desconto = $cupom->desconto;
        break;        
            }
            $valor_desconto = ($valor_desconto > $pedido_produto->valor) ? $pedido_produto->valor : number_format($valor_desconto,2);
            
            switch($cupom->modo_limite){
                case 'qnt':
                    $qtd_pedido = $this->repositoryPedidoProduto->whereIn('status',['PA','RE'])->where([
                        'cupom_desconto_id' => $cupom->id
                    ])->count();
                    if($qtd_pedido >= $cupom->limite){
                        continue;
                    }
                break;
                default:
                $valor_limite_desconto = $this->repositoryPedidoProduto->whereIn('status',['PA','RE'])->qhere([
                    'cupom_desconto_id' => $cupom->id
                ])->sum('desconto');
                if(($valor_limite_desconto + $valor_desconto) > $cupom->limite ){
                    continue;
                }
                break;
            }

            $pedido_produto->cupom_desconto_id = $cupom->id;
            $pedido_produto->desconto = $valor_desconto;
            $pedido_produto->update();

            $aplicou_desconto = true;
        }

        if($aplicou_desconto){
            return redirect()->route('carrinho.index')->with('mensage','Desconto aplicado com sucesso');
        }else{
            return redirect()->route('carrinho.index')->with('mensage','Desconto esgotado');
        }
        return redirect()->route('carrinho.index');
    }
}


