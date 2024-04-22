<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Categoria;

class CategoriasController extends Controller
{
   
    /**
     * Retrieve all categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $porPagina = $request->get('per_page');
        $busca = false;

        if($request->has('campoBusca')){
            $busca = $request->get('campoBusca');
            $categorias = Categoria::where(function ($query) use($busca){
                $query->where('nome', 'like', '%' . $busca . '%');
                    
            })->orderBy('nome')->paginate($porPagina);

        }else{
            $categorias = Categoria::orderBy('nome')->paginate($porPagina);//busca todos os produtos
        }
        return response()->json($categorias, 201);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

   
    /**
     * Store a new category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $dados = $request->all();
        $request->validate([
            'nome' => 'required | min:3 | max:100',
        ]);   
        $categoria = Categoria::create($dados);
        return response()->json($categoria, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    
    /**
     * Edit a category.
     *
     * @param string $id The ID of the category to edit.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the edited category.
     */
    public function edit(string $id)
    {
        $categoria = Categoria::find($id);
        return response()->json($categoria);
    }

    /**
     * Update a category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $id)
    {
        $dados = $request->all();
        $request->validate([
            'nome' => 'required | min:3 | max:50',
        ]);
        $categoria = Categoria::find($id);
        $categoria->update($dados);
       
        return response()->json($categoria);
    }

    
    /**
     * Delete a category.
     *
     * @param string $id The ID of the category to delete.
     * @return void
     */
    public function destroy(string $id)
    {
        $categoria = Categoria::find($id);
        $categoria->delete();
    }
}
