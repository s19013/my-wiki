<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use Inertia\Inertia;
use App\Models\Tag;
use App\Models\ArticleTag;
use App\Models\Article;
use Auth;

class TransitionController extends Controller
{
    public function transitionToViewArticle($articleId)
    {
        //削除された記事ならindexに戻す
        $deleted = Article::checkArticleDeleted(articleId:$articleId);
        if ($deleted == null ) { return redirect()->route('index'); }

        // 他人の記事を覗こうとしているならindexに戻す
        $peep = Article::illegalPeep(articleId:$articleId,userId:Auth::id());
        if ($peep == false) { return redirect()->route('index'); }


        $article = Article::serveArticle(articleId:$articleId);
        $articleTag = ArticleTag::serveAricleTag(articleId:$articleId);
        return Inertia::render('ViewArticle',[
            'article'    => $article,
            'articleTag' => $articleTag,
        ]);
    }
}
