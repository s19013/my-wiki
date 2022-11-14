<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ArticleTransitionController;
use App\Http\Controllers\BookMarkController;
use App\Http\Controllers\BookMarkTransitionController;

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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/test', function () {
        return Inertia::render('test');
    })->name('test');


    Route::prefix('Article')->group(function () {
        Route::get('/Create', function () {
            return Inertia::render('Article/CreateArticle');
        })->name('CreateArticle');

        Route::get('/View/{articleId}', [ArticleTransitionController::class,'transitionToViewArticle'])->name('ViewArticle');

        Route::get('/Edit/{articleId}', [ArticleTransitionController::class,'transitionToEditArticle'])->name('EditArticle');

        Route::get('/Search',[ArticleController::class,'search'])->name('SearchArticle');

        // Route::post('/Store'  , [ArticleController::class,'store'])->name('StoreArticle');
        // Route::put('/Update' , [ArticleController::class,'update'])->name('UpdateArticle');
        // Route::delete('/{articleId}' , [ArticleController::class,'delete'])->name('DeleteArticle');
    });

    Route::prefix('BookMark')->group(function () {
        Route::get('/Create', function () {
            return Inertia::render('BookMark/CreateBookMark');
        })->name('CreateBookMark');

        Route::get('/Edit/{bookMarkId}', [BookMarkTransitionController::class,'transitionToEditBookMark'])->name('EditBookMark');

        Route::get('/Search' , [BookMarkController::class,'search'])->name('SearchBookMark');

        // Route::post('/Store'  , [BookMarkController::class,'store'])->name('StoreBookMark');
        // Route::put('/Update' , [BookMarkController::class,'update'])->name('UpdateBookMark');
        // Route::delete('/{bookMarkId}' , [BookMarkController::class,'delete'])->name('DeleteBookMark')
    });

});

require __DIR__.'/auth.php';
