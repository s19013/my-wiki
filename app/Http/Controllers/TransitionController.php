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
        // 関数名後で変える
        $isSamePerson = Article::preventPeep(articleId:$articleId,userId:Auth::id());
        if ($isSamePerson == false) { return redirect()->route('index'); }

        $article = Article::serveArticle(articleId:$articleId);

        $articleTag = ArticleTag::serveTagsRelatedToAricle(
            userId:Auth::id(),
            articleId:$articleId
        );
        return Inertia::render('ViewArticle',[
            'article'    => $article,
            'articleTag' => $articleTag,
        ]);
    }

    public function transitionToEditArticle(Request $request)
    {
        //categoryによって変える
        if ($request->category == 2) {
            return Inertia::render('EditArticle',[
                'originalTitle'=> $request->title,
                'originalBody' => $request->body,
                'originalTag'  => $request->tag,
                'category'     => $request->category
            ]);
        }
    }
}
