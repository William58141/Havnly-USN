<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::post('/auth', [App\Http\Controllers\Api\AuthController::class, 'index']);

Route::get('/banks', [App\Http\Controllers\Api\BankController::class, 'index']);
Route::get('/banks/{id}', [App\Http\Controllers\Api\BankController::class, 'show']);

Route::get('/accounts', [App\Http\Controllers\Api\AccountController::class, 'index']);
Route::get('/accounts/{id}', [App\Http\Controllers\Api\AccountController::class, 'show']);
Route::get('/accounts/{id}/balances', [App\Http\Controllers\Api\AccountController::class, 'balances']);
Route::get('/accounts/{id}/transactions', [App\Http\Controllers\Api\AccountController::class, 'transactions']);


// catch all 404
Route::fallback(function() {
    return response()->json([
        'ok' => false,
        'error' => 404,
        'message' => 'Not found',
    ], 404);
});
