<?php

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriasController;

//Rotas

// login e Usuários
Route::post('login', 'App\Http\Controllers\LoginController@login');
Route::resource('usuarios', 'App\Http\Controllers\UserController')->middleware('auth:sanctum');
Route::get('me', 'App\Http\Controllers\LoginController@me')->middleware('auth:sanctum');

//Categorias
Route::resource('categorias', CategoriasController::class);

//Produtos
Route::apiResource('produtos', 'App\Http\Controllers\ProdutosController')->middleware('auth:sanctum');
Route::get('buscarProdutosCategoria/{id}', 'App\Http\Controllers\ProdutosController@buscarProdutosPorCategoria')->middleware('auth:sanctum');
Route::get('buscarProdutosNomeDescricao', 'App\Http\Controllers\ProdutosController@buscarProdutosPorNomeOuDescricao')->middleware('auth:sanctum');
Route::post('verificarDataValidade', 'App\Http\Controllers\ProdutosController@verificarDataValidade')->middleware('auth:sanctum');

//Upload
Route::post('upload', 'App\Http\Controllers\UploadController@upload')->name('upload')->middleware('auth:sanctum');
Route::get('download/{nome}', 'App\Http\Controllers\UploadController@getImagem')->name('download');
