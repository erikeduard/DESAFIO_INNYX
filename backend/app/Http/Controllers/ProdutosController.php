<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Produto;
use Carbon\Carbon;
use App\Models\Categoria;


class ProdutosController extends Controller
{
   
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $porPagina = $request->get('per_page');
        $busca = false;

        if($request->has('campoBusca')){
            $busca = $request->get('campoBusca');
            $produtos = Produto::where(function ($query) use($busca){
                $query->where('nome', 'like', '%' . $busca . '%')
                    ->orWhere('descricao', 'like', '%' . $busca . '%');
                    
            })->orderBy('nome')->paginate($porPagina);

        }else{
            $produtos = Produto::orderBy('nome')->paginate($porPagina);//busca todos os produtos
        }
        $categorias = Categoria::all();
        $produtos->load(['categoria']);
        return response()->json(['data'=>$produtos, 'categorias'=>$categorias], 201);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $dados = $request->all();
        //mudando a data para o formato do banco de dados
        $dados['data_validade'] = Carbon::createFromFormat('d/m/Y', $dados['data_validade'])->format('Y-m-d');
       
        $request->validate([
            'nome' => 'required | min:3 | max:50',
            'descricao' => 'required | min:3 | max:200',
            'preco' => 'required | numeric',
            'data_validade' => 'required | date_format:d/m/Y',
            'categoria_id' => 'required | exists:categorias,id',
        ]);
        $produto=Produto::create($dados);
        
        return response()->json($produto, 201) ;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $produto = Produto::find($id);
        $produto->load(['categoria']);
        return response()->json($produto, 201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    
    /**
     * Update a product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $id)
    {
        $dados = $request->all();
        //mudando a data para o formato do banco de dados
        $dados['data_validade'] = Carbon::createFromFormat('d/m/Y', $dados['data_validade'])->format('Y-m-d');

        $request->validate([
            'nome' => 'required | min:3 | max:50',
            'descricao' => 'required | min:3 | max:200',
            'preco' => 'required | numeric',
            'data_validade' => 'required | date_format:d/m/Y',
            'imagem' => 'required',
            'categoria_id' => 'required | exists:categorias,id',
        ]);
        $produto = Produto::find($id);
        $produto->update($dados);
        return response()->json($produto, 201) ;
    }

    public function buscarProdutosPorCategoria(string $id)
    {
        $produtos = Produto::where('categoria_id', $id)->get();
        $produtos->load(['categoria']);
        return response()->json($produtos, 201) ;
    }

    /**
     * Verifies the validity of a given date.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verificarDataValidade(Request $request)
    {
        if($request->get('data_validade') == null  || $request->get('data_validade') == ''){
            return response()->json(false, 201);
        }
        
        $data_validade = $request->get('data_validade');
        
        $data_validade = Carbon::createFromFormat('d/m/Y', $data_validade);
       
        $data_atual = Carbon::now();
        if($data_validade->gt($data_atual)){
            return response()->json(true, 201);
        }else{
            return response()->json(false, 201);
        }
    }
    

    /**
     * Delete a product.
     *
     * @param string $id The ID of the product to delete.
     * @return void
     */
    public function destroy(string $id)
    {
        $produto = Produto::find($id);
        $produto->delete();
    }
}
