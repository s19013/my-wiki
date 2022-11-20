<?php

namespace Tests\Feature\Validation;

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
class ArticleValidationTest extends TestCase
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
    }

    public function test_store_バリデーションに引っかかる()
    {
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        $response = $this
        ->actingAs($this->user)
        ->withSession([
            'my_wiki_session' => 'test',
            'XSRF-TOKEN' => 'test'
        ])
        ->post('/api/article/store/',[
            'articleTitle' => "mF4KdhFD7nMGMVVhQRQEzgAfkZgke4yFGsw7ysAdYABhnnduMHTHuZVFnuA65Lspu5wennHbBzAuxYd-KjsfZuR4X85sgpt-PKhtbyapceNZTCPPxRiRnh6f62XYbn-dJ7DFkX3mhCgijh4K-uBrKYZ-jW5uY3NDn_cxpKBVZVknDSQdefjg8dbrPkmtyUdGMscJbdPPWQdgRVtdHxiVjBXatSDLjB4ftmT3FRmPheLd7p-ZxcditeSw2caaaaaaaaa",
            'articleBody'  => "test" ,
            'tagList'      => [],
        ]);

        $response->assertStatus(400);

        $this->assertDatabaseHas('articles',[
            'id' => $article->id,
            'deleted_at' => null,
        ]);

        // レスポンス
        $response->assertJson([
            'errors' => ["articleTitle" => ["126文字以内で入力してください"]],
            ]);
    }

    public function test_update_バリデーションに引っかかる()
    {
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        $response = $this
        ->actingAs($this->user)
        ->withSession([
            'my_wiki_session' => 'test',
            'XSRF-TOKEN' => 'test'
        ])
        ->put('/api/article/update/',[
            'articleTitle' => "mF4KdhFD7nMGMVVhQRQEzgAfkZgke4yFGsw7ysAdYABhnnduMHTHuZVFnuA65Lspu5wennHbBzAuxYd-KjsfZuR4X85sgpt-PKhtbyapceNZTCPPxRiRnh6f62XYbn-dJ7DFkX3mhCgijh4K-uBrKYZ-jW5uY3NDn_cxpKBVZVknDSQdefjg8dbrPkmtyUdGMscJbdPPWQdgRVtdHxiVjBXatSDLjB4ftmT3FRmPheLd7p-ZxcditeSw2caaaaaaaaa",
            'articleBody'  => "test" ,
            'tagList'      => [],
        ]);

        $response->assertStatus(400);

        $this->assertDatabaseHas('articles',[
            'id' => $article->id,
            'deleted_at' => null,
        ]);

        // レスポンス
        $response->assertJson([
            'errors' => ["articleTitle" => ["126文字以内で入力してください"]],
            ]);
    }

}
