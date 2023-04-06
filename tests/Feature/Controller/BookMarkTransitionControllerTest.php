<?php

namespace Tests\Feature\Controller;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Tag;
use App\Models\BookMark;
use App\Models\BookMarkTag;
use App\Http\Controllers\BookMarkTransitionController;

use App\Repository\BookMarkTagRepository;

use Illuminate\Foundation\Testing\WithoutMiddleware;

use Carbon\Carbon;

class BookMarkTransitionControllerTest extends TestCase
{
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

    // テストしたらリセットする
    use RefreshDatabase;
    // ミドルウェアの無効化
    // use WithoutMiddleware;

    private $user;
    private $controller;
    private $bookMarkRepository;

    public function setup():void
    {
        parent::setUp();
        // ユーザーを用意
        $this->user = User::factory()->create();
        $this->controller = new BookMarkTransitionController();
        $this->bookMarkRepository = new BookMarkTagRepository();

        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'ja';
    }

    public function test_transitionToEditBookMark_自分の記事_タグ登録済み()
    {
        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //ダミー
        BookMark::factory()->count(5)->create(['user_id' => $this->user->id]);
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        //今回使うやつ
        $bookMark = BookMark::factory()->create(['user_id' => $this->user->id]);

        // タグを登録
        $tags = Tag::factory()->count(3)->create(['user_id' => $this->user->id]);

        foreach ($tags as $tag) {
            $this->bookMarkRepository->store($tag->id,$bookMark->id);
        }

        $response = $this
        ->actingAs($this->user)
        ->withHeaders(['UserLang' => 'ja'])
        ->withSession(['XSRF-TOKEN' => 'test','sundlf_session' => 'test'])
        ->get(route('EditBookMark', ['bookMarkId' => $bookMark->id]));

        // ステータス
        $response->assertStatus(200);
    }

    public function test_transitionToEditBookMark_自分の記事_タグ未登録()
    {
        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //ダミー
        BookMark::factory()->count(5)->create(['user_id' => $this->user->id]);
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        //今回使うやつ
        $bookMark = BookMark::factory()->create(['user_id' => $this->user->id]);

        // タグを登録
        $tags = Tag::factory()->count(3)->create(['user_id' => $this->user->id]);

        $this->bookMarkRepository->store(null,$bookMark->id);

        $response = $this
        ->actingAs($this->user)
        ->withHeaders(['UserLang' => 'ja'])
        ->withSession(['XSRF-TOKEN' => 'test','sundlf_session' => 'test'])
        ->get(route('EditBookMark', ['bookMarkId' => $bookMark->id]));

        // ステータス
        $response->assertStatus(200);
    }

    public function test_transitionToEditBookMark_自分の削除した記事()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //ダミー
        BookMark::factory()->count(5)->create(['user_id' => $this->user->id]);
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        //今回使うやつ
        $bookMark = BookMark::factory()->create(['user_id' => $this->user->id]);

        // タグを登録
        $tags = Tag::factory()->count(3)->create(['user_id' => $this->user->id]);

        foreach ($tags as $tag) {
            $this->bookMarkRepository->store($tag->id,$bookMark->id);
        }

        //削除
        BookMark::where('id','=',$bookMark->id)->update(['deleted_at' => Carbon::now()]);

        $response = $this
        ->actingAs($this->user)
        ->withHeaders(['UserLang' => 'ja'])
        ->withSession(['XSRF-TOKEN' => 'test','sundlf_session' => 'test'])
        ->get(route('EditBookMark', ['bookMarkId' => $bookMark->id]));

        // ステータス
        $response->assertStatus(302);

        //searchBookMarkにリダイレクトしたか
        $response->assertRedirect(route('SearchBookMark'));
    }

    public function test_transitionToEditBookMark_他人の記事()
    {
        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //ダミー
        BookMark::factory()->count(5)->create(['user_id' => $this->user->id]);
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        //今回使うやつ 他人の記事
        $bookMark = BookMark::factory()->create(['user_id' => $anotherUsers[0]->id]);

        // タグを登録
        $tags = Tag::factory()->count(3)->create(['user_id' => $this->user->id]);

        foreach ($tags as $tag) {
            $this->bookMarkRepository->store($tag->id,$bookMark->id);
        }

        $response = $this
        ->actingAs($this->user)
        ->withHeaders(['UserLang' => 'ja'])
        ->withSession(['XSRF-TOKEN' => 'test','sundlf_session' => 'test'])
        ->get(route('EditBookMark', ['bookMarkId' => $bookMark->id]));

        // ステータス
        $response->assertStatus(302);

        //searchBookMarkにリダイレクトしたか
        $response->assertRedirect(route('SearchBookMark'));
    }

}

