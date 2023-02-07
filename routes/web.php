<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ArticleTransitionController;
use App\Http\Controllers\BookMarkController;
use App\Http\Controllers\BookMarkTransitionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\WellcomeController;

use App\Tools\MetaToolKit;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('/',function(){
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'lang' => "ja"
    ]);
})->middleware("setMeta");

Route::get('/en', function() {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'lang' => "en"
    ]);
})->middleware("setMeta");

// ads.text
// Route::get('/ads.text', function () {
//     return response()->file("ads.text");
// });

// Route::get('/test', function () {
//     return Inertia::render('test');
// })->name('test');

Route::middleware(['auth', 'verified','setMeta'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('setting',function () {
        return Inertia::render('Setting');
    })->name('setting');


    Route::prefix('Article')->group(function () {
        Route::get('/Create', function () {
            return Inertia::render('Article/CreateArticle');
        })->name('CreateArticle');

        Route::get('/View/{articleId}', [ArticleTransitionController::class,'transitionToViewArticle'])->name('ViewArticle');

        Route::get('/Edit/{articleId}', [ArticleTransitionController::class,'transitionToEditArticle'])->name('EditArticle');

        Route::get('/Search',[ArticleController::class,'search'])->name('SearchArticle');

    });

    Route::prefix('BookMark')->group(function () {
        Route::get('/Create', function () {
            return Inertia::render('BookMark/CreateBookMark');
        })->name('CreateBookMark');

        Route::get('/Edit/{bookMarkId}', [BookMarkTransitionController::class,'transitionToEditBookMark'])->name('EditBookMark');

        Route::get('/Search' , [BookMarkController::class,'search'])->name('SearchBookMark');
    });

    Route::prefix('Tag')->group(function () {
        // 編集画面での検索
        Route::get('/Edit/Search' , [TagController::class,'transitionToEdit'])->name('searchInEditTag');
    });

    Route::prefix('User')->group(function () {
        Route::delete('/Delete' , [UserController::class,'delete'])->name('DeleteUser');
    });



});

require __DIR__.'/auth.php';
