<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
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
            
            //return $busca;
            $usuarios = User::where(function ($query) use($busca){
            $query->where('name', 'like', '%' . $busca . '%')
                ->orWhere('email', 'like', '%' . $busca . '%');
                
            })->orderBy('name')->paginate($porPagina);
        }else{
            $usuarios = User::orderBy('name')->paginate($porPagina);
        }
        return response()->json($usuarios, 201);
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    
    /**
     * Store a newly created user in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $dados = $request->all();
        $request->validate([
            'name' => 'required | min:3 | max:50',
            'email' => 'required | email',
            'password' => 'required | min:6 | max:20',
        ]);
        $user = User::create($dados);
        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    
    /**
     * Edit a user by their ID.
     *
     * @param string $id The ID of the user to edit.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the user data.
     */
    public function edit(string $id)
    {
        $user = User::find($id);
        return response()->json($user);
    }

    
    /**
     * Update a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $id)
    {
        $dados = $request->all();
        $request->validate([
            'name' => 'required | min:3 | max:50',
            'email' => 'required | email',
            'password' => 'required_if:redefinirSenha,true | min:6 | max:20',
        ]);
        $user = User::find($id);
        $user->update($dados);
        return response()->json($user);
    }

    
    /**
     * Delete a user by ID.
     *
     * @param string $id The ID of the user to delete.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        $user->delete();
        return response()->json(null, 204);
    }
}
