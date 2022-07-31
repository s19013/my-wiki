<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookMarkController extends Controller
{
    //
    public function bookMarkStore(Request $request)
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

    public function bookMarkUpdate(Request $request)
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

    public function bookMarkDelete(Request $request)
    {
        $request->session()->regenerateToken();
        Article::deleteArticle(articleId:$request->articleId);
    }

    public function serveUserAllBookMark(Request $request)
    {
        // タグと記事は別々?
        return Article::serveUserAllBookMark(userId:Auth::id());
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
