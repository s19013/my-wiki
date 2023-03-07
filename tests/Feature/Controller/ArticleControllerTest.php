<?php

namespace Tests\Feature\Controller;

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
    // use WithoutMiddleware;

    private $user;

    public function setup():void
    {
        parent::setUp();
        // ユーザーを用意
        $this->user = User::factory()->create();

        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'ja';
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
    public function test_store_タグあり_タイトルあり()
    {
        $tags = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);

        $response = $this
        ->actingAs($this->user)
        ->withSession([
            'my_wiki_session' => 'test',
            'XSRF-TOKEN' => 'test'
        ])
        ->post('/api/article/store/',[
            'articleTitle' => "testTitletest_store_タグあり_タイトルあり",
            'articleBody'  => "testBodytest_store_タグあり_タイトルあり" ,
            'tagList'      => [$tags[0]->id,$tags[1]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // 記事
        $this->assertDatabaseHas('articles',[
            'user_id'=> $this->user->id,
            'title' => "testTitletest_store_タグあり_タイトルあり",
            'body'  => "testBodytest_store_タグあり_タイトルあり",
            'deleted_at' => null,
        ]);

        $article = Article::select('id')
        ->where('user_id', '=' ,$this->user->id)
        ->where('title','=',"testTitletest_store_タグあり_タイトルあり")
        ->where('body' ,'=',"testBodytest_store_タグあり_タイトルあり" )
        ->first();

        $articleId = $article->id;

        //タグ
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $articleId,
            'tag_id'     => $tags[0]->id,
        ]);

        $this->assertDatabaseHas('article_tags',[
            'article_id' => $articleId,
            'tag_id'     => $tags[1]->id,
        ]);

    }

    // 期待
    // * 本文がarticlesテーブルに保存される
    // * タイトルのカラムには今日の日付がarticlesテーブルに保存される
    // * 配列で渡したタグのidがarticle_tagsテーブルに保存される
    // 条件
    // * タグあり
    // * タイトルなし
    public function test_store_タグあり_タイトルなし()
    {
        $tags = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);

        $response = $this
        ->actingAs($this->user)
        ->withSession([
            'my_wiki_session' => 'test',
            'XSRF-TOKEN' => 'test'
        ])
        ->post('/api/article/store/',[
            'articleTitle' => "",
            'articleBody'  => "testBodytest_store_タグあり_タイトルなし" ,
            'tagList'      => [$tags[0]->id,$tags[1]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // 記事
        $this->assertDatabaseHas('articles',[
            'user_id'=> $this->user->id,
            'title'  => Carbon::now(),
            'body'   => "testBodytest_store_タグあり_タイトルなし",
            'deleted_at' => null,
        ]);

        $article = Article::select('id')
        ->where('user_id', '=' ,$this->user->id)
        ->where('title','=',Carbon::now())
        ->where('body' ,'=',"testBodytest_store_タグあり_タイトルなし" )
        ->first();

        $articleId = $article->id;

        //タグ
        foreach ($tags as $tag){
            $this->assertDatabaseHas('article_tags',[
                'article_id' => $articleId,
                'tag_id'     => $tag->id,
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
    public function test_store_タグなし_タイトルあり()
    {
        $response = $this
        ->actingAs($this->user)
        ->withSession([
            'my_wiki_session' => 'test',
            'XSRF-TOKEN' => 'test'
        ])
        ->post('/api/article/store/',[
            'articleTitle' => "testTitletest_store_タグなし_タイトルあり",
            'articleBody'  => "testBodytest_store_タグなし_タイトルあり" ,
            'tagList'      => [],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // 記事
        $this->assertDatabaseHas('articles',[
            'user_id'=> $this->user->id,
            'title'  => "testTitletest_store_タグなし_タイトルあり",
            'body'   => "testBodytest_store_タグなし_タイトルあり",
            'deleted_at' => null,
        ]);

        $article = Article::select('id')
        ->where('user_id', '=' ,$this->user->id)
        ->where('title','=',"testTitletest_store_タグなし_タイトルあり")
        ->where('body' ,'=',"testBodytest_store_タグなし_タイトルあり" )
        ->first();

        $articleId = $article->id;

        //タグ
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $articleId,
            'tag_id'     => null,
        ]);
    }

    // 期待
    // * 本文がarticlesテーブルに保存される
    // * titleのカラムには今日の日付がarticlesテーブルに保存される
    // * tag_idのカラムにnullがarticle_tagsテーブルに保存される
    // 条件
    // * タグなし
    // * タイトルなし
    public function test_store_タグなし_タイトルなし()
    {

        $response = $this
        ->actingAs($this->user)
        ->withSession([
            'my_wiki_session' => 'test',
            'XSRF-TOKEN' => 'test'
        ])
        ->post('/api/article/store/',[
            'articleTitle' => "",
            'articleBody'  => "testBodytest_store_タグなし_タイトルなし" ,
            'tagList'      => [],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // 記事
        $this->assertDatabaseHas('articles',[
            'user_id'=> $this->user->id,
            'title'  => Carbon::now(),
            'body'   => "testBodytest_store_タグなし_タイトルなし",
            'deleted_at' => null,
        ]);

        $article = Article::select('id')
        ->where('user_id', '=' ,$this->user->id)
        ->where('title','=',Carbon::now())
        ->where('body' ,'=',"testBodytest_store_タグなし_タイトルなし" )
        ->first();

        $articleId = $article->id;

        //タグ
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $articleId,
            'tag_id'     => null,
        ]);
    }

    // 期待
    // * 記事を削除できる
    // 条件
    public function test_delete_自分の記事を消す(){
        $article = Article::factory()->create(['user_id' => $this->user->id]);
        $response = $this
        ->actingAs($this->user)
        ->withSession([
            'my_wiki_session' => 'test',
            'XSRF-TOKEN' => 'test'
        ])
        ->delete('/api/article/'.$article->id);

        $response->assertStatus(200);

        //
        $this->assertDatabaseHas('articles',[
            'id' => $article->id,
            'deleted_at' => Carbon::now(),
        ]);
    }

    // 期待
    // * 他人の記事を消そうとするがシステムに防がれる
    // 条件
    public function test_delete_他人の記事を消そうとするがシステムに防がれる(){

        $otherUser = User::factory()->create();

        $article = Article::factory()->create(['user_id' => $this->user->id]);

        $response = $this
        ->actingAs($otherUser)
        ->withSession([
            'my_wiki_session' => 'test',
            'XSRF-TOKEN' => 'test'
        ])
        ->delete('/api/article/'.$article->id);

        $response->assertStatus(401);
    }

}
