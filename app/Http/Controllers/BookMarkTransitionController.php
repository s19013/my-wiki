<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Inertia\Inertia;

use App\Models\BookMark;
use App\Models\BookMarkTag;

use App\Repository\BookMarkRepository;
use App\Repository\BookMarkTagRepository;
use App\Tools\NullAvoidanceToolKit;

use Auth;

class BookMarkTransitionController extends Controller
{
    private $bookMarkRepository;
    private $bookMarkTagRepository;
    private $nullAvoidanceToolKit;

    public function __construct()
    {
        $this->bookMarkRepository    = new BookMarkRepository();
        $this->bookMarkTagRepository = new BookMarkTagRepository();
        $this->nullAvoidanceToolKit  = new NullAvoidanceToolKit();
    }

    //ブックマーク編集画面に遷移する時の処理
    public function transitionToEditBookMark($bookMarkId)
    {
        //削除された記事ならindexに戻す
        $isDeleted = $this->bookMarkRepository->isDeleted(bookMarkId:$bookMarkId);
        if ($isDeleted == true ) { return redirect()->route('SearchBookMark'); }

        // 他人の記事を覗こうとしているならindexに戻す
        $isSamePerson = $this->bookMarkRepository->isSameUser(bookMarkId:$bookMarkId,userId:Auth::id());
        if ($isSamePerson == false) { return redirect()->route('SearchBookMark'); }

        $bookMark = $this->bookMarkRepository->serve(bookMarkId:$bookMarkId);

        $bookMarkTagList = $this->bookMarkTagRepository->serveTagsRelatedToBookMark(
            userId:Auth::id(),
            bookMarkId:$bookMarkId
        );

        return Inertia::render('BookMark/EditBookMark',[
            'originalBookMark'         => $bookMark,
            'originalCheckedTagList'   => $this->nullAvoidanceToolKit->ifnull($bookMarkTagList,[]),
        ]);
    }
}
