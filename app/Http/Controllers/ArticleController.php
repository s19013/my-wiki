<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use Inertia\Inertia;

use App\Models\ArticleTag;
use App\Models\Article;

use App\Repository\ArticleRepository;
use App\Repository\ArticleTagRepository;
use App\Repository\TagRepository;

use App\Http\Requests\ArticleRequest;

use Auth;
use DB;

class ArticleController extends Controller
{

    private $articleRepository;
    private $articleTagRepository;
    private $tagRepository;

    public function __construct()
    {
        $this->articleRepository    = new ArticleRepository();
        $this->articleTagRepository = new ArticleTagRepository();
        $this->tagRepository        = new TagRepository();
    }
    //新規記事作成
    public function store(ArticleRequest $request)
    {
        // webアプリからの場合,CSRFトークンを再生成して、二重送信対策
        $url = explode('/', $request->path() );
        if ($url[1] != 'extended') {$request->session()->regenerateToken();}

        $id = DB::transaction(function () use($request){
            // 記事を保存して記事のidを取得
            $articleId  = $this->articleRepository->store(
                userId   : Auth::id(),
                title    : $request->articleTitle,
                body     : $request->articleBody,
                timezone : $request->timezone,
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
            return $articleId;
        });


        // 次から更新するためにidをわたしとく
        return response()->json([
            'articleId' => $id,
        ]);
    }

    //記事更新
    public function update(ArticleRequest $request)
    {
        // webアプリからの場合,CSRFトークンを再生成して、二重送信対策
        $url = explode('/', $request->path() );
        if ($url[1] != 'extended') {$request->session()->regenerateToken();}

        // 同一人物か確認
        $isSameUser = $this->articleRepository->isSameUser(
            articleId:$request->articleId,
            userId:Auth::id());

        if (!$isSameUser) {return response('',401);}

        DB::transaction(function () use($request){
            // 記事更新
             $this->articleRepository->update(
                 articleId:$request->articleId,
                 timezone :$request->timezone,
                 title:$request->articleTitle,
                 body :$request->articleBody,

             );

             //タグ更新
             $this->articleTagRepository->update(
                 articleId     :$request->articleId,
                 updatedTagList:$request->tagList,
             );
         });
    }

    //記事削除
    public function delete($articleId)
    {
        // deleteリクエストならCSRFトークンを再生成して、二重送信対策の部分が必要ない?

        // 消そうとしてるブックマークを登録したユーザーのidと
        // 処理を実行しようとしているユーザーが同じか確かめる
        // ->他の人がかってに他の人のブックマークを消せないようにするため
        // 同一人物か確認
        $isSameUser = $this->articleRepository->isSameUser(
            articleId:$articleId,
            userId:Auth::id());

        if (!$isSameUser) {return response('',401);}

        DB::transaction(function () use($articleId){
            $this->articleRepository->delete(articleId:$articleId);
        });
    }

    //記事検索
    public function search(Request $request)
    {
        $result = $this->articleRepository->search(
            userId :Auth::id(),
            keyword:$request->keyword,
            page   :$request->page ?? 1,
            tagList:$request->tagList,
            searchTarget:$request->searchTarget ?? "title",
            searchQuantity:$request->searchQuantity ?? 10,
            sortType:$request->sortType ?? "updated_at_desc",
            isSearchUntagged:$request->isSearchUntagged ?? false
        );

        $tagList = [];

        // 最初の検索だけ$request->tagListにnullが入る
        // nullの状態でforeachをするとtagなんて項目は無いよとエラーを吐かれる｡それを避けるため
        if (!is_null($request->tagList)) {
            foreach ($request->tagList as $tag){
                $temp = $this->tagRepository->findFromId(
                        userId:Auth::id(),
                        tagId :$tag
                );
                array_push($tagList,$temp);
            }
        }

        $old = [
            "keyword" => $request->keyword ?? "",
            "tagList" => $tagList,
            "searchTarget"   => $request->searchTarget ?? "title",
            "searchQuantity" => $request->searchQuantity ?? 10,
            "sortType" => $request->sortType ?? "updated_at_desc",
            "isSearchUntagged" => $request->isSearchUntagged ?? false
        ];

        return Inertia::render('Article/SearchArticle',[
            'result' => $result,
            'old' => $old
        ]);
    }
}
