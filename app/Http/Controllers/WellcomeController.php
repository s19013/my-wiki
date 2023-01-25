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
        MetaToolKit::setUrl('https://sundlf.com/ja');

        MetaToolKit::setImg('https://sundlf.com/sundlf_logo_og.png');

        MetaToolKit::setMeta('og:locale','ja_JP');

        MetaToolKit::setTitleTag("sundlf  -- タグを使ってメモ､ブックマークを整理");

        MetaToolKit::setTitle("sundlf  -- タグを使ってメモ､ブックマークを整理");

        MetaToolKit::setDescription("メモ､ブックマークにタグを付けて保存して整理､検索などで探しやすくするツールアプリです");

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
        MetaToolKit::setUrl('https://sundlf.com/en');

        MetaToolKit::setImg('https://sundlf.com/sundlf_logo_og.png');

        MetaToolKit::setMeta('og:locale','en_US');

        MetaToolKit::setTitleTag("sundlf  -- Organize article and bookmarks using tags");

        MetaToolKit::setTitle("sundlf  -- Organize article and bookmarks using tags");

        MetaToolKit::setDescription("description','this application is makes it easier to find by adding tags to memos and bookmarks, saving them, organizing them, and searching them.");

        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'lang' => 'en',
        ]);
    }
}
