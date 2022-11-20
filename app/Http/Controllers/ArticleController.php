<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use Inertia\Inertia;

use App\Models\ArticleTag;
use App\Models\Article;

use App\Repository\ArticleRepository;
use App\Repository\ArticleTagRepository;
use App\Repository\TagRepository;

use App\Tools\NullAvoidanceToolKit;

use App\Http\Requests\ArticleRequest;

use Auth;
use DB;

class ArticleController extends Controller
{

    private $articleRepository;
    private $articleTagRepository;
    private $tagRepository;
    private $nullAvoidanceToolKit;

    public function __construct(
        ArticleRepository    $articleRepository,
        ArticleTagRepository $articleTagRepository,
        TagRepository        $tagRepository,
        NullAvoidanceToolKit $nullAvoidanceToolKit
    )
    {
        $this->articleRepository    = $articleRepository;
        $this->articleTagRepository = $articleTagRepository;
        $this->tagRepository        = $tagRepository;
        $this->nullAvoidanceToolKit = $nullAvoidanceToolKit;
    }
    //新規記事作成
    public function store(ArticleRequest $request)
    {
        // CSRFトークンを再生成して、二重送信対策
        $request->session()->regenerateToken();


        DB::transaction(function () use($request){
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
        });
    }

    //記事更新
    public function update(ArticleRequest $request)
    {
        // CSRFトークンを再生成して、二重送信対策
        $request->session()->regenerateToken();

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
        // CSRFトークンを再生成して、二重送信対策
        // deleteリクエストならここの部分が必要ない?
        //$request->session()->regenerateToken();

        // 消そうとしてるブックマークを登録したユーザーのidと
        // 処理を実行しようとしているユーザーが同じか確かめる
        // ->他の人がかってに他の人のブックマークを消せないようにするため

        DB::transaction(function () use($articleId){
            if ($this->articleRepository->isSameUser(
                articleId:$articleId,
                userId:Auth::id()))
            {$this->articleRepository->delete(articleId:$articleId);}
        });
    }

    //記事検索
    public function search(Request $request)
    {
        $tool = new NullAvoidanceToolKit();

        $result = $this->articleRepository->search(
            userId :Auth::id(),
            keyword:$request->keyword,
            page   :$this->nullAvoidanceToolKit->ifnull($request->page,1),
            tagList:$request->tagList,
            searchTarget:$this->nullAvoidanceToolKit->ifnull($request->searchTarget,"title")
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
            "keyword" => $this->nullAvoidanceToolKit->ifnull($request->keyword,""),
            "tagList" => $tagList,
            "searchTarget" => $this->nullAvoidanceToolKit->ifnull($request->searchTarget,"title")
        ];

        return Inertia::render('Article/SearchArticle',[
            'result' => $result,
            'old' => $old
        ]);
    }
}
