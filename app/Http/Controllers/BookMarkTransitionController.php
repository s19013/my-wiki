<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Inertia\Inertia;

use App\Models\BookMark;
use App\Models\BookMarkTag;

use Auth;

class BookMarkTransitionController extends Controller
{
    //ブックマーク編集画面に遷移する時の処理
    public function transitionToEditBookMark($bookMarkId)
    {
        //削除された記事ならindexに戻す
        $isDeleted = BookMark::checkBookMarkDeleted(bookMarkId:$bookMarkId);
        if ($isDeleted == true ) { return redirect()->route('SearchBookMark'); }

        // 他人の記事を覗こうとしているならindexに戻す
        $isSamePerson = BookMark::preventPeep(bookMarkId:$bookMarkId,userId:Auth::id());
        if ($isSamePerson == false) { return redirect()->route('SearchBookMark'); }

        $bookMark = BookMark::serveBookMark(bookMarkId:$bookMarkId);

        $bookMarkTagList = BookMarkTag::serveTagsRelatedToBookMark(
            userId:Auth::id(),
            bookMarkId:$bookMarkId
        );

        //ブックマークに何もタグが紐付けられなかったらnullを入れる こうしないと後の処理でバグが出る
        if ($bookMarkTagList[0]->getAttributes()['id'] == null) {
            $bookMarkTagList = null;
        }


        return Inertia::render('BookMark/EditBookMark',[
            'originalBookMark'         => $bookMark,
            'originalCheckedTagList'   => $bookMarkTagList,
        ]);
    }
}
