<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Tools\MetaToolKit;

class SetMeta
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        MetaToolKit::setLang((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0,2)));

        MetaToolKit::setUrl(
            $ja='https://sundlf.com/ja',
            $en='https://sundlf.com/en'
        );

        MetaToolKit::setImg(
            $ja='https://sundlf.com/sundlf_logo_og.png',
            $en='https://sundlf.com/sundlf_logo_og.png'
        );

        MetaToolKit::setMeta(
            $ja='og:locale','ja_JP',
            $en='og:locale','en_US'
        );

        MetaToolKit::setTitleTag(
            $ja="sundlf  -- タグを使ってメモ､ブックマークを整理",
            $en="sundlf  -- Organize article and bookmarks using tags"
        );

        MetaToolKit::setTitle(
            $ja="sundlf  -- タグを使ってメモ､ブックマークを整理",
            $en="sundlf  -- Organize article and bookmarks using tags"
        );

        MetaToolKit::setDescription(
            $ja="メモ､ブックマークにタグを付けて保存して整理､検索などで探しやすくするツールアプリです",
            $en="description','this application is makes it easier to find by adding tags to memos and bookmarks, saving them, organizing them, and searching them."
        );

        return $next($request);
    }
}
