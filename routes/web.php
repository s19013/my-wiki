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

    Route::get('/CreateArticle', function () {
        return Inertia::render('EditArticle',[
            'rederTest' => 'test'
        ]);
    })->name('CreateArticle');

    Route::get('/ViewArticle/{articleId}', [TransitionController::class,'transitionToViewArticle'])->name('ViewAricle');

    Route::get('/index', function () {
        return Inertia::render('index');
    })->name('index');


});

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::get('/createArticle', function () {
//     return Inertia::render('createArticle');
// })->middleware(['auth', 'verified'])->name('createArticle');

// Route::get('/test', function () {
//     return Inertia::render('test');
// })->middleware(['auth', 'verified'])->name('test');

require __DIR__.'/auth.php';
