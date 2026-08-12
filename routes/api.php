<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

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

Route::apiResource('produtos', ProductController::class)
    ->parameters(['produtos' => 'product'])
    ->only(['index', 'show']);

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth:api')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/user-profile', [AuthController::class, 'userProfile']);
    });

    Route::apiResource('enderecos', LocationController::class)
        ->parameters(['enderecos' => 'location'])
        ->only(['store', 'update', 'destroy']);

    Route::prefix('compras')->controller(OrderController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/{order}', 'show');
        Route::post('/', 'store');
    });

    Route::apiResource('marcas', BrandController::class)
        ->parameters(['marcas' => 'brand'])
        ->only(['index', 'show', 'store', 'update', 'destroy']);

    Route::apiResource('categorias', CategoryController::class)
        ->parameters(['categorias' => 'category'])
        ->only(['index', 'show', 'store', 'update', 'destroy']);

    Route::apiResource('produtos', ProductController::class)
        ->parameters(['produtos' => 'product'])
        ->only(['store', 'update', 'destroy']);

    Route::prefix('compras')->controller(OrderController::class)->group(function () {
        Route::get('/usuarios/{userId}', 'getUserOrders');
        Route::get('/{order}/itens', 'getOrderItems');
        Route::patch('/{order}/status', 'updateStatus');
    });
});
