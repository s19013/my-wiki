<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Http\Request;
use App\Http\Controllers\BookMarkController;

use App\Models\BookMark;
use App\Models\BookMarkTag;
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
class BookMarkValidationTest extends TestCase
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
    }

    public function test_バリデーション_url未入力()
    {

        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->post('/api/bookmark/store/',[
            'bookMarkTitle'=> "test",
            'bookMarkUrl'  => null ,
            'tagList'      => [],
        ]);

        // ステータス
        $response->assertStatus(400);

        // json
        $response->assertJson([
            'errors' => ["bookMarkUrl" => ["urlを入力してください"]],
            ]);

    }

    public function test_バリデーション_url形式で入力されていない()
    {

        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->post('/api/bookmark/store/',[
            'bookMarkTitle'=> "test",
            'bookMarkUrl'  => "test" ,
            'tagList'      => [],
        ]);

        // ステータス
        $response->assertStatus(400);

        // json
        $response->assertJson([
            'errors' => ["bookMarkUrl" => ["url形式で入力してください"]],
            ]);

    }

    public function test_バリデーション_タイトルが長過ぎる()
    {

        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->post('/api/bookmark/store/',[
            'bookMarkTitle'=> "mF4KdhFD7nMGMVVhQRQEzgAfkZgke4yFGsw7ysAdYABhnnduMHTHuZVFnuA65Lspu5wennHbBzAuxYd-KjsfZuR4X85sgpt-PKhtbyapceNZTCPPxRiRnh6f62XYbn-dJ7DFkX3mhCgijh4K-uBrKYZ-jW5uY3NDn_cxpKBVZVknDSQdefjg8dbrPkmtyUdGMscJbdPPWQdgRVtdHxiVjBXatSDLjB4ftmT3FRmPheLd7p-ZxcditeSw2caaaaaaa",
            'bookMarkUrl'  => "http://hide-no-server.com" ,
            'tagList'      => [],
        ]);

        // ステータス
        $response->assertStatus(400);

        // json
        $response->assertJson([
            'errors' => ["bookMarkTitle" => ["126文字以内で入力してください"]],
            ]);

    }
}
