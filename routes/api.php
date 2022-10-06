<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\BookMarkController;

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
        Route::delete('/{tagId}' , [TagController::class,'tagDelete'])->name('api.tag.delete');
        Route::post('/store'  , [TagController::class,'tagStore']);
        Route::post('/update' , [TagController::class,'tagUpdate']);
        Route::post('/search' , [TagController::class,'tagSearch']);
    });

    Route::prefix('/article')->group(function () {
        Route::post('/store'  , [ArticleController::class,'articleStore']);
        Route::post('/update' , [ArticleController::class,'articleUpdate']);
        Route::delete('/{articleId}' , [ArticleController::class,'articleDelete'])->name('api.article.delete');
        Route::post('/search' , [ArticleController::class,'articleSearch']);
    });

    Route::prefix('/bookmark')->group(function () {
        Route::post('/store'  , [BookMarkController::class,'bookMarkStore']);
        Route::post('/update' , [BookMarkController::class,'bookMarkUpdate']);
        Route::delete('/{bookMarkId}' , [BookMarkController::class,'bookMarkDelete'])->name('api.bookMark.delete');
        Route::post('/search' , [BookMarkController::class,'bookMarkSearch']);
    });
});




require __DIR__.'/auth.php';
