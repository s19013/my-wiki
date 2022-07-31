<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;


use App\Models\ArticleTag;
use App\Models\Article;
use Auth;

class ArticleController extends Controller
{

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

    public function aricleUpdate(Request $request)
    {
        $request->session()->regenerateToken();

        Article::updateArticle(
            articleId:$request->articleId,
            title:$request->articleTitle,
            body :$request->articleBody
        );
        ArticleTag::updateAricleTag(
            articleId     :$request->articleId,
            updatedTagList:$request  ->tagList,
        );
    }

    public function articleDelete(Request $request)
    {
        $request->session()->regenerateToken();
        Article::articleDelete(articleId:$request->articleId);
    }

    public function serveUserAllArticle(Request $request)
    {
        // タグと記事は別々?
        return Article::serveUserAllArticle(
            userId:Auth::id(),
            category:2);;
    }

    // 編集か新規かを分ける
    // public function DetermineProcessing(Request $request)
    // {
    //     //新規
    //     if ($request->articleId == 0) { $this->articleStore($request); }
    //     // 編集
    //     else {
    //         $this->aricleUpdate($request);
    //     }
    // }
}
