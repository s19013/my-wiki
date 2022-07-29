<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Tag;
use App\Models\ArticleTag;
use App\Models\Article;
use Auth;

class ArticleController extends Controller
{
    public function serveUserAllTag()
    {
        return Tag::getUserAllTag(userId:Auth::id());
    }

    public function articleStore(Request $request)
    {
        // CSRFトークンを再生成して、二重送信対策
        $request->session()->regenerateToken();
        // 記事を保存して記事のidを取得
        $articleId = Article::storeArticle(
                userId   : Auth::id(),
                title    : $request->articleTitle,
                body     : $request->articleBody,
                category : $request->category,
        );

        // なんのタグも設定されていない時
        if (empty($request->tagList) == true) {
            ArticleTag::storeArticleTag(
                userId    : Auth::id(),
                tagId     : null,
                articleId : $articleId,
            );
        }
        //タグが設定されている時
        else {
            foreach($request->tagList as $tagId){
                ArticleTag::storeArticleTag(
                    userId    : Auth::id(),
                    tagId     : $tagId,
                    articleId : $articleId,
                );
            }
        }
    }

    public function tagStore(Request $request)
    {
        // CSRFトークンを再生成して、二重送信対策
        $request->session()->regenerateToken();
        return Tag::store(
            userId:Auth::id(),
            tag   :$request->tag,
        );
    }

    public function serveAddedTag()
    {
        return Tag::serveAddedTag(userId:Auth::id());
    }

    public function tagSearch(Request $request)
    {
        return Tag::search(
            userId:Auth::id(),
            tag   :$request->tag
        );
    }

    public function deleteArticle(Request $request)
    {
        Article::deleteArticle(articleId:$request->articleId);
    }

    // public function articleRead(Request $request)
    // {
    //     return Article::serveArticle(articleId:$request->articleId);
    // }

    // public function tagRead(Request $request)
    // {
    //     return ArticleTag::serveTagsRelatedToAricle(articleId:$request->articleId);
    // }

    public function serveUserAllArticle(Request $request)
    {
        // タグと記事は別々?
        return Article::serveUserAllArticle(userId:Auth::id());
    }
}
