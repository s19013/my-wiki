<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Tag;
use App\Models\Article;

class ArticleController extends Controller
{
    //
    public function serveUserAllTag(Request $request)
    {
        return Tag::getUserAllTag($request->userId);
    }

    public function store(Request $request)
    {
        Article::storeArticle(
                userId   : $request->userId,
                title    : $request->title,
                body     : $request->article,
                category : $request->category,
            );
    }
}
