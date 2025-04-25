<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SimulacaoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Rota GET resposavel por retornar todas as instituicoes disponiveis
Route::get('/instituicoes', [SimulacaoController::class, 'instituicoes']);

//Rota GET resposavel por retornar todos os convenios disponiveis
Route::get('/convenios', [SimulacaoController::class, 'convenios']);

//Rota POST resposavel por fazer as simulacao de credito
Route::post('/simulacaocredito', [SimulacaoController::class, 'simulacaoCredito']);

