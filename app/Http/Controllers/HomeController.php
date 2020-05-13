<?php

namespace App\Http\Controllers;

use App\Produto;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $produtos = Produto::paginate();
        return view('home',[
            'produtos' => $produtos
            ]);
    }

    public function show($id)
    {
        $produtos = Produto::where('id',$id)->first();
        if(!$produtos){
            return redirect()->back();
        }
        return view('showProduct',[
            'produto' => $produtos
        ]);
    }
}
