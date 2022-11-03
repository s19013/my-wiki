<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;


use App\Models\ArticleTag;
use App\Models\Article;

use App\Repository\ArticleRepository;

use Auth;

class ArticleController extends Controller
{

    public $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }
    //新規記事作成
    public function articleStore(Request $request)
    {
        // CSRFトークンを再生成して、二重送信対策
        $request->session()->regenerateToken();

        // 記事を保存して記事のidを取得
        $articleId = $this->articleRepository->store(
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
        $this->articleRepository->update(
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
        // deleteリクエストならここの部分が必要ない?
        // $request->session()->regenerateToken();

        $this->articleRepository->delete(articleId:$articleId);
    }

    //記事検索
    public function articleSearch(Request $request)
    {
        $result = $this->articleRepository->search(
            userId:Auth::id(),
            articleToSearch:$request->articleToSearch,
            currentPage:$request->currentPage,
            tagList:$request->tagList,
            searchTarget:$request->searchTarget
        );
        return response()->json($result,200);
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
