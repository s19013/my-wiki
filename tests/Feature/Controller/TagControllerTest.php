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
    use WithoutMiddleware;

    private $user;

    public function setup():void
    {
        parent::setUp();
        // ユーザーを用意
        $this->user = User::factory()->create();
    }

    public function test_tagStore()
    {
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->post('/api/tag/store/',[
            'tag' => "test_tagStore",
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // ブックマーク
        $this->assertDatabaseHas('tags',[
            'user_id'=> $this->user->id,
            'name' => "test_tagStore",
            'deleted_at' => null,
        ]);
    }

    public function test_tagSearch_キーワードあり_指定したユーザーのタグを取ってくる_けしたタグは取ってこない()
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
        ->withSession(['test' => 'test'])
        ->post('/api/tag/search/',[
            'tag' => "php",
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

        // ヒットするはずのタグを取ってきているか
        $this->assertContains($hitTag1->id, $temp);
        $this->assertContains($hitTag2->id, $temp);
    }

    public function test_tagSearch_キーワードなし_指定したユーザーのタグを取ってくる_けしたタグは取ってこない()
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
        ->withSession(['test' => 'test'])
        ->post('/api/tag/search/',[
            'tag' => "",
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
}
