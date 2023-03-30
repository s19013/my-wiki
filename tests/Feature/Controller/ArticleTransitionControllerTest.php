<?php

namespace Tests\Feature\Controller;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Tag;
use App\Models\Article;
use App\Models\ArticleTag;
use App\Http\Controllers\ArticleTransitionController;

use App\Repository\ArticleTagRepository;

use Illuminate\Foundation\Testing\WithoutMiddleware;

use Carbon\Carbon;

class ArticleTransitionControllerTest extends TestCase
{

    // テストしたらリセットする
    use RefreshDatabase;
    // ミドルウェアの無効化
    // use WithoutMiddleware;

    private $user;
    private $controller;
    private $articleTagRepository;

    public function setup():void
    {
        parent::setUp();
        // ユーザーを用意
        $this->user = User::factory()->create();
        $this->controller = new ArticleTransitionController();
        $this->articleTagRepository = new ArticleTagRepository();
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'ja';
    }

    //自分でもいまいちなテストだと思う(他のテストもだけど)
    public function test_transitionToViewArticle_自分の記事_タグ登録済み()
    {
        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //ダミー
        Article::factory()->count(5)->create(['user_id' => $this->user->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        //今回使うやつ
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        // タグを登録
        $tags = Tag::factory()->count(3)->create(['user_id' => $this->user->id]);

        foreach ($tags as $tag) {
            $this->articleTagRepository->store($tag->id,$article->id);
        }

        $response = $this
        ->actingAs($this->user)
        ->withSession(['XSRF-TOKEN' => 'test','sundlf_session' => 'test'])
        ->get(route('ViewArticle', ['articleId' => $article->id]));

        // ステータス
        $response->assertStatus(200);
    }

    public function test_transitionToViewArticle_自分の記事_タグ未登録()
    {
        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //ダミー
        Article::factory()->count(5)->create(['user_id' => $this->user->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        //今回使うやつ
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        // タグを登録
        $this->articleTagRepository->store(null,$article->id);

        $response = $this
        ->actingAs($this->user)
        ->withSession(['XSRF-TOKEN' => 'test','sundlf_session' => 'test'])
        ->get(route('ViewArticle', ['articleId' => $article->id]));


        // ステータス
        $response->assertStatus(200);
    }

    public function test_transitionToViewArticle_自分の削除した記事()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //ダミー
        Article::factory()->count(5)->create(['user_id' => $this->user->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        //今回使うやつ
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        // タグを登録
        $tags = Tag::factory()->count(3)->create(['user_id' => $this->user->id]);

        foreach ($tags as $tag) {
            $this->articleTagRepository->store($tag->id,$article->id);
        }

        //削除
        Article::where('id','=',$article->id)->update(['deleted_at' => Carbon::now()]);

        $response = $this
        ->actingAs($this->user)
        ->withSession(['XSRF-TOKEN' => 'test','sundlf_session' => 'test'])
        ->get(route('ViewArticle', ['articleId' => $article->id]));

        // ステータス
        $response->assertStatus(302);

        //searchArticleにリダイレクトしたか
        $response->assertRedirect(route('SearchArticle'));
    }

    public function test_transitionToViewArticle_他人の記事()
    {
        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //ダミー
        Article::factory()->count(5)->create(['user_id' => $this->user->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        //今回使うやつ 他人の記事
        $article = Article::factory()->create(['user_id' => $anotherUsers[0]->id]);

        // タグを登録
        $tags = Tag::factory()->count(3)->create(['user_id' => $this->user->id]);

        foreach ($tags as $tag) {
            $this->articleTagRepository->store($tag->id,$article->id);
        }

        $response = $this
        ->actingAs($this->user)
        ->withSession(['XSRF-TOKEN' => 'test','sundlf_session' => 'test'])
        ->get(route('ViewArticle', ['articleId' => $article->id]));

        // ステータス
        $response->assertStatus(302);

        //searchArticleにリダイレクトしたか
        $response->assertRedirect(route('SearchArticle'));
    }

    public function test_transitionToEditArticle_自分の記事()
    {
        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //ダミー
        Article::factory()->count(5)->create(['user_id' => $this->user->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        //今回使うやつ
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        // タグを登録
        $tags = Tag::factory()->count(3)->create(['user_id' => $this->user->id]);

        foreach ($tags as $tag) {
            $this->articleTagRepository->store($tag->id,$article->id);
        }

        $response = $this
        ->actingAs($this->user)
        ->withSession(['XSRF-TOKEN' => 'test','sundlf_session' => 'test'])
        ->get(route('EditArticle', ['articleId' => $article->id]));

        // ステータス
        $response->assertStatus(200);
    }

    public function test_transitionToEditArticle_自分の削除した記事()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //ダミー
        Article::factory()->count(5)->create(['user_id' => $this->user->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        //今回使うやつ
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        // タグを登録
        $tags = Tag::factory()->count(3)->create(['user_id' => $this->user->id]);

        foreach ($tags as $tag) {
            $this->articleTagRepository->store($tag->id,$article->id);
        }

        //削除
        Article::where('id','=',$article->id)->update(['deleted_at' => Carbon::now()]);



        $response = $this
        ->actingAs($this->user)
        ->withSession(['XSRF-TOKEN' => 'test','sundlf_session' => 'test'])
        ->get(route('EditArticle', ['articleId' => $article->id]));

        // ステータス
        $response->assertStatus(302);

        //searchArticleにリダイレクトしたか
        $response->assertRedirect(route('SearchArticle'));
    }

    public function test_transitionToEditArticle_他人の記事()
    {
        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //ダミー
        Article::factory()->count(5)->create(['user_id' => $this->user->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        //今回使うやつ 他人の記事
        $article = Article::factory()->create(['user_id' => $anotherUsers[0]->id]);

        // タグを登録
        $tags = Tag::factory()->count(3)->create(['user_id' => $this->user->id]);

        foreach ($tags as $tag) {
            $this->articleTagRepository->store($tag->id,$article->id);
        }

        $response = $this
        ->actingAs($this->user)
        ->withSession(['XSRF-TOKEN' => 'test','sundlf_session' => 'test'])
        ->get(route('EditArticle', ['articleId' => $article->id]));

        // ステータス
        $response->assertStatus(302);

        //searchArticleにリダイレクトしたか
        $response->assertRedirect(route('SearchArticle'));
    }

    // 不正操作についてはこれより前のテストでテストされているのでここではしない
    // カウントアップ
    public function test_transitionToViewArticle_カウントアップ()
    {
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        $this->articleTagRepository->store(null,$article->id);

        $response = $this
        ->actingAs($this->user)
        ->withSession(['XSRF-TOKEN' => 'test','sundlf_session' => 'test'])
        ->get(route('ViewArticle', ['articleId' => $article->id]));

        // ステータス
        $response->assertStatus(200);

        $this->assertDatabaseHas('articles',[
            'id'    => $article->id,
            'count' => 1,
        ]);

    }

}

