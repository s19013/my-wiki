<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\TransitionController;

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

        Route::get('/View/{articleId}', [TransitionController::class,'transitionToViewArticle'])->name('ViewAricle');

        Route::get('/Edit/{articleId}', [TransitionController::class,'transitionToEditArticle'])->name('EditAricle');

        Route::get('/Search', function () {
            return Inertia::render('Article/SearchArticle');
        })->name('SearchArticle');
    });

    Route::prefix('BookMark')->group(function () {
        Route::get('/Create', function () {
            return Inertia::render('BookMark/CreateBookMark');
        })->name('CreateBookMark');

        Route::get('/Edit/{articleId}', [TransitionController::class,'transitionToEditArticle'])->name('EditBookMark');

        Route::get('/Search', function () {
            return Inertia::render('BookMark/SearchBookMark');
        })->name('SearchBookMark');
    });

});

require __DIR__.'/auth.php';
