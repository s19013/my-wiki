<?php

namespace Tests\Feature\Controller;

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
class BookMarkControllerTest extends TestCase
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

        // ここでこの値を設定しておかないとエラーが出てしまう(テスト環境のみ)
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'ja';
    }

    // 期待
    // * タイトル､urlがbook_marksテーブルに保存される
    // * 配列で渡したタグのidがbook_mark_tagsテーブルに保存される
    // 条件
    // * タグあり
    // * タイトルあり
    public function test_bookMarkStore_タグあり_タイトルあり()
    {
        $tags = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);

        $response = $this
        ->actingAs($this->user)
        ->withSession(['XSRF-TOKEN' => 'test','sundlf_session' => 'test'])
        ->post('/api/bookmark/store/',[
            'bookMarkTitle'=> "testTitletest_bookMarkStore_タグあり_タイトルあり",
            'bookMarkUrl'  => "http://hide-no-server.com/タグあり_タイトルあり" ,
            'tagList'      => [$tags[0]->id,$tags[1]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // ブックマーク
        $this->assertDatabaseHas('book_marks',[
            'user_id'=> $this->user->id,
            'title'  => "testTitletest_bookMarkStore_タグあり_タイトルあり",
            'url'    => "http://hide-no-server.com/タグあり_タイトルあり",
            'deleted_at' => null,
        ]);

        $bookMark = BookMark::select('id')
        ->where('user_id', '=' ,$this->user->id)
        ->where('title','=',"testTitletest_bookMarkStore_タグあり_タイトルあり")
        ->where('url' ,'=',"http://hide-no-server.com/タグあり_タイトルあり" )
        ->first();

        $bookMarkId = $bookMark->id;

        //タグ
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMarkId,
            'tag_id'     => $tags[0]->id,
        ]);

        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMarkId,
            'tag_id'     => $tags[1]->id,
        ]);

    }

    // 期待
    // * urlがbook_marksテーブルに保存される
    // * タイトルのカラムには今日の日付がbook_marksテーブルに保存される
    // * 配列で渡したタグのidがbook_mark_tagsテーブルに保存される
    // 条件
    // * タグあり
    // * タイトルなし
    public function test_bookMarkStore_タグあり_タイトルなし()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        $tags = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);

        $response = $this
        ->actingAs($this->user)
        ->withSession(['XSRF-TOKEN' => 'test','sundlf_session' => 'test'])
        ->post('/api/bookmark/store/',[
            'bookMarkTitle' => "",
            'bookMarkUrl'  => "http://hide-no-server.com/タグあり_タイトルなし" ,
            'tagList'      => [$tags[0]->id,$tags[1]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // ブックマーク
        $this->assertDatabaseHas('book_marks',[
            'user_id'=> $this->user->id,
            'title'  => Carbon::now("UTC")."(UTC)",
            'url'   => "http://hide-no-server.com/タグあり_タイトルなし",
            'deleted_at' => null,
        ]);

        $bookMark = BookMark::select('id')
        ->where('user_id', '=' ,$this->user->id)
        ->where('title','=',Carbon::now("UTC")."(UTC)")
        ->where('url' ,'=',"http://hide-no-server.com/タグあり_タイトルなし" )
        ->first();

        $bookMarkId = $bookMark->id;

        //タグ
        foreach ($tags as $tag){
            $this->assertDatabaseHas('book_mark_tags',[
                'book_mark_id' => $bookMarkId,
                'tag_id'     => $tag->id,
            ]);
        }
    }

    // 期待
    // * urlがbook_marksテーブルに保存される
    // * titleのカラムには今日の日付がbook_marksテーブルに保存される
    // * 配列で渡したタグのidがbook_mark_tagsテーブルに保存される
    // 条件
    // * タグあり
    // * タイトルなし
    public function test_bookMarkStore_タグなし_タイトルあり()
    {
        $response = $this
        ->actingAs($this->user)
        ->withSession(['XSRF-TOKEN' => 'test','sundlf_session' => 'test'])
        ->post('/api/bookmark/store/',[
            'bookMarkTitle' => "testTitletest_bookMarkStore_タグなし_タイトルあり",
            'bookMarkUrl'   => "http://hide-no-server.com/タグなし_タイトルあり" ,
            'tagList'       => [],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // ブックマーク
        $this->assertDatabaseHas('book_marks',[
            'user_id'=> $this->user->id,
            'title'  => "testTitletest_bookMarkStore_タグなし_タイトルあり",
            'url'   => "http://hide-no-server.com/タグなし_タイトルあり",
            'deleted_at' => null,
        ]);

        $bookMark = BookMark::select('id')
        ->where('user_id', '=' ,$this->user->id)
        ->where('title','=',"testTitletest_bookMarkStore_タグなし_タイトルあり")
        ->where('url' ,'=',"http://hide-no-server.com/タグなし_タイトルあり" )
        ->first();

        $bookMarkId = $bookMark->id;

        //タグ
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMarkId,
            'tag_id'     => null,
        ]);
    }

    // 期待
    // * urlがbook_marksテーブルに保存される
    // * titleのカラムには今日の日付がbook_marksテーブルに保存される
    // * tag_idのカラムにnullがbook_mark_tagsテーブルに保存される
    // 条件
    // * タグなし
    // * タイトルなし
    public function test_bookMarkStore_タグなし_タイトルなし()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        $response = $this
        ->actingAs($this->user)
        ->withSession(['XSRF-TOKEN' => 'test','sundlf_session' => 'test'])
        ->post('/api/bookmark/store/',[
            'bookMarkTitle' => "",
            'bookMarkUrl'   => "http://hide-no-server.com/タグなし_タイトルなし" ,
            'tagList'       => [],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // ブックマーク
        $this->assertDatabaseHas('book_marks',[
            'user_id'=> $this->user->id,
            'title'  => Carbon::now("UTC")."(UTC)",
            'url'    => "http://hide-no-server.com/タグなし_タイトルなし",
            'deleted_at' => null,
        ]);

        $bookMark = BookMark::select('id')
        ->where('user_id', '=' ,$this->user->id)
        ->where('title','=',Carbon::now("UTC")."(UTC)")
        ->where('url' ,'=',"http://hide-no-server.com/タグなし_タイトルなし" )
        ->first();

        $bookMarkId = $bookMark->id;

        //タグ
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMarkId,
            'tag_id'     => null,
        ]);
    }

    // 期待
    // * エラーjsonが返される
    // 条件
    // * 登録ずみのurlと同じ
    public function test_bookMarkStore_登録ずみのurlと同じ()
    {
        $this
        ->actingAs($this->user)
        ->withSession(['XSRF-TOKEN' => 'test','sundlf_session' => 'test'])
        ->post('/api/bookmark/store/',[
            'bookMarkTitle' => "testTitletest_bookMarkStore_タグあり_タイトルあり",
            'bookMarkUrl'   => "http://hide-no-server.com/タグあり_タイトルあり" ,
            'tagList'       => [],
        ]);

        $response = $this
        ->actingAs($this->user)
        ->withSession(['XSRF-TOKEN' => 'test','sundlf_session' => 'test'])
        ->post('/api/bookmark/store/',[
            'bookMarkTitle' => "testTitletest_bookMarkStore_タグあり_タイトルあり",
            'bookMarkUrl'   => "http://hide-no-server.com/タグあり_タイトルあり" ,
            'tagList'       => [],
        ]);

        // ステータス
        $response->assertStatus(400);

        // データベース
        $response->assertJson([
            'messages' => ["bookMarkUrl" => ["そのブックマークは既に保存しています"]],
            ]);
    }

    // 期待
    // * ブックマークを削除できる
    // 条件
    public function test_delete_自分のブックマークを消す(){
        $bookMark = BookMark::factory()->create(['user_id' => $this->user->id]);
        $response = $this
        ->actingAs($this->user)
        ->withSession([
            'my_wiki_session' => 'test',
            'XSRF-TOKEN' => 'test'
        ])
        ->delete('/api/bookmark/'.$bookMark->id);

        $response->assertStatus(200);

        //
        $this->assertDatabaseHas('book_marks',[
            'id' => $bookMark->id,
            'deleted_at' => Carbon::now(),
        ]);
    }

    // 期待
    // * 他人のブックマークを消そうとするがシステムに防がれる
    // 条件
    public function test_delete_他人のブックマークを消そうとするがシステムに防がれる(){

        $otherUser = User::factory()->create();

        $bookMark = BookMark::factory()->create(['user_id' => $this->user->id]);

        $response = $this
        ->actingAs($otherUser)
        ->withSession([
            'my_wiki_session' => 'test',
            'XSRF-TOKEN' => 'test'
        ])
        ->delete('/api/bookmark/'.$bookMark->id);

        $response->assertStatus(401);
    }

    // カウントアップ
    // public function test_countup_通常(){
    //     $bookMark = BookMark::factory()->create(['user_id' => $this->user->id]);

    //     $response = $this
    //     ->actingAs($this->user)
    //     ->withSession([
    //         'my_wiki_session' => 'test',
    //         'XSRF-TOKEN' => 'test'
    //     ])
    //     ->get('/api/bookmark/countup/'.$bookMark->id);

    //     $response->assertStatus(200);

    //     //
    //     $this->assertDatabaseHas('book_marks',[
    //         'id' => $bookMark->id,
    //         'count' => 1
    //     ]);
    // }

    // 削除済みはそもそもsameuserで引っかかる
    // public function test_countup_削除済みはカウントしない(){
    //     $bookMark = BookMark::factory()->create(['user_id' => $this->user->id]);

    //     BookMark::where('id','=',$bookMark->id)->delete();

    //     $response = $this
    //     ->actingAs($this->user)
    //     ->withSession([
    //         'my_wiki_session' => 'test',
    //         'XSRF-TOKEN' => 'test'
    //     ])
    //     ->get('/api/bookmark/countup/'.$bookMark->id);

    //     $response->assertStatus(400);
    // }

    // 別のユーザー
    public function test_countup_別のユーザーはカウントしない(){
        $otherUser = User::factory()->create();

        $bookMark = BookMark::factory()->create(['user_id' => $this->user->id]);

        $response = $this
        ->actingAs($otherUser)
        ->withSession([
            'my_wiki_session' => 'test',
            'XSRF-TOKEN' => 'test'
        ])
        ->get('/api/bookmark/countup/'.$bookMark->id);

        $response->assertStatus(400);
    }

}
