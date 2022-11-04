<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use Inertia\Inertia;

use App\Models\ArticleTag;
use App\Models\Article;

use App\Repository\ArticleRepository;
use App\Repository\ArticleTagRepository;

use Auth;

class ArticleController extends Controller
{

    private $articleRepository;
    private $articleTagRepository;

    public function __construct(ArticleRepository $articleRepository,ArticleTagRepository $articleTagRepository)
    {
        $this->articleRepository = $articleRepository;
        $this->articleTagRepository = $articleTagRepository;
    }
    //新規記事作成
    public function store(Request $request)
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
            $this->articleTagRepository->store(
                tagId     : null,
                articleId : $articleId,
            );
        }
        //タグが設定されている時
        else {
            foreach($request->tagList as $tagId){
                $this->articleTagRepository->store(
                    tagId     : $tagId,
                    articleId : $articleId,
                );
            }
        }
    }

    //記事更新
    public function update(Request $request)
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
        $this->articleTagRepository->update(
            articleId     :$request->articleId,
            updatedTagList:$request->tagList,
        );
    }

    //記事削除
    public function delete($articleId)
    {
        // CSRFトークンを再生成して、二重送信対策
        // deleteリクエストならここの部分が必要ない?
        // //$request->session()->regenerateToken();

        if ($this->articleRepository->isSameUser(
                articleId:$articleId,
                userId:Auth::id()))
        {
            $this->articleRepository->delete(articleId:$articleId);
        }
    }

    //記事検索
    public function search(Request $request)
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

}
