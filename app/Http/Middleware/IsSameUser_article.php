<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use App\Repository\ArticleRepository;

use Auth;

class IsSameUser_article
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
        $articleRepository = new ArticleRepository();
        $articleId = $request->articleId;

        //削除された記事ならExceptionを投げる
        $isDeleted = $articleRepository->isDeleted(articleId:$articleId);
        if ($isDeleted == true ) {return redirect()->route('SearchArticle');}

        // 他人の記事を覗こうとしているならExceptionを投げる
        $isSamePerson = $articleRepository->isSameUser(articleId:$articleId,userId:Auth::id());
        if ($isSamePerson == false) {return redirect()->route('SearchArticle');}

        return $next($request);
    }
}
