<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Tag;
use App\Models\ArticleTag;
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
        // 記事を保存して記事のidを取得
        $articleId = Article::storeArticle(
                userId   : $request->userId,
                title    : $request->articleTitle,
                body     : $request->articleBody,
                category : $request->category,
        );

        // なんのタグも設定されていない時
        if (empty($request->tagList) == true) {
            ArticleTag::storeArticleTag(
                userId    : $request->userId,
                tagId     : null,
                articleId : $articleId,
            );
        }
        //タグが設定されている時
        else {
            foreach($request->tagList as $tagId){
                ArticleTag::storeArticleTag(
                    userId    : $request->userId,
                    tagId     : $tagId,
                    articleId : $articleId,
                );
            }
        }
    }
}
