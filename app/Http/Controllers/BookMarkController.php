<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\BookMarkTag;
use App\Models\BookMark;
use Auth;

class BookMarkController extends Controller
{
    //
    public function bookMarkStore(Request $request)
    {
        // CSRFトークンを再生成して、二重送信対策
        $request->session()->regenerateToken();

        // 記事を保存して記事のidを取得
        $bookMarkId = BookMark::storeBookMark(
                userId   : Auth::id(),
                title    : $request->bookMarkTitle,
                url      : $request->bookMarkUrl,
        );

        // なんのタグも設定されていない時
        if (empty($request->tagList) == true) {
            BookMarkTag::storeBookMarkTag(
                tagId     : null,
                bookMarkId : $bookMarkId,
            );
        }
        //タグが設定されている時
        else {
            foreach($request->tagList as $tagId){
                BookMarkTag::storeBookMarkTag(
                    tagId     : $tagId,
                    bookMarkId : $bookMarkId,
                );
            }
        }
    }

    public function bookMarkUpdate(Request $request)
    {
        $request->session()->regenerateToken();

        BookMark::updateBookMark(
            bookMarkId:$request->bookMarkId,
            title:$request->bookMarkTitle,
            url :$request->bookMarkUrl
        );
        BookMarkTag::updateBookMarkTag(
            bookMarkId     :$request->bookMarkId,
            updatedTagList:$request  ->tagList,
        );
    }

    public function bookMarkSearch(Request $request)
    {
        return BookMark::searchBookMark(
            userId:Auth::id(),
            bookMarkToSearch:$request->bookMarkToSearch,
            currentPage:$request->currentPage
        );
    }

    public function bookMarkDelete(Request $request)
    {
        $request->session()->regenerateToken();
        BookMark::deleteBookMark(bookMarkId:$request->bookMarkId);
    }

    public function serveUserAllBookMark(Request $request)
    {
        // タグと記事は別々?
        return BookMark::serveUserAllBookMark(userId:Auth::id());
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
