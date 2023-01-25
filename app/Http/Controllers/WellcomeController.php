<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;

use App\Tools\MetaToolKit;

use Auth;
use DB;

class WellcomeController extends Controller
{
    public function returnJa()
    {
        MetaToolKit::setTitle("sundlf  -- タグを使ってメモ､ブックマークを整理");

        MetaToolKit::setNameMeta('twitter:title','sundlf  -- タグを使ってメモ､ブックマークを整理');
        MetaToolKit::setNameMeta('description','メモ､ブックマークにタグを付けて保存して整理､検索などで探しやすくするツールアプリです');
        MetaToolKit::setNameMeta('twitter:description','メモ､ブックマークにタグを付けて保存して整理､検索などで探しやすくするツールアプリです');

        MetaToolKit::setPropertyMeta('og:title','sundlf  -- タグを使ってメモ､ブックマークを整理');
        MetaToolKit::setPropertyMeta('og:description','メモ､ブックマークにタグを付けて保存して整理､検索などで探しやすくするツールアプリです');

        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'lang' => 'ja',
        ]);
    }

    public function returnEn()
    {
        MetaToolKit::setTitle("sundlf  -- Organize article and bookmarks using tags");

        MetaToolKit::setNameMeta('twitter:title','sundlf  -- Organize article and bookmarks using tags');
        MetaToolKit::setNameMeta('description','this application is makes it easier to find by adding tags to memos and bookmarks, saving them, organizing them, and searching them.');
        MetaToolKit::setNameMeta('twitter:description','this application is makes it easier to find by adding tags to memos and bookmarks, saving them, organizing them, and searching them.');

        MetaToolKit::setPropertyMeta('og:title','sundlf  -- Organize article and bookmarks using tags');
        MetaToolKit::setPropertyMeta('og:description','this application is makes it easier to find by adding tags to memos and bookmarks, saving them, organizing them, and searching them.');

        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'lang' => 'en',
        ]);
    }
}
