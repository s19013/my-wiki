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
        $bookmarkId = BookMark::storeBookMark(
                userId   : Auth::id(),
                title    : $request->bookmarkTitle,
                url     : $request->bookmarkUrl,
        );

        // なんのタグも設定されていない時
        if (empty($request->tagList) == true) {
            BookMarkTag::storeBookMarkTag(
                tagId     : null,
                bookmarkId : $bookmarkId,
            );
        }
        //タグが設定されている時
        else {
            foreach($request->tagList as $tagId){
                BookMarkTag::storeBookMarkTag(
                    tagId     : $tagId,
                    bookmarkId : $bookmarkId,
                );
            }
        }
    }

    public function bookMarkUpdate(Request $request)
    {
        $request->session()->regenerateToken();

        BookMark::updateBookMark(
            bookmarkId:$request->bookmarkId,
            title:$request->bookmarkTitle,
            url :$request->bookmarkUrl
        );
        BookMarkTag::updateAricleTag(
            bookmarkId     :$request->bookmarkId,
            updatedTagList:$request  ->tagList,
        );
    }

    public function bookMarkDelete(Request $request)
    {
        $request->session()->regenerateToken();
        BookMark::deleteBookMark(bookmarkId:$request->bookmarkId);
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
