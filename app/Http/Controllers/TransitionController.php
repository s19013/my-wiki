<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use Inertia\Inertia;

use App\Models\Tag;
use App\Models\ArticleTag;
use App\Models\Article;
use App\Models\BookMark;
use App\Models\BookMarkTag;

use Auth;

class TransitionController extends Controller
{
    // 記事の共通処理をまとめる
    public function commonProcessing($articleId)
    {
        //削除された記事ならExceptionを投げる
        $isDeleted = Article::checkArticleDeleted(articleId:$articleId);
        if ($isDeleted == true ) { throw new \Exception("illegal"); }

        // 他人の記事を覗こうとしているならExceptionを投げる
        $isSamePerson = Article::preventPeep(articleId:$articleId,userId:Auth::id());
        if ($isSamePerson == false) { throw new \Exception("illegal"); }

        //記事を取り出す
        $article = Article::serveArticle(articleId:$articleId);

        //記事に紐付けられたタグを取り出す
        $articleTag = ArticleTag::serveTagsRelatedToAricle(
            userId:Auth::id(),
            articleId:$articleId
        );

        return [
            'article'        => $article,
            'articleTagList' => $articleTag,
        ];
    }



    //記事閲覧画面に遷移する時の処理
    public function transitionToViewArticle($articleId)
    {
        try {
            $returnValue = $this->commonProcessing($articleId);
        } catch (\Exception $e) {
            //違法行為をしていたら検索画面に強制リダイレクト
            return redirect()->route('SearchArticle');
        }

        return Inertia::render('Article/ViewArticle',$returnValue);
    }

    //記事編集画面に遷移する時の処理
    public function transitionToEditArticle($articleId)
    {
        try {
            $returnValue = $this->commonProcessing($articleId);
        } catch (\Exception $e) {
            //違法行為をしていたら検索画面に強制リダイレクト
            return redirect()->route('SearchArticle');
        }

        return Inertia::render('Article/EditArticle',[
            'originalArticle'        => $returnValue['article'],
            'originalCheckedTagList' => $returnValue['articleTagList']
        ]);
    }

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

        $bookMarkTag = BookMarkTag::serveTagsRelatedToAricle(
            userId:Auth::id(),
            bookMarkId:$bookMarkId
        );


        return Inertia::render('BookMark/EditBookMark',[
            'originalBookMark'         => $bookMark,
            'originalCheckedTagList'   => $bookMarkTag,
        ]);
    }
}
