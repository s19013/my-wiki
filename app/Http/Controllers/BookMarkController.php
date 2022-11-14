<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\BookMarkTag;
use App\Models\BookMark;


use Inertia\Inertia;

use App\Repository\BookMarkRepository;
use App\Repository\BookMarkTagRepository;
use App\Repository\TagRepository;

use App\Tools\NullAvoidanceToolKit;
use Auth;
use DB;

class BookMarkController extends Controller
{
    private $bookMarkRepository;
    private $bookMarkTagRepository;
    private $tagRepository;
    private $nullAvoidanceToolKit;

    public function __construct(
        BookMarkRepository $bookMarkRepository,
        BookMarkTagRepository $bookMarkTagRepository,
        TagRepository        $tagRepository,
        NullAvoidanceToolKit $nullAvoidanceToolKit
    )
    {
        $this->bookMarkRepository    = $bookMarkRepository;
        $this->bookMarkTagRepository = $bookMarkTagRepository;
        $this->tagRepository        = $tagRepository;
        $this->nullAvoidanceToolKit = $nullAvoidanceToolKit;
    }

    //新規ブックマーク作成
    public function store(Request $request)
    {
        // CSRFトークンを再生成して、二重送信対策
        $request->session()->regenerateToken();

        //urlがすでに登録されているか確かめる
        $isAllreadyExists =$this->bookMarkRepository->isAllreadyExists(Auth::id(),$request->bookMarkUrl);
        if ($isAllreadyExists == true) {
            return response()->json(
                ["message" => "already exists"],
                400
            );
        }

        DB::transaction(function () use($request){
            // 記事を保存して記事のidを取得
            $bookMarkId = $this->bookMarkRepository->store(
                userId   : Auth::id(),
                title    : $request->bookMarkTitle,
                url      : $request->bookMarkUrl,
            );

            // なんのタグも設定されていない時
            if (empty($request->tagList) == true) {
                $this->bookMarkTagRepository->store(
                    tagId      : null,
                    bookMarkId : $bookMarkId,
                );
            }
            //タグが設定されている時
            else {
                foreach($request->tagList as $tagId){
                    $this->bookMarkTagRepository->store(
                        tagId      : $tagId,
                        bookMarkId : $bookMarkId,
                    );
                }
            }
        });
    }

    //ブックマークの更新
    public function update(Request $request)
    {
        // CSRFトークンを再生成して、二重送信対策
        $request->session()->regenerateToken();

        DB::transaction(function () use($request){
            //ブックマークの更新
            $this->bookMarkRepository->update(
                bookMarkId:$request->bookMarkId,
                title:$request->bookMarkTitle,
                url  :$request->bookMarkUrl
            );

            //タグの更新
            $this->bookMarkTagRepository->update(
                bookMarkId     :$request->bookMarkId,
                updatedTagList :$request->tagList,
            );
        });
    }

    public function delete($bookMarkId)
    {
        // CSRFトークンを再生成して、二重送信対策
        // deleteリクエストならここの部分が必要ない?
        // $request->session()->regenerateToken();

        DB::transaction(function () use($bookMarkId){
            if ($this->bookMarkRepository->isSameUser(
                bookMarkId:$bookMarkId,
                userId:Auth::id()))
            {$this->bookMarkRepository->delete(bookMarkId:$bookMarkId);}
        });
    }

    //ブックマーク検索
    public function search(Request $request)
    {
        $tool = new NullAvoidanceToolKit();

        $result = $this->bookMarkRepository->search(
            userId:Auth::id(),
            keyword:$request->bookMarkToSearch,
            page:$this->nullAvoidanceToolKit->ifnull($request->page,1),
            tagList    :$request->tagList,
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

        // この関数がこれで動くのは､get通信で同じページに表示しているから?
        return Inertia::render('BookMark/SearchBookMark',[
            'result' => $result,
            'old' => $old
        ]);
    }
}
