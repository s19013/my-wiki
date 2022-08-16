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
    //やはりここで違反処理をまとめておこう
    //返り値booleanでtrueだったらもとの関数からリダイレクトしよう



    //記事閲覧画面に遷移する時の処理
    public function transitionToViewArticle($articleId)
    {
        //削除された記事ならindexに戻す
        $isDeleted = Article::checkArticleDeleted(articleId:$articleId);
        if ($isDeleted == true ) { return redirect()->route('SearchArticle'); }

        // 他人の記事を覗こうとしているならindexに戻す
        $isSamePerson = Article::preventPeep(articleId:$articleId,userId:Auth::id());
        if ($isSamePerson == false) { return redirect()->route('SearchArticle');}


        //記事を取り出す
        $article = Article::serveArticle(articleId:$articleId);

        //記事に紐付けられたタグを取り出す
        $articleTag = ArticleTag::serveTagsRelatedToAricle(
            userId:Auth::id(),
            articleId:$articleId
        );


        return Inertia::render('Article/ViewArticle',[
            'article'        => $article,
            'articleTagList' => $articleTag,
        ]);
    }

    //記事編集画面に遷移する時の処理
    public function transitionToEditArticle($articleId)
    {
        //削除された記事ならindexに戻す
        $isDeleted = Article::checkArticleDeleted(articleId:$articleId);
        if ($isDeleted == true ) { return redirect()->route('SearchArticle'); }

        // 他人の記事を覗こうとしているならindexに戻す
        $isSamePerson = Article::preventPeep(articleId:$articleId,userId:Auth::id());
        if ($isSamePerson == false) { return redirect()->route('SearchArticle'); }

        //記事を取り出す
        $article = Article::serveArticle(articleId:$articleId);

        //記事に紐付けられたタグを取り出す
        $articleTag = ArticleTag::serveTagsRelatedToAricle(
            userId:Auth::id(),
            articleId:$articleId
        );


        return Inertia::render('Article/EditArticle',[
            'originalArticle'     => $article,
            'originalCheckedTagList' => $articleTag,
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
            'originalBookMark'     => $bookMark,
            'originalCheckedTagList'   => $bookMarkTag,
        ]);
    }
}
