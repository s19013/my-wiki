<?php

namespace Tests\Feature\Controller;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Tag;
use App\Models\User;

// データベース関係で使う
use Illuminate\Foundation\Testing\WithoutMiddleware;

use Carbon\Carbon;

/**
 * 今は$request->session()->regenerateToken();で騒ぎ立てられるのをどうにかできないので
 * $request->session()->regenerateToken();はすべてコメントアウトしてテストする
 */
class TagControllerTest extends TestCase
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
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'ja';
    }

    // 期待
    // 引数にしていした文字列をtagsテーブルに保存されるか
    // 条件
    // 指定したユーザーのタグ名がまだ登録されていない
    public function test_store_タグがまだ登録されていない()
    {
        $response = $this
        ->actingAs($this->user)
        ->withSession(['XSRF-TOKEN' => 'test','sundlf_session' => 'test'])
        ->post('/api/tag/store/',[
            'name' => "test_store",
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // ブックマーク
        $this->assertDatabaseHas('tags',[
            'user_id'=> $this->user->id,
            'name' => "test_store",
            'deleted_at' => null,
        ]);
    }

    // 期待
    // 400番エラーがかえされるか
    // 条件
    // 指定したユーザーが同じタグ名を登録しようとしている
    public function test_store_同じタグ名を登録しようとしている()
    {
        Tag::create([
            'name'    => 'test',
            'user_id' => $this->user->id,
        ]);

        $response = $this
        ->actingAs($this->user)
        ->withSession(['XSRF-TOKEN' => 'test','sundlf_session' => 'test'])
        ->post('/api/tag/store/',[
            'name' => 'test',
        ]);

        // ステータス
        $response->assertStatus(400);
        $response->assertJson(['messages' => ["name" => ["そのタグは既に保存しています"]]]);
    }

    // 期待
    // 指定したタグが更新できた
    // 条件
    // 変更したタグ名が他のタグとかぶらなかった
    public function test_update_変更したタグ名が他のタグとかぶらなかった()
    {
        $tag = Tag::create([
            'name'    => 'test',
            'user_id' => $this->user->id,
        ]);

        $response = $this
        ->actingAs($this->user)
        ->withSession(['XSRF-TOKEN' => 'test','sundlf_session' => 'test'])
        ->put('/api/tag/update/',[
            'id'   => $tag->id,
            'name' => 'update'
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // ブックマーク
        $this->assertDatabaseHas('tags',[
            'id'     => $tag->id,
            'user_id'=> $this->user->id,
            'name'   => "update",
            'deleted_at' => null,
        ]);
    }

    // 期待
    // 400番エラーが返された
    // 条件
    // 変更したタグ名が他のタグとかぶった
    public function test_update_変更したタグ名が他のタグとかぶった()
    {
        Tag::create([
            'name'    => 'allready',
            'user_id' => $this->user->id,
        ]);

        $tag = Tag::create([
            'name'    => 'test',
            'user_id' => $this->user->id,
        ]);

        $response = $this
        ->actingAs($this->user)
        ->withSession(['XSRF-TOKEN' => 'test','sundlf_session' => 'test'])
        ->put('/api/tag/update/',[
            'id'   => $tag->id,
            'name' => 'allready'
        ]);

        // ステータス
        $response->assertStatus(400);
        $response->assertJson(['messages' => ["name" => ["そのタグは既に保存しています"]]]);
    }

    // 期待
    // 他人のタグを編集しようとしたらブロックされる
    // 401番エラーが返された
    // 条件
    // 変更したタグ名が他のタグとかぶった
    public function test_update_他人のタグを編集しようとしたらブロックされる()
    {
        $tag = Tag::factory()->create(['user_id' => $this->user->id]);

        $otherUser = User::factory()->create();

        $response = $this
        ->actingAs($otherUser)
        ->withSession(['XSRF-TOKEN' => 'test','sundlf_session' => 'test'])
        ->put('/api/tag/update/',[
            'id'   => $tag->id,
            'name' => 'allready'
        ]);

        // ステータス
        $response->assertStatus(401);
    }

    // 期待
    // * nameカラムに指定したキーワードを含むデータをとってくる
    // * 指定したユーザーのタグを取ってくる
    // * けしたタグは取ってこない
    // 条件
    // キーワードあり
    public function test_search_キーワードあり()
    {
        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        $hitTag1 = Tag::factory()->create([
            'user_id' => $this->user->id,
            'name'    => 'php'
        ]);

        $hitTag2 = Tag::factory()->create([
            'user_id' => $this->user->id,
            'name'    => 'phpUnit'
        ]);

        // 他にダミーのタグを作る
        Tag::factory()->count(5)->create(['user_id' => $this->user->id,]);
        Tag::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id,]);
        Tag::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id,]);

        // 消す
        // まだ消す作業は追加してなかったからまたこんど

        $response = $this
        ->actingAs($this->user)
        ->withSession(['XSRF-TOKEN' => 'test','sundlf_session' => 'test'])
        ->post('/api/tag/search/',[
            'keyword' => "php",
        ]);

        // ステータス
        $response->assertStatus(200);

        // 削除したタグが含まれていないか
        // foreach ($response->json() as $data){
        //     $this->assertNotEquals($data['id'],$deleteBookMark->id);
        // }

        // 全部指定したユーザーのタグか
        foreach ($response->json() as $data){
            $this->assertEquals($data['user_id'],$this->user->id);
        }

        $temp = [];
        foreach ($response->json() as $data){
            array_push( $temp , $data['id'] );
        }

        // ヒットするはずのタグを取ってきているか
        $this->assertContains($hitTag1->id, $temp);
        $this->assertContains($hitTag2->id, $temp);
    }

    // 期待
    // * 指定したユーザーのタグをすべて取ってくる
    // * けしたタグは取ってこない
    // 条件
    // キーワードあり
    public function test_search_キーワードなし()
    {
        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        // 他にダミーのタグを作る
        Tag::factory()->count(10)->create(['user_id' => $this->user->id,]);
        Tag::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id,]);
        Tag::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id,]);

        // 消す
        // まだ消す作業は追加してなかったからまたこんど

        $response = $this
        ->actingAs($this->user)
        ->withSession(['XSRF-TOKEN' => 'test','sundlf_session' => 'test'])
        ->post('/api/tag/search/',[
            'keyword' => "",
        ]);

        // ステータス
        $response->assertStatus(200);

        // print_r($response->json());

        // 削除したタグが含まれていないか
        // foreach ($response->json() as $data){
        //     $this->assertNotEquals($data['id'],$deleteBookMark->id);
        // }

        // 全部指定したユーザーのタグか
        foreach ($response->json() as $data){
            $this->assertEquals($data['user_id'],$this->user->id);
        }

        $temp = [];
        foreach ($response->json() as $data){
            array_push( $temp , $data['id'] );
        }

        // 個数はあっているか
        $this->assertEquals(10, count($temp));
    }

    // 期待
    // * タグを削除できる
    // 条件
    public function test_delete_自分のタグを消す(){
        $tag = Tag::factory()->create(['user_id' => $this->user->id]);
        $response = $this
        ->actingAs($this->user)
        ->withSession([
            'my_wiki_session' => 'test',
            'XSRF-TOKEN' => 'test'
        ])
        ->delete('/api/tag/'.$tag->id);

        $response->assertStatus(200);

        //
        $this->assertDatabaseHas('tags',[
            'id' => $tag->id,
            'deleted_at' => Carbon::now(),
        ]);
    }

    // 期待
    // * 他人のタグを消そうとするがシステムに防がれる
    // * 401が返される
    // 条件
    public function test_delete_他人のタグを消そうとするがシステムに防がれる(){

        $otherUser = User::factory()->create();

        $tag = Tag::factory()->create(['user_id' => $this->user->id]);

        $response = $this
        ->actingAs($otherUser)
        ->withSession([
            'my_wiki_session' => 'test',
            'XSRF-TOKEN' => 'test'
        ])
        ->delete('/api/tag/'.$tag->id);

        $response->assertStatus(401);

        //
        $this->assertDatabaseHas('tags',[
            'id' => $tag->id,
            'deleted_at' => null,
        ]);
    }
}
