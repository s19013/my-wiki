<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Inertia\Inertia;

use App\Models\ArticleTag;
use App\Models\Article;

use Auth;

class ArticleTransitionController extends Controller
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
        $articleTagList = ArticleTag::serveTagsRelatedToArticle(
            userId:Auth::id(),
            articleId:$articleId
        );

        return [
            'article'        => $article,
            'articleTagList' => $articleTagList,
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

}
