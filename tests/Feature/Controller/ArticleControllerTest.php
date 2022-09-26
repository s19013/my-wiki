<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Http\Request;
use App\Http\Controllers\ArticleController;

use App\Models\Article;
use App\Models\ArticleTag;
use App\Models\Tag;
use App\Models\User;

// データベース関係で使う
use Illuminate\Foundation\Testing\WithoutMiddleware;

use Carbon\Carbon;
use Auth;
use Session;
/**
 * 今は$request->session()->regenerateToken();で騒ぎ立てられるのをどうにかできないので
 * $request->session()->regenerateToken();はすべてコメントアウトしてテストする
 */
class ArticleControllerTest extends TestCase
{
    // テストしたらリセットする
    use RefreshDatabase;
    // ミドルウェアの無効化
    use WithoutMiddleware;

    private $user;

    public function setup():void
    {
        parent::setUp();
        // ユーザーを用意
        $this->user = User::factory()->create();
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    // public function test_example()
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    public function test_articleStore_タグあり_タイトルあり()
    {
        $tags = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);

        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->post('/api/article/store/',[
            'articleTitle' => "testTitletest_articleStore_タグあり_タイトルあり",
            'articleBody'  => "testBodytest_articleStore_タグあり_タイトルあり" ,
            'tagList'      => [$tags[0]->id,$tags[1]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // 記事
        $this->assertDatabaseHas('articles',[
            'user_id'=> $this->user->id,
            'title' => "testTitletest_articleStore_タグあり_タイトルあり",
            'body'  => "testBodytest_articleStore_タグあり_タイトルあり",
            'deleted_at' => null,
        ]);

        $article = Article::select('id')
        ->where('user_id', '=' ,$this->user->id)
        ->where('title','=',"testTitletest_articleStore_タグあり_タイトルあり")
        ->where('body' ,'=',"testBodytest_articleStore_タグあり_タイトルあり" )
        ->first();

        $articleId = $article->id;

        //タグ
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $articleId,
            'tag_id'     => $tags[0]->id,
            'deleted_at' => null,
        ]);

        $this->assertDatabaseHas('article_tags',[
            'article_id' => $articleId,
            'tag_id'     => $tags[1]->id,
            'deleted_at' => null,
        ]);

    }

    public function test_articleStore_タグあり_タイトルなし()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        $tags = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);

        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->post('/api/article/store/',[
            'articleTitle' => "",
            'articleBody'  => "testBodytest_articleStore_タグあり_タイトルなし" ,
            'tagList'      => [$tags[0]->id,$tags[1]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // 記事
        $this->assertDatabaseHas('articles',[
            'user_id'=> $this->user->id,
            'title'  => Carbon::now(),
            'body'   => "testBodytest_articleStore_タグあり_タイトルなし",
            'deleted_at' => null,
        ]);

        $article = Article::select('id')
        ->where('user_id', '=' ,$this->user->id)
        ->where('title','=',Carbon::now())
        ->where('body' ,'=',"testBodytest_articleStore_タグあり_タイトルなし" )
        ->first();

        $articleId = $article->id;

        //タグ
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $articleId,
            'tag_id'     => $tags[0]->id,
            'deleted_at' => null,
        ]);

        $this->assertDatabaseHas('article_tags',[
            'article_id' => $articleId,
            'tag_id'     => $tags[1]->id,
            'deleted_at' => null,
        ]);

    }

    public function test_articleStore_タグなし_タイトルあり()
    {
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->post('/api/article/store/',[
            'articleTitle' => "testTitletest_articleStore_タグなし_タイトルあり",
            'articleBody'  => "testBodytest_articleStore_タグなし_タイトルあり" ,
            'tagList'      => null,
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // 記事
        $this->assertDatabaseHas('articles',[
            'user_id'=> $this->user->id,
            'title'  => "testTitletest_articleStore_タグなし_タイトルあり",
            'body'   => "testBodytest_articleStore_タグなし_タイトルあり",
            'deleted_at' => null,
        ]);

        $article = Article::select('id')
        ->where('user_id', '=' ,$this->user->id)
        ->where('title','=',"testTitletest_articleStore_タグなし_タイトルあり")
        ->where('body' ,'=',"testBodytest_articleStore_タグなし_タイトルあり" )
        ->first();

        $articleId = $article->id;

        //タグ
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $articleId,
            'tag_id'     => null,
            'deleted_at' => null,
        ]);
    }

    public function test_articleStore_タグなし_タイトルなし()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->post('/api/article/store/',[
            'articleTitle' => "",
            'articleBody'  => "testBodytest_articleStore_タグなし_タイトルなし" ,
            'tagList'      => null,
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // 記事
        $this->assertDatabaseHas('articles',[
            'user_id'=> $this->user->id,
            'title'  => Carbon::now(),
            'body'   => "testBodytest_articleStore_タグなし_タイトルなし",
            'deleted_at' => null,
        ]);

        $article = Article::select('id')
        ->where('user_id', '=' ,$this->user->id)
        ->where('title','=',Carbon::now())
        ->where('body' ,'=',"testBodytest_articleStore_タグなし_タイトルなし" )
        ->first();

        $articleId = $article->id;

        //タグ
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $articleId,
            'tag_id'     => null,
            'deleted_at' => null,
        ]);
    }

    public function test_articleUpdate_タグ総入れ替え()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        // 記事などを作成
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);
        $newTags = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);

        ArticleTag::create([
            "article_id" => $article->id,
            "tag_id"     => $tags[0]->id
        ]);

        ArticleTag::create([
            "article_id" => $article->id,
            "tag_id"     => $tags[1]->id
        ]);

        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->post('/api/article/update/',[
            'articleId'     => $article->id,
            'articleTitle'  => "更新titleタグ総入れ替え",
            'articleBody'   => "更新bodyタグ総入れ替え" ,
            'tagList' => [$newTags[0]->id,$newTags[1]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // 記事
        $this->assertDatabaseHas('articles',[
            'user_id'=> $this->user->id,
            'title'  => "更新titleタグ総入れ替え",
            'body'   => "更新bodyタグ総入れ替え",
            'deleted_at' => null,
        ]);

        //タグ
        //新しく追加
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $newTags[0]->id,
            'deleted_at' => null,
        ]);

        $this->assertDatabaseHas('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $newTags[1]->id,
            'deleted_at' => null,
        ]);

        //削除したタグ
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $tags[0]->id,
            'deleted_at' => Carbon::now(),
        ]);

        $this->assertDatabaseHas('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $tags[1]->id,
            'deleted_at' => Carbon::now(),
        ]);
    }

    public function test_articleUpdate_元のタグをそのままに新しく追加()
    {
        // 記事などを作成
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);
        $newTags = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);

        ArticleTag::create([
            "article_id" => $article->id,
            "tag_id"     => $tags[0]->id
        ]);

        ArticleTag::create([
            "article_id" => $article->id,
            "tag_id"     => $tags[1]->id
        ]);

        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->post('/api/article/update/',[
            'articleId'     => $article->id,
            'articleTitle'  => "更新title元のタグをそのままに新しく追加",
            'articleBody'   => "更新body元のタグをそのままに新しく追加" ,
            'tagList' => [$tags[0]->id,$tags[1]->id,$newTags[0]->id,$newTags[1]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // 記事
        $this->assertDatabaseHas('articles',[
            'user_id'=> $this->user->id,
            'title'  => "更新title元のタグをそのままに新しく追加",
            'body'   => "更新body元のタグをそのままに新しく追加",
            'deleted_at' => null,
        ]);

        //タグ
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $newTags[0]->id,
            'deleted_at' => null,
        ]);

        $this->assertDatabaseHas('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $newTags[1]->id,
            'deleted_at' => null,
        ]);
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $tags[0]->id,
            'deleted_at' => null,
        ]);

        $this->assertDatabaseHas('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $tags[1]->id,
            'deleted_at' => null,
        ]);
    }

    public function test_articleUpdate_タグの一部を消す()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        // 記事などを作成
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(4)->create(['user_id' => $this->user->id]);

        ArticleTag::create([
            "article_id" => $article->id,
            "tag_id"     => $tags[0]->id
        ]);

        ArticleTag::create([
            "article_id" => $article->id,
            "tag_id"     => $tags[1]->id
        ]);

        ArticleTag::create([
            "article_id" => $article->id,
            "tag_id"     => $tags[2]->id
        ]);

        ArticleTag::create([
            "article_id" => $article->id,
            "tag_id"     => $tags[3]->id
        ]);

        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->post('/api/article/update/',[
            'articleId'     => $article->id,
            'articleTitle'  => "更新titleタグ総入れ替え",
            'articleBody'   => "更新bodyタグ総入れ替え" ,
            'tagList' => [$tags[2]->id,$tags[3]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // 記事
        $this->assertDatabaseHas('articles',[
            'user_id'=> $this->user->id,
            'title'  => "更新titleタグ総入れ替え",
            'body'   => "更新bodyタグ総入れ替え",
            'deleted_at' => null,
        ]);

        //タグ
        //残したタグ
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $tags[2]->id,
            'deleted_at' => null,
        ]);

        $this->assertDatabaseHas('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $tags[3]->id,
            'deleted_at' => null,
        ]);

        //削除したタグ
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $tags[0]->id,
            'deleted_at' => Carbon::now(),
        ]);

        $this->assertDatabaseHas('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $tags[1]->id,
            'deleted_at' => Carbon::now(),
        ]);
    }

    public function test_articleUpdate_タグがついてなかった記事にタグを付ける()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        // 記事などを作成
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);

        ArticleTag::create([
            "article_id" => $article->id,
            "tag_id"     => null
        ]);

        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->post('/api/article/update/',[
            'articleId'     => $article->id,
            'articleTitle'  => "更新titleタグがついてなかった記事にタグを付ける",
            'articleBody'   => "更新bodyタグがついてなかった記事にタグを付ける" ,
            'tagList' => [$tags[0]->id,$tags[1]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // 記事
        $this->assertDatabaseHas('articles',[
            'user_id'=> $this->user->id,
            'title'  => "更新titleタグがついてなかった記事にタグを付ける",
            'body'   => "更新bodyタグがついてなかった記事にタグを付ける",
            'deleted_at' => null,
        ]);

        //タグ
        //追加したタグ
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $tags[0]->id,
            'deleted_at' => null,
        ]);

        $this->assertDatabaseHas('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $tags[1]->id,
            'deleted_at' => null,
        ]);

        //削除したタグ
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => null,
            'deleted_at' => Carbon::now(),
        ]);
    }

    public function test_articleDelete()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        // 記事などを作成
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->delete(route('api.article.delete', ['articleId' => $article->id]));

        // ステータス
        $response->assertStatus(200);

        //消えたことを確認
        $this->assertDatabaseHas('articles',[
            'id' => $article->id,
            'user_id'    => $this->user->id,
            'deleted_at' => Carbon::now(),
        ]);
    }

    public function test_articleSearch_タグ指定なし_タイトル検索_キーワードなし_指定したユーザーの記事だけを取ってくる_削除した記事とって来ない()
    {
        //
        Carbon::setTestNow(Carbon::now());


        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーの記事
        $articles = Article::factory()->count(20)->create(['user_id' => $this->user->id]);

        Article::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        //わざと記事を消す
        Article::where('id','=',$articles[0]->id)
        ->update(['deleted_at' => Carbon::now()]);
        Article::where('id','=',$articles[1]->id)
        ->update(['deleted_at' => Carbon::now()]);


        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->post('/api/article/search/',[
            'userId' => $this->user->id,
            'articleToSearch' =>"",
            'currentPage'  =>1,
            'tagList'      => null,
            'searchTarget' =>"title"
        ]);

        // print_r($response->json()['articleList']);

        // ステータス
        $response->assertStatus(200);

        //帰ってきたarticleListの数を数える(10個以上あっても一度に10個までしか返さない)
        $response->assertJsonCount(10,$key='articleList');

        // 何ページ分あるか確認
        $this->assertEquals(2, $response->json()['pageCount']);

        // 削除した記事が含まれていないか
        foreach ($response->json()['articleList'] as $data){
            $this->assertNotEquals($data['id'],$articles[0]->id);
        }
        foreach ($response->json()['articleList'] as $data){
            $this->assertNotEquals($data['id'],$articles[1]->id);
        }

        // 全部指定したユーザーの記事か
        foreach ($response->json()['articleList'] as $data){
            $this->assertEquals($data['user_id'],$this->user->id);
        }
    }

    public function test_articleSearch_タグ指定なし_タイトル検索_指定したユーザーの記事だけを取ってくる_削除した記事とって来ない()
    {
        //
        Carbon::setTestNow(Carbon::now());


        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーの記事
        $articles = Article::factory()->count(10)->create(['user_id' => $this->user->id]);

        //検索で引っかかるような記事作成
        $hitArticle1 = Article::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'how to make applePie'
        ]);

        $hitArticle2 = Article::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'how to make appleTea'
        ]);

        // わざと消す記事
        $deleteArticle = Article::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'how to make appleJam'
        ]);

        Article::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        //わざと記事を消す
        Article::where('id','=',$deleteArticle->id)
        ->update(['deleted_at' => Carbon::now()]);


        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->post('/api/article/search/',[
            'userId' => $this->user->id,
            'articleToSearch' =>"apple make",
            'currentPage'  =>1,
            'tagList'      => null,
            'searchTarget' =>"title"
        ]);

        // print_r($response->json()['articleList']);

        // ステータス
        $response->assertStatus(200);

        // 削除した記事が含まれていないか
        foreach ($response->json()['articleList'] as $data){
            $this->assertNotEquals($data['id'],$deleteArticle->id);
        }

        // 全部指定したユーザーの記事か
        foreach ($response->json()['articleList'] as $data){
            $this->assertEquals($data['user_id'],$this->user->id);
        }

        $temp = [];
        foreach ($response->json()['articleList'] as $data){
            array_push( $temp , $data['id'] );
        }

        // ヒットするはずの記事を取ってきているか
        $this->assertContains($hitArticle1->id, $temp);
        $this->assertContains($hitArticle2->id, $temp);

    }

    public function test_articleSearch_タグ指定あり_タイトル検索_キーワードなし_指定したユーザーの記事だけを取ってくる_削除した記事とって来ない()
    {
        //
        Carbon::setTestNow(Carbon::now());


        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーの記事
        $articles = Article::factory()->count(10)->create(['user_id' => $this->user->id]);


        //検索で引っかかるような記事作成
        $hitArticle1 = Article::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'how to make applePie'
        ]);

        // 他のダミーにも付けるタグ
        $hitTag = Tag::factory()->create([
            'name'    => 'recipe',
            'user_id' => $this->user->id
        ]);

        ArticleTag::create([
            'article_id' => $hitArticle1->id,
            'tag_id'     => $hitTag->id,
        ]);

        // ダミー
        $hitArticle2 = Article::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'how to make berryPie'
        ]);

        $dammyTag1 = Tag::factory()->create([
            'name'    => 'berry',
            'user_id' => $this->user->id
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
            'user_id' => $this->user->id,
            'title' => 'how to make peachPie'
        ]);

        $dammyTag2 = Tag::factory()->create([
            'name'    => 'berry',
            'user_id' => $this->user->id
        ]);

        ArticleTag::create([
            'article_id' => $deleteArticle->id,
            'tag_id'     => $dammyTag2->id,
        ]);

        ArticleTag::create([
            'article_id' => $deleteArticle->id,
            'tag_id'     => $hitTag->id,
        ]);

        //他のダミー記事
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        Tag::factory()->count(2)->create(['user_id' => $anotherUsers[0]->id]);
        Tag::factory()->count(2)->create(['user_id' => $anotherUsers[0]->id]);


        //わざと記事を消す
        Article::where('id','=',$deleteArticle->id)
        ->update(['deleted_at' => Carbon::now()]);


        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->post('/api/article/search/',[
            'userId' => $this->user->id,
            'articleToSearch' =>"",
            'currentPage'  =>1,
            'tagList'      => [$hitTag->id],
            'searchTarget' =>"title"
        ]);

        // print_r($response->json()['articleList']);

        // ステータス
        $response->assertStatus(200);

        // 削除した記事が含まれていないか
        foreach ($response->json()['articleList'] as $data){
            $this->assertNotEquals($data['id'],$deleteArticle->id);
        }

        // 全部指定したユーザーの記事か
        foreach ($response->json()['articleList'] as $data){
            $this->assertEquals($data['user_id'],$this->user->id);
        }

        $temp = [];
        foreach ($response->json()['articleList'] as $data){
            array_push( $temp , $data['id'] );
        }

        // ヒットするはずの記事を取ってきているか
        $this->assertContains($hitArticle1->id, $temp);
        $this->assertContains($hitArticle2->id, $temp);
    }

    public function test_articleSearch_タグ指定あり_タイトル検索_指定したユーザーの記事だけを取ってくる_削除した記事とって来ない()
    {
        //
        Carbon::setTestNow(Carbon::now());


        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーの記事
        $articles = Article::factory()->count(10)->create(['user_id' => $this->user->id]);


        //検索で引っかかるような記事作成
        $hitArticle = Article::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'how to make applePie'
        ]);

        $hitTag = Tag::factory()->create([
            'name'    => 'apple',
            'user_id' => $this->user->id
        ]);

        // 他のダミーにも付けるタグ
        $tag = Tag::factory()->create([
            'name'    => 'recipe',
            'user_id' => $this->user->id
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
            'user_id' => $this->user->id,
            'title' => 'how to make berryPie'
        ]);

        $dammyTag1 = Tag::factory()->create([
            'name'    => 'berry',
            'user_id' => $this->user->id
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
            'user_id' => $this->user->id,
            'title' => 'how to make peachPie'
        ]);

        $dammyTag2 = Tag::factory()->create([
            'name'    => 'berry',
            'user_id' => $this->user->id
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


        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->post('/api/article/search/',[
            'userId' => $this->user->id,
            'articleToSearch' =>"make",
            'currentPage'  =>1,
            'tagList'      => [$hitTag->id],
            'searchTarget' =>"title"
        ]);

        // print_r($response->json()['articleList']);

        // ステータス
        $response->assertStatus(200);

        // 削除した記事が含まれていないか
        foreach ($response->json()['articleList'] as $data){
            $this->assertNotEquals($data['id'],$deleteArticle->id);
        }

        // 全部指定したユーザーの記事か
        foreach ($response->json()['articleList'] as $data){
            $this->assertEquals($data['user_id'],$this->user->id);
        }

        $temp = [];
        foreach ($response->json()['articleList'] as $data){
            array_push( $temp , $data['id'] );
        }

        // ヒットするはずの記事を取ってきているか
        $this->assertContains($hitArticle->id, $temp);
    }

    public function test_articleSearch_タグ指定なし_本文検索_キーワードなし_指定したユーザーの記事だけを取ってくる_削除した記事とって来ない()
    {
        //
        Carbon::setTestNow(Carbon::now());


        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーの記事
        $articles = Article::factory()->count(20)->create(['user_id' => $this->user->id]);

        Article::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        //わざと記事を消す
        Article::where('id','=',$articles[0]->id)
        ->update(['deleted_at' => Carbon::now()]);
        Article::where('id','=',$articles[1]->id)
        ->update(['deleted_at' => Carbon::now()]);


        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->post('/api/article/search/',[
            'userId' => $this->user->id,
            'articleToSearch' =>"",
            'currentPage'  =>1,
            'tagList'      => null,
            'searchTarget' =>"body"
        ]);

        // print_r($response->json()['articleList']);

        // ステータス
        $response->assertStatus(200);

        //帰ってきたarticleListの数を数える(10個以上あっても一度に10個までしか返さない)
        $response->assertJsonCount(10,$key='articleList');

        // 何ページ分あるか確認
        $this->assertEquals(2, $response->json()['pageCount']);

        // 削除した記事が含まれていないか
        foreach ($response->json()['articleList'] as $data){
            $this->assertNotEquals($data['id'],$articles[0]->id);
        }
        foreach ($response->json()['articleList'] as $data){
            $this->assertNotEquals($data['id'],$articles[1]->id);
        }

        // 全部指定したユーザーの記事か
        foreach ($response->json()['articleList'] as $data){
            $this->assertEquals($data['user_id'],$this->user->id);
        }
    }

    public function test_articleSearch_タグ指定なし_本文検索_指定したユーザーの記事だけを取ってくる_削除した記事とって来ない()
    {
        //
        Carbon::setTestNow(Carbon::now());


        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーの記事
        $articles = Article::factory()->count(1)->create(['user_id' => $this->user->id]);

        //検索で引っかかるような記事作成
        $hitArticle1 = Article::factory()->create([
            'user_id' => $this->user->id,
            'body' => 'how to make applePie'
        ]);

        $hitArticle2 = Article::factory()->create([
            'user_id' => $this->user->id,
            'body' => 'how to make appleTea'
        ]);

        // わざと消す記事
        $deleteArticle = Article::factory()->create([
            'user_id' => $this->user->id,
            'body' => 'how to make appleJam'
        ]);

        Article::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        //わざと記事を消す
        Article::where('id','=',$deleteArticle->id)
        ->update(['deleted_at' => Carbon::now()]);


        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->post('/api/article/search/',[
            'userId' => $this->user->id,
            'articleToSearch' =>"apple make",
            'currentPage'  =>1,
            'tagList'      => null,
            'searchTarget' =>"body"
        ]);

        // print_r($response->json()['articleList']);

        // ステータス
        $response->assertStatus(200);

        // 削除した記事が含まれていないか
        foreach ($response->json()['articleList'] as $data){
            $this->assertNotEquals($data['id'],$deleteArticle->id);
        }

        // 全部指定したユーザーの記事か
        foreach ($response->json()['articleList'] as $data){
            $this->assertEquals($data['user_id'],$this->user->id);
        }

        $temp = [];
        foreach ($response->json()['articleList'] as $data){
            array_push( $temp , $data['id'] );
        }

        // ヒットするはずの記事を取ってきているか
        $this->assertContains($hitArticle1->id, $temp);
        $this->assertContains($hitArticle2->id, $temp);

    }

    public function test_articleSearch_タグ指定あり_本文検索_キーワードなし_指定したユーザーの記事だけを取ってくる_削除した記事とって来ない()
    {
        //
        Carbon::setTestNow(Carbon::now());


        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーの記事
        $articles = Article::factory()->count(10)->create(['user_id' => $this->user->id]);


        //検索で引っかかるような記事作成
        $hitArticle1 = Article::factory()->create([
            'user_id' => $this->user->id,
            'body' => 'how to make applePie'
        ]);

        // 他のダミーにも付けるタグ
        $hitTag = Tag::factory()->create([
            'name'    => 'recipe',
            'user_id' => $this->user->id
        ]);

        ArticleTag::create([
            'article_id' => $hitArticle1->id,
            'tag_id'     => $hitTag->id,
        ]);

        // ダミー
        $hitArticle2 = Article::factory()->create([
            'user_id' => $this->user->id,
            'body' => 'how to make berryPie'
        ]);

        $dammyTag1 = Tag::factory()->create([
            'name'    => 'berry',
            'user_id' => $this->user->id
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
            'user_id' => $this->user->id,
            'body' => 'how to make peachPie'
        ]);

        $dammyTag2 = Tag::factory()->create([
            'name'    => 'berry',
            'user_id' => $this->user->id
        ]);

        ArticleTag::create([
            'article_id' => $deleteArticle->id,
            'tag_id'     => $dammyTag2->id,
        ]);

        ArticleTag::create([
            'article_id' => $deleteArticle->id,
            'tag_id'     => $hitTag->id,
        ]);

        //他のダミー記事
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        Tag::factory()->count(2)->create(['user_id' => $anotherUsers[0]->id]);
        Tag::factory()->count(2)->create(['user_id' => $anotherUsers[0]->id]);


        //わざと記事を消す
        Article::where('id','=',$deleteArticle->id)
        ->update(['deleted_at' => Carbon::now()]);


        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->post('/api/article/search/',[
            'userId' => $this->user->id,
            'articleToSearch' =>"",
            'currentPage'  =>1,
            'tagList'      => [$hitTag->id],
            'searchTarget' =>"body"
        ]);

        // print_r($response->json()['articleList']);

        // ステータス
        $response->assertStatus(200);

        // 削除した記事が含まれていないか
        foreach ($response->json()['articleList'] as $data){
            $this->assertNotEquals($data['id'],$deleteArticle->id);
        }

        // 全部指定したユーザーの記事か
        foreach ($response->json()['articleList'] as $data){
            $this->assertEquals($data['user_id'],$this->user->id);
        }

        $temp = [];
        foreach ($response->json()['articleList'] as $data){
            array_push( $temp , $data['id'] );
        }

        // ヒットするはずの記事を取ってきているか
        $this->assertContains($hitArticle1->id, $temp);
        $this->assertContains($hitArticle2->id, $temp);
    }

    public function test_articleSearch_タグ指定あり_本文検索_指定したユーザーの記事だけを取ってくる_削除した記事とって来ない()
    {
        //
        Carbon::setTestNow(Carbon::now());

        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーの記事
        $articles = Article::factory()->count(10)->create(['user_id' => $this->user->id]);


        //検索で引っかかるような記事作成
        $hitArticle = Article::factory()->create([
            'user_id' => $this->user->id,
            'body' => 'how to make applePie'
        ]);

        $hitTag = Tag::factory()->create([
            'name'    => 'apple',
            'user_id' => $this->user->id
        ]);

        // 他のダミーにも付けるタグ
        $tag = Tag::factory()->create([
            'name'    => 'recipe',
            'user_id' => $this->user->id
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
            'user_id' => $this->user->id,
            'body' => 'how to make berryPie'
        ]);

        $dammyTag1 = Tag::factory()->create([
            'name'    => 'berry',
            'user_id' => $this->user->id
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
            'user_id' => $this->user->id,
            'body' => 'how to make peachPie'
        ]);

        $dammyTag2 = Tag::factory()->create([
            'name'    => 'berry',
            'user_id' => $this->user->id
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


        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->post('/api/article/search/',[
            'userId' => $this->user->id,
            'articleToSearch' =>"make",
            'currentPage'  =>1,
            'tagList'      => [$hitTag->id],
            'searchTarget' =>"body"
        ]);

        // print_r($response->json()['articleList']);

        // ステータス
        $response->assertStatus(200);

        // 削除した記事が含まれていないか
        foreach ($response->json()['articleList'] as $data){
            $this->assertNotEquals($data['id'],$deleteArticle->id);
        }

        // 全部指定したユーザーの記事か
        foreach ($response->json()['articleList'] as $data){
            $this->assertEquals($data['user_id'],$this->user->id);
        }

        $temp = [];
        foreach ($response->json()['articleList'] as $data){
            array_push( $temp , $data['id'] );
        }

        // ヒットするはずの記事を取ってきているか
        $this->assertContains($hitArticle->id, $temp);
    }
}
