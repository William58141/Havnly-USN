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

Route::get('/help', [App\Http\Controllers\Api\HelpController::class, 'index']);
Route::post('/auth', [App\Http\Controllers\Api\AuthController::class, 'auth']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/banks', [App\Http\Controllers\Api\BankController::class, 'index']);
    Route::get('/banks/{id}', [App\Http\Controllers\Api\BankController::class, 'show']);

    Route::get('/accounts', [App\Http\Controllers\Api\AccountController::class, 'index']);
    Route::get('/accounts/{id}', [App\Http\Controllers\Api\AccountController::class, 'show']);
    Route::get('/accounts/{id}/balances', [App\Http\Controllers\Api\AccountController::class, 'showBalances']);
    Route::get('/accounts/{id}/transactions', [App\Http\Controllers\Api\AccountController::class, 'showTransactions']);

    // Route::post('/payment', [App\Http\Controllers\Api\PaymentController::class, 'index']);
    // Route::get('/payment/{id}', [App\Http\Controllers\Api\PaymentController::class, 'show']);

    Route::get('/resources/{id}', [App\Http\Controllers\Api\ResourceController::class, 'show']);
});


Route::fallback(function () {
    return response()->json([
        'ok' => false,
        'error' => 404,
        'description' => 'Not found',
    ], 404);
});
