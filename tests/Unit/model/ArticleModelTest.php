<?php

namespace Tests\Unit\model;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use App\Models\Article;
use App\Models\ArticleTag;
use App\Models\Tag;
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

    // このテストはこの場所から動かしては行けない
    // 期待
    // 記事データを登録した時にその記事のIDを取ってこれるか
    public function test_storeArticle_記事データを登録した時にその記事のidを取ってこれるか(): void
    {
        // 正しく動けば､記事を保存したと同時に記事のIdが帰ってくる
        $returnedId = $this->articleModel->storeArticle("testTitle","testBody",$this->userId);

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
    public function test_storeArticle_タイトルを入力(): void
    {
        // データを登録
        $this->articleModel->storeArticle(
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
    public function test_storeArticle_タイトルを入力しなかった()
    {
        //これで､Carbon::now()で呼び出される時間を固定化できるらしい
        Carbon::setTestNow(Carbon::now());

        // データを登録
        $this->articleModel->storeArticle(
            '',
            "testBody_test_storeArticle_タイトルを入力しなかった",
            $this->userId
        );

        $this->assertDatabaseHas('articles',[
            'title' => Carbon::now(),
            'body'  => 'testBody_test_storeArticle_タイトルを入力しなかった'
        ]);
    }

    // public function test_storeArticle_記事データを登録した時のidを取ってこれるか(): void
    // {
    //     $returnedId = $this->articleModel->storeArticle("testTitle","testBody",$this->userId);

    //     $this->assertDatabaseHas('articles',[
    //         'id'    => $returnedId,
    //         'title' => 'testTitle',
    //         'body'  => 'testBody'
    //     ]);
    // }

    // 期待
    // 指定したIDの記事が更新されている
    public function test_updateArticle_指定したIDの記事が更新されている()
    {
        $article = Article::factory()->create(['user_id' => $this->userId]);

        // データを更新
        $this->articleModel->updateArticle($article->id,"updatedTitle","updatedBody");

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
        $this->articleModel->deleteArticle($article->id);

        // 論理削除されているか
        $this->assertDatabaseHas('articles',[
            'id' => $article->id,
            'deleted_at'  => Carbon::now()
        ]);
    }

    // 指定した記事をとってこれているか
    public function test_serveArticle_指定した記事をとってこれているか()
    {
        // 記事を登録する
        $newArticle = Article::factory()->create(['user_id' => $this->userId]);

        // 記事を取ってくる
        $receivedArticle = $this->articleModel->serveArticle($newArticle->id);

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
    public function test_checkArticleDeleted_削除済み()
    {
        $article = Article::factory()->create(['user_id' => $this->userId]);

        // 削除する
        $this->articleModel->deleteArticle($article->id);

        $this->assertTrue($this->articleModel->checkArticleDeleted($article->id));
    }

    // 期待
    // 関数checkArticleDeletedの帰り値がFalseである
    // 条件
    // 指定した記事が論理削除されていない
    public function test_checkArticleDeleted_削除してない()
    {
        $article = Article::factory()->create(['user_id' => $this->userId]);

        $this->assertFalse($this->articleModel->checkArticleDeleted($article->id));
    }

    // 期待
    // 関数preventPeepの帰り値がTrueである
    // 条件
    // 指定した記事が指定したユーザーが作った記事であった場合
    public function test_preventPeep_同一人物()
    {
        $article = Article::factory()->create(['user_id' => $this->userId]);

        $this->assertTrue($this->articleModel->preventPeep($article->id,$this->userId));
    }

    // 期待
    // 関数preventPeepの帰り値がFalseである
    // 条件
    // 指定した記事が指定したユーザー以外が作った記事だった場合
    public function test_preventPeep_不正()
    {
        $article = Article::factory()->create(['user_id' => $this->userId]);

        $this->assertFalse($this->articleModel->preventPeep($article->id,$this->userId + 100));
    }

    // 期待
    // 何ページ分あるか数える
    // 一度に取得する件数は10件を超えない
    // 条件
    // すべてのデータの合計数が10以上
    public function test_articleSearch_すべてのデータの合計数が10以上()
    {
        //
        Carbon::setTestNow(Carbon::now());

        //テストユーザーの記事
        Article::factory()->count(25)->create(['user_id' => $this->userId]);

        $response = $this->articleModel->searchArticle(
            userId:$this->userId,
            articleToSearch:'',
            currentPage:1,
            tagList:null,
            searchTarget:'title'
        );

        $articleList = $response['articleList'];
        $pageCount   = $response['pageCount'];

        //帰ってきたarticleListの数を数える(10個以上あっても一度に10個までしか返さない)
        $this->assertCount(10,$articleList);

        // 何ページ分あるか確認
        // 今回は全部で20件ある,1ページ10件までなので,10件 + 10件 + 5件の3ページに分かれる
        $this->assertEquals(3, $pageCount);
    }

    // 期待
    // 何ページ分あるか数える
    // 一度に取得する件数は10件を超えない
    // 条件
    // すべてのデータの合計数が10以下
    public function test_articleSearch_すべてのデータの合計数が10以下()
    {
        //
        Carbon::setTestNow(Carbon::now());

        //テストユーザーの記事
        Article::factory()->count(5)->create(['user_id' => $this->userId]);

        $response = $this->articleModel->searchArticle(
            userId:$this->userId,
            articleToSearch:'',
            currentPage:1,
            tagList:null,
            searchTarget:'title'
        );

        $articleList = $response['articleList'];
        $pageCount   = $response['pageCount'];

        //帰ってきたarticleListの数を数える
        $this->assertCount(5,$articleList);

        // 何ページ分あるか確認
        $this->assertEquals(1, $pageCount);
    }

    // 期待
    // * 指定したユーザーの記事だけを取ってくる
    // * 削除した記事は取ってこない
    // 条件
    // * タグ指定なし
    // * タイトル検索
    // * キーワードなし
    public function test_articleSearch_タグ指定なし_タイトル検索_キーワードなし()
    {
        //
        Carbon::setTestNow(Carbon::now());

        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーの記事
        $articles = Article::factory()->count(20)->create(['user_id' => $this->userId]);

        //わざと記事を消す
        Article::where('id','=',$articles[0]->id)
        ->update(['deleted_at' => Carbon::now()]);
        Article::where('id','=',$articles[1]->id)
        ->update(['deleted_at' => Carbon::now()]);

        // ダミーの記事
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        $response = $this->articleModel->searchArticle(
            userId:$this->userId,
            articleToSearch:'',
            currentPage:1,
            tagList:null,
            searchTarget:'title'
        );

        $articleList = $response['articleList'];

        $idList = [];
        foreach ($articleList as $data){ array_push($idList,$data->id); }

        // 全部指定したユーザーの記事か
        foreach ($articleList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除した記事は含んでないか
        $this->assertNotContains($articles[0]->id,$idList);
        $this->assertNotContains($articles[1]->id,$idList);
    }

    // 期待
    //  * titelカラムに"apple"と"make"の文字列が入っているデータをとってくる
    //  * 指定したユーザーの記事だけを取ってくる
    //  * 削除した記事は取ってこない
    // 条件
    // * タグ指定なし
    // * タイトル検索
    // * キーワードあり
    public function test_articleSearch_タグ指定なし_タイトル検索_キーワードあり()
    {
        //
        Carbon::setTestNow(Carbon::now());


        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーの記事
        $articles = Article::factory()->count(10)->create(['user_id' => $this->userId]);

        //検索で引っかかるような記事作成
        $hitArticle1 = Article::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make applePie'
        ]);

        $hitArticle2 = Article::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make appleTea'
        ]);

        // わざと消す記事
        $deleteArticle = Article::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make appleJam'
        ]);

        //わざと記事を消す
        Article::where('id','=',$deleteArticle->id)->update(['deleted_at' => Carbon::now()]);

        // ダミーの記事
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        $response = $this->articleModel->searchArticle(
            userId:$this->userId,
            articleToSearch:"apple make",
            currentPage:1,
            tagList:null,
            searchTarget:'title'
        );

        $articleList = $response['articleList'];

        $idList = [];
        foreach ($articleList as $data){ array_push($idList,$data->id); }


        // 全部指定したユーザーの記事か
        foreach ($articleList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除した記事は含んでないか
        $this->assertNotContains($deleteArticle->id,$idList);

        // ヒットするはずの記事を取ってきているか
        $this->assertContains($hitArticle1->id, $idList);
        $this->assertContains($hitArticle2->id, $idList);

    }

    // 期待
    //  * 指定したタグがついている記事をとってくる
    //  * 指定したユーザーの記事を取ってくる
    //  * 削除した記事は取ってこない
    // 条件
    // * タグ指定:'recipe'
    // * タイトル検索
    // * キーワードなし
    public function test_articleSearch_タグ指定あり_タイトル検索_キーワードなし_()
    {
        //
        Carbon::setTestNow(Carbon::now());

        // 検索でヒットするはずのタグを作る
        $hitTag = Tag::factory()->create([
            'name'    => 'recipe',
            'user_id' => $this->userId
        ]);

        //検索で引っかかるような記事作成
        $hitArticle1 = Article::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make applePie'
        ]);

        ArticleTag::create([
            'article_id' => $hitArticle1->id,
            'tag_id'     => $hitTag->id,
        ]);

        // ダミー
        $hitArticle2 = Article::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make berryPie'
        ]);

        $dammyTag1 = Tag::factory()->create([
            'name'    => 'berry',
            'user_id' => $this->userId
        ]);

        ArticleTag::create([
            'article_id' => $hitArticle2->id,
            'tag_id'     => $dammyTag1->id,
        ]);

        ArticleTag::create([
            'article_id' => $hitArticle2->id,
            'tag_id'     => $hitTag->id,
        ]);

        // わざと消す記事
        $deleteArticle = Article::factory()->create([
            'user_id' => $this->userId,
            'title'   => 'how to make peachPie'
        ]);

        $dammyTag2 = Tag::factory()->create([
            'name'    => 'peach',
            'user_id' => $this->userId
        ]);

        ArticleTag::create([
            'article_id' => $deleteArticle->id,
            'tag_id'     => $dammyTag2->id,
        ]);

        ArticleTag::create([
            'article_id' => $deleteArticle->id,
            'tag_id'     => $hitTag->id,
        ]);

        //わざと記事を消す
        Article::where('id','=',$deleteArticle->id)
        ->update(['deleted_at' => Carbon::now()]);

        $response = $this->articleModel->searchArticle(
            userId:$this->userId,
            articleToSearch:"",
            currentPage:1,
            tagList:[$hitTag->id],
            searchTarget:'title'
        );

        $articleList = $response['articleList'];

        $idList = [];
        foreach ($articleList as $data){ array_push($idList,$data->id); }

        // 全部指定したユーザーの記事か
        foreach ($articleList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除した記事は含んでないか
        $this->assertNotContains($deleteArticle->id,$idList);

        // ヒットするはずの記事を取ってきているか
        $this->assertContains($hitArticle1->id, $idList);
    }

    // 期待
    //  * titelカラムに"make"の文字列が入っているデータをとってくる
    //  * 指定したタグがついている記事をとってくる
    //  * 指定したユーザーの記事だけを取ってくる
    //  * 削除した記事は取ってこない
    // 条件
    // * タグ指定:'recipe'
    // * タイトル検索
    // * キーワードなし
    public function test_articleSearch_タグ指定あり_タイトル検索_キーワードあり()
    {
        //
        Carbon::setTestNow(Carbon::now());


        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーの記事
        $articles = Article::factory()->count(10)->create(['user_id' => $this->userId]);


        //検索で引っかかるような記事作成
        $hitArticle = Article::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make applePie'
        ]);

        $hitTag = Tag::factory()->create([
            'name'    => 'apple',
            'user_id' => $this->userId
        ]);

        // 他のダミーにも付けるタグ
        $tag = Tag::factory()->create([
            'name'    => 'recipe',
            'user_id' => $this->userId
        ]);

        ArticleTag::create([
            'article_id' => $hitArticle->id,
            'tag_id'     => $hitTag->id,
        ]);

        ArticleTag::create([
            'article_id' => $hitArticle->id,
            'tag_id'     => $tag->id,
        ]);

        // ダミー
        $dammyArticle = Article::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make berryPie'
        ]);

        $dammyTag1 = Tag::factory()->create([
            'name'    => 'berry',
            'user_id' => $this->userId
        ]);

        ArticleTag::create([
            'article_id' => $dammyArticle->id,
            'tag_id'     => $dammyTag1->id,
        ]);

        ArticleTag::create([
            'article_id' => $dammyArticle->id,
            'tag_id'     => $tag->id,
        ]);

        // わざと消す記事
        $deleteArticle = Article::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make peachPie'
        ]);

        $dammyTag2 = Tag::factory()->create([
            'name'    => 'berry',
            'user_id' => $this->userId
        ]);

        ArticleTag::create([
            'article_id' => $deleteArticle->id,
            'tag_id'     => $dammyTag2->id,
        ]);

        ArticleTag::create([
            'article_id' => $deleteArticle->id,
            'tag_id'     => $tag->id,
        ]);

        //他のダミー記事
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        Tag::factory()->count(2)->create(['user_id' => $anotherUsers[0]->id]);
        Tag::factory()->count(2)->create(['user_id' => $anotherUsers[0]->id]);


        //わざと記事を消す
        Article::where('id','=',$deleteArticle->id)
        ->update(['deleted_at' => Carbon::now()]);

        $response = $this->articleModel->searchArticle(
            userId:$this->userId,
            articleToSearch:"make",
            currentPage:1,
            tagList:[$hitTag->id],
            searchTarget:'title'
        );

        $articleList = $response['articleList'];

        $idList = [];
        foreach ($articleList as $data){ array_push($idList,$data->id); }

        // 全部指定したユーザーの記事か
        foreach ($articleList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除した記事は含んでないか
        $this->assertNotContains($deleteArticle->id,$idList);

        // ヒットするはずの記事を取ってきているか
        $this->assertContains($hitArticle->id, $idList);
    }

    // 期待
    // * 指定したユーザーの記事だけを取ってくる
    // * 削除した記事は取ってこない
    // 条件
    // * タグ指定なし
    // * 本文検索
    // * キーワードなし
    public function test_articleSearch_タグ指定なし_本文検索_キーワードなし()
    {
        //
        Carbon::setTestNow(Carbon::now());

        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーの記事
        $articles = Article::factory()->count(20)->create(['user_id' => $this->userId]);

        //わざと記事を消す
        Article::where('id','=',$articles[0]->id)
        ->update(['deleted_at' => Carbon::now()]);
        Article::where('id','=',$articles[1]->id)
        ->update(['deleted_at' => Carbon::now()]);

        // ダミーの記事
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        $response = $this->articleModel->searchArticle(
            userId:$this->userId,
            articleToSearch:'',
            currentPage:1,
            tagList:null,
            searchTarget:'body'
        );

        $articleList = $response['articleList'];

        $idList = [];
        foreach ($articleList as $data){ array_push($idList,$data->id); }

        // 全部指定したユーザーの記事か
        foreach ($articleList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除した記事は含んでないか
        $this->assertNotContains($articles[0]->id,$idList);
        $this->assertNotContains($articles[1]->id,$idList);
    }

    // 期待
    //  * titelカラムに"apple"と"make"の文字列が入っているデータをとってくる
    //  * 指定したユーザーの記事だけを取ってくる
    //  * 削除した記事は取ってこない
    // 条件
    // * タグ指定なし
    // * 本文検索
    // * キーワードあり
    public function test_articleSearch_タグ指定なし_本文検索_キーワードあり()
    {
        //
        Carbon::setTestNow(Carbon::now());


        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーの記事
        $articles = Article::factory()->count(10)->create(['user_id' => $this->userId]);

        //検索で引っかかるような記事作成
        $hitArticle1 = Article::factory()->create([
            'user_id' => $this->userId,
            'body' => 'how to make applePie'
        ]);

        $hitArticle2 = Article::factory()->create([
            'user_id' => $this->userId,
            'body' => 'how to make appleTea'
        ]);

        // わざと消す記事
        $deleteArticle = Article::factory()->create([
            'user_id' => $this->userId,
            'body' => 'how to make appleJam'
        ]);

        //わざと記事を消す
        Article::where('id','=',$deleteArticle->id)->update(['deleted_at' => Carbon::now()]);

        // ダミーの記事
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        $response = $this->articleModel->searchArticle(
            userId:$this->userId,
            articleToSearch:"apple make",
            currentPage:1,
            tagList:null,
            searchTarget:'body'
        );

        $articleList = $response['articleList'];

        $idList = [];
        foreach ($articleList as $data){ array_push($idList,$data->id); }


        // 全部指定したユーザーの記事か
        foreach ($articleList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除した記事は含んでないか
        $this->assertNotContains($deleteArticle->id,$idList);

        // ヒットするはずの記事を取ってきているか
        $this->assertContains($hitArticle1->id, $idList);
        $this->assertContains($hitArticle2->id, $idList);

    }

    // 期待
    //  * 指定したタグがついている記事をとってくる
    //  * 指定したユーザーの記事を取ってくる
    //  * 削除した記事は取ってこない
    // 条件
    // * タグ指定:'recipe'
    // * 本文検索
    // * キーワードなし
    public function test_articleSearch_タグ指定あり_本文検索_キーワードなし_()
    {
        //
        Carbon::setTestNow(Carbon::now());

        // 検索でヒットするはずのタグを作る
        $hitTag = Tag::factory()->create([
            'name'    => 'recipe',
            'user_id' => $this->userId
        ]);

        //検索で引っかかるような記事作成
        $hitArticle1 = Article::factory()->create([
            'user_id' => $this->userId,
            'body' => 'how to make applePie'
        ]);

        ArticleTag::create([
            'article_id' => $hitArticle1->id,
            'tag_id'     => $hitTag->id,
        ]);

        // ダミー
        $hitArticle2 = Article::factory()->create([
            'user_id' => $this->userId,
            'body' => 'how to make berryPie'
        ]);

        $dammyTag1 = Tag::factory()->create([
            'name'    => 'berry',
            'user_id' => $this->userId
        ]);

        ArticleTag::create([
            'article_id' => $hitArticle2->id,
            'tag_id'     => $dammyTag1->id,
        ]);

        ArticleTag::create([
            'article_id' => $hitArticle2->id,
            'tag_id'     => $hitTag->id,
        ]);

        // わざと消す記事
        $deleteArticle = Article::factory()->create([
            'user_id' => $this->userId,
            'body'   => 'how to make peachPie'
        ]);

        $dammyTag2 = Tag::factory()->create([
            'name'    => 'peach',
            'user_id' => $this->userId
        ]);

        ArticleTag::create([
            'article_id' => $deleteArticle->id,
            'tag_id'     => $dammyTag2->id,
        ]);

        ArticleTag::create([
            'article_id' => $deleteArticle->id,
            'tag_id'     => $hitTag->id,
        ]);

        //わざと記事を消す
        Article::where('id','=',$deleteArticle->id)
        ->update(['deleted_at' => Carbon::now()]);

        $response = $this->articleModel->searchArticle(
            userId:$this->userId,
            articleToSearch:"",
            currentPage:1,
            tagList:[$hitTag->id],
            searchTarget:'body'
        );

        $articleList = $response['articleList'];

        $idList = [];
        foreach ($articleList as $data){ array_push($idList,$data->id); }

        // 全部指定したユーザーの記事か
        foreach ($articleList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除した記事は含んでないか
        $this->assertNotContains($deleteArticle->id,$idList);

        // ヒットするはずの記事を取ってきているか
        $this->assertContains($hitArticle1->id, $idList);
    }

    // 期待
    //  * titelカラムに"make"の文字列が入っているデータをとってくる
    //  * 指定したタグがついている記事をとってくる
    //  * 指定したユーザーの記事だけを取ってくる
    //  * 削除した記事は取ってこない
    // 条件
    // * タグ指定:'recipe'
    // * 本文検索
    // * キーワードなし
    public function test_articleSearch_タグ指定あり_本文検索_キーワードあり()
    {
        //
        Carbon::setTestNow(Carbon::now());


        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーの記事
        $articles = Article::factory()->count(10)->create(['user_id' => $this->userId]);


        //検索で引っかかるような記事作成
        $hitArticle = Article::factory()->create([
            'user_id' => $this->userId,
            'body' => 'how to make applePie'
        ]);

        $hitTag = Tag::factory()->create([
            'name'    => 'apple',
            'user_id' => $this->userId
        ]);

        // 他のダミーにも付けるタグ
        $tag = Tag::factory()->create([
            'name'    => 'recipe',
            'user_id' => $this->userId
        ]);

        ArticleTag::create([
            'article_id' => $hitArticle->id,
            'tag_id'     => $hitTag->id,
        ]);

        ArticleTag::create([
            'article_id' => $hitArticle->id,
            'tag_id'     => $tag->id,
        ]);

        // ダミー
        $dammyArticle = Article::factory()->create([
            'user_id' => $this->userId,
            'body' => 'how to make berryPie'
        ]);

        $dammyTag1 = Tag::factory()->create([
            'name'    => 'berry',
            'user_id' => $this->userId
        ]);

        ArticleTag::create([
            'article_id' => $dammyArticle->id,
            'tag_id'     => $dammyTag1->id,
        ]);

        ArticleTag::create([
            'article_id' => $dammyArticle->id,
            'tag_id'     => $tag->id,
        ]);

        // わざと消す記事
        $deleteArticle = Article::factory()->create([
            'user_id' => $this->userId,
            'body' => 'how to make peachPie'
        ]);

        $dammyTag2 = Tag::factory()->create([
            'name'    => 'berry',
            'user_id' => $this->userId
        ]);

        ArticleTag::create([
            'article_id' => $deleteArticle->id,
            'tag_id'     => $dammyTag2->id,
        ]);

        ArticleTag::create([
            'article_id' => $deleteArticle->id,
            'tag_id'     => $tag->id,
        ]);

        //他のダミー記事
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        Tag::factory()->count(2)->create(['user_id' => $anotherUsers[0]->id]);
        Tag::factory()->count(2)->create(['user_id' => $anotherUsers[0]->id]);


        //わざと記事を消す
        Article::where('id','=',$deleteArticle->id)
        ->update(['deleted_at' => Carbon::now()]);

        $response = $this->articleModel->searchArticle(
            userId:$this->userId,
            articleToSearch:"make",
            currentPage:1,
            tagList:[$hitTag->id],
            searchTarget:'body'
        );

        $articleList = $response['articleList'];

        $idList = [];
        foreach ($articleList as $data){ array_push($idList,$data->id); }

        // 全部指定したユーザーの記事か
        foreach ($articleList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除した記事は含んでないか
        $this->assertNotContains($deleteArticle->id,$idList);

        // ヒットするはずの記事を取ってきているか
        $this->assertContains($hitArticle->id, $idList);
    }
}
