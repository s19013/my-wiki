<?php

use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/test', function (Request $request) {
    return response()->json([
        "message" => "ok"
    ]);
});

Route::middleware('auth:sanctum')->post('/postTest', function (Request $request) {
    return response()->json([
        "message" => $request->message
    ]);
});

require __DIR__.'/auth.php';
