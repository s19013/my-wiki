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

    // 期待
    // * タイトル､本文がarticlesテーブルに保存される
    // * 配列で渡したタグのidがarticle_tagsテーブルに保存される
    // 条件
    // * タグあり
    // * タイトルあり
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

    // 期待
    // * 本文がarticlesテーブルに保存される
    // * タイトルのカラムには今日の日付がarticlesテーブルに保存される
    // * 配列で渡したタグのidがarticle_tagsテーブルに保存される
    // 条件
    // * タグあり
    // * タイトルなし
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
        foreach ($tags as $tag){
            $this->assertDatabaseHas('article_tags',[
                'article_id' => $articleId,
                'tag_id'     => $tag->id,
                'deleted_at' => null,
            ]);
        }
    }

    // 期待
    // * 本文がarticlesテーブルに保存される
    // * titleのカラムには今日の日付がarticlesテーブルに保存される
    // * 配列で渡したタグのidがarticle_tagsテーブルに保存される
    // 条件
    // * タグあり
    // * タイトルなし
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

    // 期待
    // * 本文がarticlesテーブルに保存される
    // * titleのカラムには今日の日付がarticlesテーブルに保存される
    // * tag_idのカラムにnullがarticle_tagsテーブルに保存される
    // 条件
    // * タグなし
    // * タイトルなし
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

    // 期待
    // * タイトル､本文が更新されている
    // * もとの記事についていたタグのidがすべて論理削除されている
    // * 新しく紐づけたタグのidがarticle_tagsテーブルに保存される
    // 条件
    // * もとの記事についていたタグをすべて外す
    // * 記事に別のタグを紐づける
    public function test_articleUpdate_タグ総入れ替え()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        // 記事などを作成
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);
        $newTags = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);

        foreach ($tags as $tag){
            ArticleTag::create([
                "article_id" => $article->id,
                "tag_id"     => $tag->id
            ]);
        }

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
        foreach ($newTags as $newTag){
            $this->assertDatabaseHas('article_tags',[
                'article_id' => $article->id,
                'tag_id'     => $newTag->id,
                'deleted_at' => null,
            ]);
        }

        //削除したタグ
        foreach ($tags as $tag){
            $this->assertDatabaseHas('article_tags',[
                'article_id' => $article->id,
                'tag_id'     => $tag->id,
                'deleted_at' => Carbon::now(),
            ]);
        }
    }

    // 期待
    // * タイトル､本文が更新されている
    // * もとの記事についていたタグのidになにも変化がない
    // * 新しく紐づけたタグのidがarticle_tagsテーブルに保存される
    // 条件
    // * 記事に別のタグを追加で紐づける
    public function test_articleUpdate_元のタグをそのままに新しく追加()
    {
        // 記事などを作成
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);
        $newTags = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);

        foreach ($tags as $tag){
            ArticleTag::create([
                "article_id" => $article->id,
                "tag_id"     => $tag->id
            ]);
        }

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
        // 新しくつけたタグ
        foreach ($newTags as $newTag){
            $this->assertDatabaseHas('article_tags',[
                'article_id' => $article->id,
                'tag_id'     => $newTag->id,
                'deleted_at' => null,
            ]);
        }

        // もともとつけていたタグ
        foreach ($tags as $tag){
            $this->assertDatabaseHas('article_tags',[
                'article_id' => $article->id,
                'tag_id'     => $tag->id,
                'deleted_at' => null,
            ]);
        }
    }

    // 期待
    // * タイトル､本文が更新されている
    // * もとの記事についていたタグのidになにも変化がない
    // * 新しく紐づけたタグのidがarticle_tagsテーブルに保存される
    // 条件
    // * つけているタグの一部を消す
    public function test_articleUpdate_タグの一部を消す()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        // 記事などを作成
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(4)->create(['user_id' => $this->user->id]);

        foreach ($tags as $tag){
            ArticleTag::create([
                "article_id" => $article->id,
                "tag_id"     => $tag->id
            ]);
        }

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

    // 期待
    // * タイトル､本文が更新されている
    // * もとの記事のtag_id = null のデータを論理削除
    // * 新しく紐づけたタグのidがarticle_tagsテーブルに保存される
    // 条件
    // * タグがついてなかった記事にタグを付ける
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
        foreach ($tags as $tag){
            $this->assertDatabaseHas('article_tags',[
                'article_id' => $article->id,
                'tag_id'     => $tag->id,
                'deleted_at' => null,
            ]);
        }

        //削除したタグ
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => null,
            'deleted_at' => Carbon::now(),
        ]);
    }

}
