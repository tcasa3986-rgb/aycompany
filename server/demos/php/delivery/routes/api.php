<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\RepartidorApiController;
use App\Http\Controllers\Api\CatalogoApiController;

/*
|--------------------------------------------------------------------------
| API Routes - CRM Delivery
|--------------------------------------------------------------------------
| Usar header: Authorization: Bearer <token>
| Para registrarlo: agregar Sanctum y publicar config — ver instalar.bat
*/

Route::post('/login', [AuthApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me',     [AuthApiController::class, 'me']);
    Route::post('/logout',[AuthApiController::class, 'logout']);

    // Catálogo / pedidos
    Route::get('/categorias',     [CatalogoApiController::class, 'categorias']);
    Route::get('/productos',      [CatalogoApiController::class, 'productos']);
    Route::get('/pedidos',        [CatalogoApiController::class, 'pedidos']);
    Route::get('/pedidos/{pedido}', [CatalogoApiController::class, 'pedido']);

    // Repartidor
    Route::get('/repartidor/entregas',          [RepartidorApiController::class, 'entregas']);
    Route::post('/repartidor/entregas/{entrega}/estado', [RepartidorApiController::class, 'actualizarEntrega']);
    Route::post('/repartidor/ubicacion',        [RepartidorApiController::class, 'ubicacion']);
});
