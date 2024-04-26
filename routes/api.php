<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProdutcController;
use App\Models\Category;
use Illuminate\Http\Request;
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

Route::group([
    'prefix' => 'auth',
    'middleware' => 'api',
], function ($routes) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

//brand routes
Route::group(['prefix' => 'marcas'], function ($routes) {
    Route::controller(BrandController::class)->group(function () {
        Route::get('index', 'index')->middleware('is_admin');
        Route::get('show/{id}', 'show')->middleware('is_admin');
        Route::post('store', 'store')->middleware('is_admin');
        Route::put('update_brand/{id}', 'update_brand')->middleware('is_admin');
        Route::delete('delete_brand/{id}', 'delete_brand')->middleware('is_admin');
    });
});

//categories routes
Route::group(['prefix' => 'categorias'], function ($routes) {
    Route::controller(CategoryController::class)->group(function () {
        Route::get('index', 'index')->middleware('is_admin');
        Route::get('show/{id}', 'show')->middleware('is_admin');
        Route::post('store', 'store')->middleware('is_admin');
        Route::put('update_category/{id}', 'update_category')->middleware('is_admin');
        Route::delete('delete_category/{id}', 'delete_category')->middleware('is_admin');
    });
});

//locations routes
Route::group(['prefix' => 'enderecos'], function ($routes) {
    Route::controller(LocationController::class)->group(function () {
        Route::post('store', 'store'); 
        Route::put('update_location/{id}', 'update_location');
        Route::delete('delete_location/{id}', 'delete_location');
    });
});

//products routes
Route::group(['prefix' => 'produtos'], function ($routes) {
    Route::controller(ProdutcController::class)->group(function () {
        Route::get('index', 'index');
        Route::get('show/{id}', 'show');
        Route::post('store', 'store')->middleware('is_admin');
        Route::put('update_product/{id}', 'update_product')->middleware('is_admin');
        Route::delete('delete_product/{id}', 'delete_product')->middleware('is_admin');
    });
});


//order routes
Route::group(['prefix' => 'compras'], function ($routes) {
    Route::controller(OrderController::class)->group(function () {
        Route::get('index', 'index');
        Route::get('show/{id}', 'show');
        Route::post('store', 'store');
        Route::get('get_order_items/{id}', 'get_order_items')->middleware('is_admin');
        Route::get('get_user_orders/{id}', 'get_user_orders')->middleware('is_admin');
        Route::post('change_order_status/{id}', 'change_order_status')->middleware('is_admin');
    });
});
