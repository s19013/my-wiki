<?php

namespace Tests\Unit\model;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use App\Models\Article;
use App\Models\User;

// データベース関係で使う
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

// use Illuminate\Foundation\Testing\WithoutMiddleware;
// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\DatabaseTransactions;

use Carbon\Carbon;

class ArticleModelTest extends TestCase
{
    // テストしたらリセットする
    use RefreshDatabase;
    // use DatabaseMigrations;

    private $articleModel;
    private $userId;

    public function setup():void
    {
        parent::setUp();
        $this->articleModel = new Article();

        // ユーザーを用意
        $user = User::create([
            'name'     => "tester",
            'email'    => "hideya670@gmail.com",
            'password' => "gemini0522",
        ]);

        $this->userId = $user->id;
        // echo $this->userId;

        // ob_flush();
        // flush();
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    // public function test_example()
    // {
    //     $this->assertTrue(true);
    // }

    public function test_storeArticle(): void
    {
        // データを登録
        $this->articleModel->storeArticle("testTitle","testBody",$this->userId);

        // 登録したデータがあるか確認
        $this->assertDatabaseHas('articles',[
            'title' => 'testTitle',
            'body'  => 'testBody'
        ]);
    }

    // このテストはCarbon::now()をつかうので時間帯によってはエラーがでるかもしれない
    public function test_storeArticle_タイトルを入力しなかった場合()
    {
        //これで､Carbon::now()で呼び出される時間を固定化できるらしい
        Carbon::setTestNow(Carbon::now());

        $this->articleModel->storeArticle('',"testBody",$this->userId);
        $this->assertDatabaseHas('articles',[
            'title' => Carbon::now(),
            'body'  => 'testBody'
        ]);
    }

    public function test_記事データを登録した時のidを取ってこれるか(): void
    {
        $returnedId = $this->articleModel->storeArticle("testTitle","testBody",$this->userId);

        $this->assertDatabaseHas('articles',[
            'id'    => $returnedId,
            'title' => 'testTitle',
            'body'  => 'testBody'
        ]);
    }

    public function test_updateArticle()
    {
        $returnedId = $this->articleModel->storeArticle("testTitle","testBody",$this->userId);
        $this->articleModel->updateArticle($returnedId,"updatedTitle","updatedBody");

        $this->assertDatabaseHas('articles',[
            'title' => 'updatedTitle',
            'body'  => 'updatedBody'
        ]);
    }

    public function test_記事論理削除()
    {
        Carbon::setTestNow(Carbon::now());

        $returnedId = $this->articleModel->storeArticle("testTitle","testBody",$this->userId);
        $this->articleModel->deleteArticle($returnedId);

        $this->assertDatabaseHas('articles',[
            'id' => $returnedId,
            'deleted_at'  => Carbon::now()
        ]);
    }

    public function test_serveArticle(Type $var = null)
    {
        $returnedId = $this->articleModel->storeArticle("serveTitle","serveBody",$this->userId);
        $article = $this->articleModel->serveArticle($returnedId);

        //id
        $this->assertSame($returnedId,$article->id);
        //title
        $this->assertSame("serveTitle",$article->title);
        //body
        $this->assertSame("serveBody" ,$article->body);
    }

    public function test_checkArticleDeleted_削除済み()
    {
        $returnedId = $this->articleModel->storeArticle("testTitle","testBody",$this->userId);
        // 削除する
        $this->articleModel->deleteArticle($returnedId);

        $this->assertTrue($this->articleModel->checkArticleDeleted($returnedId));
    }

    public function test_checkArticleDeleted_削除してない()
    {
        $returnedId = $this->articleModel->storeArticle("testTitle","testBody",$this->userId);

        $this->assertFalse($this->articleModel->checkArticleDeleted($returnedId));
    }

    public function test_preventPeep_同一人物()
    {
        $returnedId = $this->articleModel->storeArticle("testTitle","testBody",$this->userId);
        $this->assertTrue($this->articleModel->preventPeep($returnedId,$this->userId));
    }

    public function test_preventPeep_不正()
    {
        $returnedId = $this->articleModel->storeArticle("testTitle","testBody",$this->userId);
        $this->assertFalse($this->articleModel->preventPeep($returnedId,$this->userId + 100));
    }

}
