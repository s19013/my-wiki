<?php

namespace Tests\Unit\Repository;

use Tests\TestCase;
use App\Models\Article;
use App\Models\ArticleTag;
use App\Models\Tag;
use App\Models\User;

use App\Repository\ArticleRepository;

// データベース関係で使う
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;


use Carbon\Carbon;

class ArticleRepositoryTest extends TestCase
{
    // テストしたらリセットする
    use RefreshDatabase;

    private $articleModel;
    private $articleRepository;
    private $userId;

    public function setup():void
    {
        parent::setUp();
        $this->articleRepository = new ArticleRepository();

        // ユーザーを用意
        $user = User::create([
            'name'     => "tester",
            'email'    => "hideya670@gmail.com",
            'password' => "gemini0522",
        ]);

        $this->userId = $user->id;
    }

    // このテストはこの場所から動かしては行けない
    // 期待
    // 記事データを登録した時にその記事のIDを取ってこれるか
    public function test_store_記事データを登録した時にその記事のidを取ってこれるか(): void
    {
        // 正しく動けば､記事を保存したと同時に記事のIdが帰ってくる
        $returnedId = $this->articleRepository->store("testTitle","testBody",$this->userId);

        // このテスト関数は一番最初に動く(この記事は一番最初に登録されるので) idは必ず[1]が帰ってくるはず
        // なんでって言われてもそれがdbの連番の仕様だからとしか答え切れない

        $this->assertEquals($returnedId,1);
    }

    // 期待
    // データがdbに登録されている
    // * 入力したタイトル
    // * 入力した本文
    //
    // 条件
    // タイトル入力済み
    public function test_store_タイトルを入力(): void
    {
        // データを登録
        $this->articleRepository->store(
            "testTitle_test_storeArticle_タイトルを入力",
            "testBody_test_storeArticle_タイトルを入力" ,
            $this->userId
        );

        // 登録したデータがあるか確認
        $this->assertDatabaseHas('articles',[
            'title' => "testTitle_test_storeArticle_タイトルを入力",
            'body'  => "testBody_test_storeArticle_タイトルを入力",
        ]);
    }

    // 期待
    // データがdbに登録されている
    // * タイトルには今の日時が登録されてる
    // * 入力したタイトル
    //
    // 条件
    // タイトルを入力しなかった
    public function test_store_タイトルを入力しなかった()
    {
        //これで､Carbon::now()で呼び出される時間を固定化できるらしい
        Carbon::setTestNow(Carbon::now());

        // データを登録
        $this->articleRepository->store(
            '',
            "testBody_test_storeArticle_タイトルを入力しなかった",
            $this->userId
        );

        $this->assertDatabaseHas('articles',[
            'title' => Carbon::now(),
            'body'  => 'testBody_test_storeArticle_タイトルを入力しなかった'
        ]);
    }

    // 期待
    // 指定したIDの記事が更新されている
    public function test_update_指定したIDの記事が更新されている()
    {
        $article = Article::factory()->create(['user_id' => $this->userId]);

        // データを更新
        $this->articleRepository->update($article->id,"updatedTitle","updatedBody");

        $this->assertDatabaseHas('articles',[
            'id'    => $article->id,
            'title' => 'updatedTitle',
            'body'  => 'updatedBody'
        ]);
    }

    // 期待
    // 指定した記事が論理削除されている
    public function test_deleteArticle_指定した記事が論理削除されている()
    {
        Carbon::setTestNow(Carbon::now());

        $article = Article::factory()->create(['user_id' => $this->userId]);

        // 更新を削除
        $this->articleRepository->delete($article->id);

        // 論理削除されているか
        $this->assertDatabaseHas('articles',[
            'id' => $article->id,
            'deleted_at'  => Carbon::now()
        ]);
    }

    // 指定した記事をとってこれているか
    public function test_serve_指定した記事をとってこれているか()
    {
        // 記事を登録する
        $newArticle = Article::factory()->create(['user_id' => $this->userId]);

        // 記事を取ってくる
        $receivedArticle = $this->articleRepository->serve($newArticle->id);

        //id
        $this->assertSame($newArticle->id,$receivedArticle->id);
        //title
        $this->assertSame($newArticle->title,$receivedArticle->title);
        //body
        $this->assertSame($newArticle->body,$receivedArticle->body);
    }

    // 期待
    // 関数checkArticleDeletedの帰り値がTrueである
    // 条件
    // 指定した記事が論理削除されていた
    public function test_isDeleted_削除済み()
    {
        $article = Article::factory()->create(['user_id' => $this->userId]);

        // 削除する
        $this->articleRepository->delete($article->id);

        $this->assertTrue($this->articleRepository->isDeleted($article->id));
    }

    // 期待
    // 関数checkArticleDeletedの帰り値がFalseである
    // 条件
    // 指定した記事が論理削除されていない
    public function test_isDeleted_削除してない()
    {
        $article = Article::factory()->create(['user_id' => $this->userId]);

        $this->assertFalse($this->articleRepository->isDeleted($article->id));
    }

    // 期待
    // 関数isSameUserの帰り値がTrueである
    // 条件
    // 指定した記事が指定したユーザーが作った記事であった場合
    public function test_isSameUser_同一人物()
    {
        $article = Article::factory()->create(['user_id' => $this->userId]);

        $this->assertTrue($this->articleRepository->isSameUser($article->id,$this->userId));
    }

    // 期待
    // 関数isSameUserの帰り値がFalseである
    // 条件
    // 指定した記事が指定したユーザー以外が作った記事だった場合
    public function test_isSameUser_不正()
    {
        $article = Article::factory()->create(['user_id' => $this->userId]);

        $this->assertFalse($this->articleRepository->isSameUser($article->id,$this->userId + 100));
    }
}
