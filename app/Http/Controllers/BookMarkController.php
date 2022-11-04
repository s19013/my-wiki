<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\BookMarkTag;
use App\Models\BookMark;
use Auth;

use App\Repository\BookMarkRepository;
use App\Repository\BookMarkTagRepository;

class BookMarkController extends Controller
{
    private $bookMarkRepository;
    private $bookMarkTagRepository;

    public function __construct(BookMarkRepository $bookMarkRepository,BookMarkTagRepository $bookMarkTagRepository)
    {
        $this->bookMarkRepository    = $bookMarkRepository;
        $this->bookMarkTagRepository = $bookMarkTagRepository;
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
    }

    //ブックマークの更新
    public function update(Request $request)
    {
        // CSRFトークンを再生成して、二重送信対策
        $request->session()->regenerateToken();

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
    }

    //ブックマーク検索
    public function search(Request $request)
    {
        $result = $this->bookMarkRepository->search(
            userId:Auth::id(),
            bookMarkToSearch:$request->bookMarkToSearch,
            currentPage:$request->currentPage,
            tagList    :$request->tagList,
            searchTarget:$request->searchTarget
        );

        return response()->json($result,200);
    }

    public function delete($bookMarkId)
    {
        // CSRFトークンを再生成して、二重送信対策
        // deleteリクエストならここの部分が必要ない?
        // //$request->session()->regenerateToken();

        $this->bookMarkRepository->delete(bookMarkId:$bookMarkId);
    }
}
