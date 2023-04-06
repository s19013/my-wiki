<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\BookMarkTag;
use App\Models\BookMark;


use Inertia\Inertia;

use App\Repository\BookMarkRepository;
use App\Repository\BookMarkTagRepository;
use App\Repository\TagRepository;

use App\Http\Requests\BookMarkRequest;

use Auth;
use DB;

class BookMarkController extends Controller
{
    private $bookMarkRepository;
    private $bookMarkTagRepository;
    private $tagRepository;

    public function __construct()
    {
        $this->bookMarkRepository    = new BookMarkRepository();
        $this->bookMarkTagRepository = new BookMarkTagRepository();
        $this->tagRepository        = new TagRepository();
    }

    //新規ブックマーク作成
    public function store(BookMarkRequest $request)
    {
        // CSRFトークンを再生成して、二重送信対策
        $request->session()->regenerateToken();

        //urlがすでに登録されているか確かめる
        $isAllreadyExists =$this->bookMarkRepository->isAllreadyExists(Auth::id(),$request->bookMarkUrl);
        if ($isAllreadyExists == true) {
            try {
                if ((substr($request->headers->get("UserLang"), 0,2)) == 'ja'){
                    return response()->json([
                        'messages' => ["bookMarkUrl" => ["そのブックマークは既に保存しています"]],
                        ],
                        400);
                }
            } catch (\Throwable $th) {}
            return response()->json([
                'messages' => ["bookMarkUrl" => ["this bookmark is already saved"]],
                ],
                400);
        }

        DB::transaction(function () use($request){
            // 記事を保存して記事のidを取得
            $bookMarkId = $this->bookMarkRepository->store(
                userId   : Auth::id(),
                title    : $request->bookMarkTitle,
                url      : $request->bookMarkUrl,
                timezone : $request->timezone
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
    public function update(BookMarkRequest $request)
    {
        // CSRFトークンを再生成して、二重送信対策
        $request->session()->regenerateToken();

        $isSameUser = $this->bookMarkRepository->isSameUser(
            bookMarkId:$request->bookMarkId,
            userId:Auth::id());

        if (!$isSameUser) {return response('',401);}

        //更新しようとしているurlが自分以外にすでに登録されているか確かめる
        $bookMarkId = $this->bookMarkRepository->serveBookMarkId(
            url:$request->bookMarkUrl,
            userId:Auth::id()
        );

        // 帰り値がnullの場合は無視する(urlを完全に別のものに変更したから,更新するurlがまだ登録されてないから)
        if (!is_null($bookMarkId)&&$request->bookMarkId != $bookMarkId) {
            try {
                if ((substr($request->headers->get("UserLang"), 0,2)) == 'ja'){
                    return response()->json([
                        'messages' => ["bookMarkUrl" => ["そのブックマークは既に保存しています"]],
                        ],
                        400);
                }
            } catch (\Throwable $th) {} // 特に何もすることないから書かなくても良いと思う
            return response()->json([
                'messages' => ["bookMarkUrl" => ["this bookmark is already saved"]],
                ],
                400);
        }

        DB::transaction(function () use($request){
            //ブックマークの更新
            $this->bookMarkRepository->update(
                bookMarkId:$request->bookMarkId,
                timezone  : $request->timezone,
                title :$request->bookMarkTitle,
                url   :$request->bookMarkUrl,
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
        // deleteリクエストならCSRFトークンを再生成して、二重送信対策の部分が必要ない?

        // 消そうとしてるブックマークを登録したユーザーのidと
        // 処理を実行しようとしているユーザーが同じか確かめる
        // ->他の人がかってに他の人のブックマークを消せないようにするため

        $isSameUser = $this->bookMarkRepository->isSameUser(
            bookMarkId:$bookMarkId,
            userId:Auth::id());

        if (!$isSameUser) {return response('',401);}

        DB::transaction(function () use($bookMarkId){
            $this->bookMarkRepository->delete(bookMarkId:$bookMarkId);
        });
    }

    //ブックマーク検索
    public function search(Request $request)
    {
        $result = $this->bookMarkRepository->search(
            userId:Auth::id(),
            keyword:$request->keyword,
            page:\NullAvoidance::ifnull($request->page,1),
            tagList    :$request->tagList,
            searchTarget:\NullAvoidance::ifnull($request->searchTarget,"title"),
            searchQuantity:\NullAvoidance::ifnull($request->searchQuantity,10),
            sortType:\NullAvoidance::ifnull($request->sortType,"updated_at_desc")
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
            "keyword" => \NullAvoidance::ifnull($request->keyword,""),
            "tagList" => $tagList,
            "searchTarget" => \NullAvoidance::ifnull($request->searchTarget,"title"),
            "searchQuantity" => \NullAvoidance::ifnull($request->searchQuantity,10),
            "sortType" => \NullAvoidance::ifnull($request->sortType,"updated_at_desc"),
        ];

        return Inertia::render('BookMark/SearchBookMark',[
            'result' => $result,
            'old' => $old
        ]);
    }

    public function countup($bookMarkId)
    {
        // 同一人物じゃなかったら数えない(いたずら防止)
        $isSameUser = $this->bookMarkRepository->isSameUser(
            bookMarkId:$bookMarkId,
            userId:Auth::id());

        if (!$isSameUser) {return response('',400);}
        // 削除済みだったら数えない
        if ($this->bookMarkRepository->isDeleted($bookMarkId)) {return response('',400);}


        $this->bookMarkRepository->countUp($bookMarkId);
    }

    // 拡張機能用
    public function serveBookMarkToExtended(Request $request)
    {
        $bookMarkId = $this->bookMarkRepository->serveBookMarkId(
            url   :$request->bookMarkUrl,
            userId:Auth::id(),
        );

        if (is_null($bookMarkId)) {
            return response()->json([
                "result" => "not found"
            ]);
        }

        $bookMark = $this->bookMarkRepository->serve($bookMarkId);

        $bookMarkTagList = $this->bookMarkTagRepository->serveTagsRelatedToBookMark(
            userId:Auth::id(),
            bookMarkId:$bookMarkId
        );

        return response()->json([
            "result"         => "found",
            "bookmark"       => $bookMark,
            "checkedTagList" => \NullAvoidance::ifnull($bookMarkTagList,[])
        ]);
    }

    public function serveBookMarkIdToExtended(Request $request)
    {
        $bookMarkId = $this->bookMarkRepository->serveBookMarkId(
            url   :$request->bookMarkUrl,
            userId:Auth::id(),
        );

        return response()->json([
            "bookMarkId"       => $bookMarkId,
        ]);
    }
}
