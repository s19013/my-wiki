<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Inertia\Inertia;

use App\Models\ArticleTag;
use App\Models\Article;

use App\Repository\ArticleRepository;
use App\Repository\ArticleTagRepository;

use Auth;

class ArticleTransitionController extends Controller
{

    public $articleRepository;
    public $articleTagRepository;

    public function __construct()
    {
        // ここではなぜか引数で実体化できなかった
        // ArticleRepository $articleRepository,ArticleTagRepository $articleTagRepository
        $this->articleRepository = new ArticleRepository();
        $this->articleTagRepository = new ArticleTagRepository();
    }

    // 記事の共通処理をまとめる
    public function commonProcessing($articleId)
    {
        //削除された記事ならExceptionを投げる
        $isDeleted = $this->articleRepository->isDeleted(articleId:$articleId);
        if ($isDeleted == true ) {throw new \Exception("illegal");}

        // 他人の記事を覗こうとしているならExceptionを投げる
        $isSamePerson = $this->articleRepository->preventPeep(articleId:$articleId,userId:Auth::id());
        if ($isSamePerson == false) {throw new \Exception("illegal");}
    }



    //記事閲覧画面に遷移する時の処理
    public function transitionToViewArticle($articleId)
    {

        try {
            $this->commonProcessing($articleId);
        } catch (\Exception $e) {
            //違法行為をしていたら検索画面に強制リダイレクト
            return redirect()->route('SearchArticle');
        }

        return Inertia::render('Article/ViewArticle',[
            'article'        => $this->articleRepository->serve(articleId:$articleId),
            'articleTagList' => $this->articleTagRepository->serveTagsRelatedToArticle(
                userId:Auth::id(),
                articleId:$articleId
            ),
        ]);
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
            'originalArticle'        => $this->articleRepository->serve(articleId:$articleId),
            'originalCheckedTagList' => $this->articleTagRepository->serveTagsRelatedToArticle(
                userId:Auth::id(),
                articleId:$articleId
            )
        ]);
    }

}
