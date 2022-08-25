<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\BookMarkTag;
use App\Models\BookMark;
use Auth;

class BookMarkController extends Controller
{
    //新規ブックマーク作成
    public function bookMarkStore(Request $request)
    {
        // CSRFトークンを再生成して、二重送信対策
        $request->session()->regenerateToken();

        //urlがすでに登録されているか確かめる
        $isAllreadyExists =BookMark::isAllreadyExists(Auth::id(),$request->bookMarkUrl);
        if ($isAllreadyExists == true) {
            return response()->json(
                ["message" => "already exists"],
                400
            );
        }

        // 記事を保存して記事のidを取得
        $bookMarkId = BookMark::storeBookMark(
                userId   : Auth::id(),
                title    : $request->bookMarkTitle,
                url      : $request->bookMarkUrl,
        );

        // なんのタグも設定されていない時
        if (empty($request->tagList) == true) {
            BookMarkTag::storeBookMarkTag(
                tagId      : null,
                bookMarkId : $bookMarkId,
            );
        }
        //タグが設定されている時
        else {
            foreach($request->tagList as $tagId){
                BookMarkTag::storeBookMarkTag(
                    tagId      : $tagId,
                    bookMarkId : $bookMarkId,
                );
            }
        }
    }

    //ブックマークの更新
    public function bookMarkUpdate(Request $request)
    {
        // CSRFトークンを再生成して、二重送信対策
        $request->session()->regenerateToken();

        //ブックマークの更新
        BookMark::updateBookMark(
            bookMarkId:$request->bookMarkId,
            title:$request->bookMarkTitle,
            url  :$request->bookMarkUrl
        );

        //タグの更新
        BookMarkTag::updateBookMarkTag(
            bookMarkId     :$request->bookMarkId,
            updatedTagList :$request->tagList,
        );
    }

    //ブックマーク検索
    public function bookMarkSearch(Request $request)
    {
        return BookMark::searchBookMark(
            userId:Auth::id(),
            bookMarkToSearch:$request->bookMarkToSearch,
            currentPage:$request->currentPage,
            tagList    :$request->tagList
        );
    }

    public function bookMarkDelete(Request $request)
    {
        // CSRFトークンを再生成して、二重送信対策
        $request->session()->regenerateToken();

        BookMark::deleteBookMark(bookMarkId:$request->bookMarkId);
    }

    // 編集か新規かを分ける
    // public function DetermineProcessing(Request $request)
    // {
    //     //新規
    //     if ($request->bookmarkId == 0) { $this->bookmarkStore($request); }
    //     // 編集
    //     else {
    //         $this->aricleUpdate($request);
    //     }
    // }
}
