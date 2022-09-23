<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;


use App\Models\ArticleTag;
use App\Models\Article;
use Auth;

class ArticleController extends Controller
{
    //新規記事作成
    public function articleStore(Request $request)
    {
        // CSRFトークンを再生成して、二重送信対策
        $request->session()->regenerateToken();

        // 記事を保存して記事のidを取得
        $articleId = Article::storeArticle(
                userId   : Auth::id(),
                title    : $request->articleTitle,
                body     : $request->articleBody,
        );

        // なんのタグも設定されていない時
        if (empty($request->tagList) == true) {
            ArticleTag::storeArticleTag(
                tagId     : null,
                articleId : $articleId,
            );
        }
        //タグが設定されている時
        else {
            foreach($request->tagList as $tagId){
                ArticleTag::storeArticleTag(
                    tagId     : $tagId,
                    articleId : $articleId,
                );
            }
        }
    }

    //記事更新
    public function articleUpdate(Request $request)
    {
        // CSRFトークンを再生成して、二重送信対策
        $request->session()->regenerateToken();

        // 記事更新
        Article::updateArticle(
            articleId:$request->articleId,
            title:$request->articleTitle,
            body :$request->articleBody
        );

        //タグ更新
        ArticleTag::updateArticleTag(
            articleId     :$request->articleId,
            updatedTagList:$request->tagList,
        );
    }

    //記事削除
    public function articleDelete($articleId)
    {
        // CSRFトークンを再生成して、二重送信対策
        // $request->session()->regenerateToken();

        Article::deleteArticle(articleId:$articleId);
    }

    //記事検索
    public function articleSearch(Request $request)
    {
        return Article::searchArticle(
            userId:Auth::id(),
            articleToSearch:$request->articleToSearch,
            currentPage:$request->currentPage,
            tagList:$request->tagList,
            searchTarget:$request->searchTarget
        );
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
