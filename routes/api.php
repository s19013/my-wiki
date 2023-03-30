<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\BookMarkController;

use App\Http\Controllers\Extended\ExtendedUserController;

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

// 拡張機能のログイン

Route::post('/extended/login', [ExtendedUserController::class, 'login']);
Route::get('/extended/logout', [ExtendedUserController::class, 'logout']);

// 認証が必要な部分
Route::middleware('auth:sanctum', 'throttle:30,1')->group(function () {
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
        Route::delete('/{tagId}' , [TagController::class,'delete'])->name('api.tag.delete');
        Route::post('/store'  , [TagController::class,'store']);
        Route::put('/update' , [TagController::class,'update']);
        // ダイアログ内の検索
        // 自分でもなんでpostにしてしまったのかわすれたけど､特に困ってないから放置でいいや
        Route::post('/search' , [TagController::class,'search']);
    });

    Route::prefix('/article')->group(function () {
        Route::post('/store'  , [ArticleController::class,'store']);
        Route::put('/update' , [ArticleController::class,'update']);
        Route::delete('/{articleId}' , [ArticleController::class,'delete'])->name('api.article.delete');
        Route::get('/search' , [ArticleController::class,'search']);
    });

    Route::prefix('/bookmark')->group(function () {
        Route::post('/store'  , [BookMarkController::class,'store']);
        Route::put('/update' , [BookMarkController::class,'update']);
        Route::delete('/{bookMarkId}' , [BookMarkController::class,'delete'])->name('api.bookMark.delete');
        Route::get('/search' , [BookMarkController::class,'search']);

        // カウントアップ用
        Route::get('/countup/{bookMarkId}',[BookMarkController::class,'countup']);
    });

    Route::get('/extended/bookmark/', [BookMarkController::class, 'serveBookMarkToExtended']);
});




require __DIR__.'/auth.php';
