<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\TagController;

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
Route::middleware('auth:sanctum', 'throttle:60,1')->group(function () {
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
        Route::post('/delete' , [TagController::class,'']);
        Route::post('/store'  , [TagController::class,'tagStore']);
        Route::post('/edit'   , [TagController::class,'']);
        Route::post('/search' , [TagController::class,'tagSearch']);
    });

    Route::prefix('/article')->group(function () {
        Route::post('/store'  , [ArticleController::class,'articleStore']);
        Route::post('/update' , [ArticleController::class,'aricleUpdate']);
        Route::post('/edit'   , [ArticleController::class,'']);
        Route::post('/delete' , [ArticleController::class,'deleteArticle']);
        Route::post('/getUserAllArticle',[ArticleController::class,'serveUserAllArticle']);
    });
});




require __DIR__.'/auth.php';
