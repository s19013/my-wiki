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
        $article = Article::serveArticle(articleId:$articleId);
        $articleTag = ArticleTag::serveAricleTag(articleId:$articleId);
        return Inertia::render('ViewArticle',[
            'article'    => $article,
            'articleTag' => $articleTag,
        ]);
    }
}
