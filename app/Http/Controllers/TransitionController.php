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
        $isDeleted = Article::checkArticleDeleted(articleId:$articleId);
        if ($isDeleted == true ) { return redirect()->route('index'); }

        // 他人の記事を覗こうとしているならindexに戻す
        $isSamePerson = Article::preventPeep(articleId:$articleId,userId:Auth::id());
        if ($isSamePerson == false) { return redirect()->route('index'); }


        $article = Article::serveArticle(articleId:$articleId);

        $articleTag = ArticleTag::serveTagsRelatedToAricle(
            userId:Auth::id(),
            articleId:$articleId
        );
        return Inertia::render('Article/ViewArticle',[
            'article'    => $article,
            'articleTag' => $articleTag,
        ]);
    }

    public function transitionToEditArticle($articleId)
    {
        //削除された記事ならindexに戻す
        $isDeleted = Article::checkArticleDeleted(articleId:$articleId);
        if ($isDeleted == true ) { return redirect()->route('SearchArticle'); }

        // 他人の記事を覗こうとしているならindexに戻す
        $isSamePerson = Article::preventPeep(articleId:$articleId,userId:Auth::id());
        if ($isSamePerson == false) { return redirect()->route('SearchArticle'); }

        $article = Article::serveArticle(articleId:$articleId);

        $articleTag = ArticleTag::serveTagsRelatedToAricle(
            userId:Auth::id(),
            articleId:$articleId
        );


        return Inertia::render('Article/EditArticle',[
            'originalArticle'     => $article,
            'originalCheckedTag' => $articleTag,
        ]);
    }
}
