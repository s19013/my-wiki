<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ArticleController;

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

// 認証が必要な部分
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/test', function (Request $request) {
        return response()->json([
            "message" => "ok"
        ]);
    });
    Route::post('/postTest', function (Request $request) {
        return response()->json([
            "message" => $request->message
        ]);
    });

    Route::prefix('/tag')->group(function () {
        Route::post('/delete',[ArticleController::class,'']);
        Route::post('/store',[ArticleController::class,'tagStore']);
        Route::post('/edit',[ArticleController::class,'']);
        Route::post('/serveUserAllTag',[ArticleController::class,'serveUserAllTag']);
        Route::post('/serveAddedTag',[ArticleController::class,'serveAddedTag']);
        Route::post('/search',[ArticleController::class,'tagSearch']);
    });

    Route::prefix('/article')->group(function () {
        Route::post('/store',[ArticleController::class,'articleStore']);
        Route::post('/edit',[ArticleController::class,'']);
        Route::post('/delete',[ArticleController::class,'']);
        Route::post('/serveUserAllArticle',[ArticleController::class,'']);
    });
});




require __DIR__.'/auth.php';
