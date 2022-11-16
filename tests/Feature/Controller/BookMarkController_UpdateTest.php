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
class BookMarkController_UpdateTest extends TestCase
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

    // 期待
    // * タイトル､urlが更新されている
    // * もとのブックマークについていたタグのidがすべて論理削除されている
    // * 新しく紐づけたタグのidがbook_mark_tagsテーブルに保存される
    // 条件
    // * もとのブックマークについていたタグをすべて外す
    // * ブックマークに別のタグを紐づける
    public function test_bookMarkUpdate_タグ総入れ替え()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        // ブックマークなどを作成
        $bookMark = BookMark::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);
        $newTags = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);

        foreach ($tags as $tag){
            BookMarkTag::create([
                "book_mark_id" => $bookMark->id,
                "tag_id"     => $tag->id
            ]);
        }


        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/bookmark/update/',[
            'bookMarkId'     => $bookMark->id,
            'bookMarkTitle'  => "更新titleタグ総入れ替え",
            'bookMarkUrl'    => "更新urlタグ総入れ替え" ,
            'tagList' => [$newTags[0]->id,$newTags[1]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // ブックマーク
        $this->assertDatabaseHas('book_marks',[
            'user_id'=> $this->user->id,
            'title'  => "更新titleタグ総入れ替え",
            'url'   => "更新urlタグ総入れ替え",
            'deleted_at' => null,
        ]);

        //タグ
        //新しく追加
        foreach ($newTags as $newTag){
            $this->assertDatabaseHas('book_mark_tags',[
                'book_mark_id' => $bookMark->id,
                'tag_id'     => $newTag->id,
                'deleted_at' => null,
            ]);
        }

        //削除したタグ
        foreach ($tags as $tag){
            $this->assertDatabaseHas('book_mark_tags',[
                'book_mark_id' => $bookMark->id,
                'tag_id'     => $tag->id,
                'deleted_at' => Carbon::now(),
            ]);
        }
    }

    // 期待
    // * タイトル､urlが更新されている
    // * もとのブックマークについていたタグのidになにも変化がない
    // * 新しく紐づけたタグのidがbook_mark_tagsテーブルに保存される
    // 条件
    // * ブックマークに別のタグを追加で紐づける
    public function test_bookMarkUpdate_元のタグをそのままに新しく追加()
    {
        // ブックマークなどを作成
        $bookMark = BookMark::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);
        $newTags = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);

        foreach ($tags as $tag){
            BookMarkTag::create([
                "book_mark_id" => $bookMark->id,
                "tag_id"     => $tag->id
            ]);
        }

        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/bookmark/update/',[
            'bookMarkId'     => $bookMark->id,
            'bookMarkTitle'  => "更新title元のタグをそのままに新しく追加",
            'bookMarkUrl'   => "更新url元のタグをそのままに新しく追加" ,
            'tagList' => [$tags[0]->id,$tags[1]->id,$newTags[0]->id,$newTags[1]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // ブックマーク
        $this->assertDatabaseHas('book_marks',[
            'user_id'=> $this->user->id,
            'title'  => "更新title元のタグをそのままに新しく追加",
            'url'   => "更新url元のタグをそのままに新しく追加",
            'deleted_at' => null,
        ]);

        //タグ
        // 新しくつけたタグ
        foreach ($newTags as $newTag){
            $this->assertDatabaseHas('book_mark_tags',[
                'book_mark_id' => $bookMark->id,
                'tag_id'     => $newTag->id,
                'deleted_at' => null,
            ]);
        }

        // もともとつけていたタグ
        foreach ($tags as $tag){
            $this->assertDatabaseHas('book_mark_tags',[
                'book_mark_id' => $bookMark->id,
                'tag_id'     => $tag->id,
                'deleted_at' => null,
            ]);
        }
    }

    // 期待
    // * タイトル､urlが更新されている
    // * もとのブックマークについていたタグのidになにも変化がない
    // * 新しく紐づけたタグのidがbook_mark_tagsテーブルに保存される
    // 条件
    // * つけているタグの一部を消す
    public function test_bookMarkUpdate_タグの一部を消す()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        // ブックマークなどを作成
        $bookMark = BookMark::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(4)->create(['user_id' => $this->user->id]);

        foreach ($tags as $tag){
            BookMarkTag::create([
                "book_mark_id" => $bookMark->id,
                "tag_id"     => $tag->id
            ]);
        }

        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/bookmark/update/',[
            'bookMarkId'     => $bookMark->id,
            'bookMarkTitle'  => "更新titleタグ総入れ替え",
            'bookMarkUrl'   => "更新urlタグ総入れ替え" ,
            'tagList' => [$tags[2]->id,$tags[3]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // ブックマーク
        $this->assertDatabaseHas('book_marks',[
            'user_id'=> $this->user->id,
            'title'  => "更新titleタグ総入れ替え",
            'url'   => "更新urlタグ総入れ替え",
            'deleted_at' => null,
        ]);

        //タグ
        //残したタグ
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMark->id,
            'tag_id'     => $tags[2]->id,
            'deleted_at' => null,
        ]);

        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMark->id,
            'tag_id'     => $tags[3]->id,
            'deleted_at' => null,
        ]);

        //削除したタグ
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMark->id,
            'tag_id'     => $tags[0]->id,
            'deleted_at' => Carbon::now(),
        ]);

        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMark->id,
            'tag_id'     => $tags[1]->id,
            'deleted_at' => Carbon::now(),
        ]);
    }

    // 期待
    // * タイトル､urlが更新されている
    // * もとのブックマークのtag_id = null のデータを論理削除
    // * 新しく紐づけたタグのidがbook_mark_tagsテーブルに保存される
    // 条件
    // * タグがついてなかったブックマークにタグを付ける
    public function test_bookMarkUpdate_タグがついてなかったブックマークにタグを付ける()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        // ブックマークなどを作成
        $bookMark = BookMark::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);

        BookMarkTag::create([
            "book_mark_id" => $bookMark->id,
            "tag_id"     => null
        ]);

        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/bookmark/update/',[
            'bookMarkId'     => $bookMark->id,
            'bookMarkTitle'  => "更新titleタグがついてなかったブックマークにタグを付ける",
            'bookMarkUrl'   => "更新urlタグがついてなかったブックマークにタグを付ける" ,
            'tagList' => [$tags[0]->id,$tags[1]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // ブックマーク
        $this->assertDatabaseHas('book_marks',[
            'user_id'=> $this->user->id,
            'title'  => "更新titleタグがついてなかったブックマークにタグを付ける",
            'url'   => "更新urlタグがついてなかったブックマークにタグを付ける",
            'deleted_at' => null,
        ]);

        //タグ
        //追加したタグ
        foreach ($tags as $tag){
            $this->assertDatabaseHas('book_mark_tags',[
                'book_mark_id' => $bookMark->id,
                'tag_id'     => $tag->id,
                'deleted_at' => null,
            ]);
        }

        //削除したタグ
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMark->id,
            'tag_id'     => null,
            'deleted_at' => Carbon::now(),
        ]);
    }

    // 期待
    // * タイトル､urlが更新されている
    // * 新しくtag_id = null のデータを保存
    // * もともとついていたタグのdeleted_atに日付を入れる
    // 条件
    // * タグがついていたブックマークのタグをすべて消す
    public function test_bookMarkUpdate_タグがついていたブックマークのタグをすべて消す()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        // ブックマークなどを作成
        $bookMark = BookMark::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);

        foreach ($tags as $tag){
            BookMarkTag::create([
                "book_mark_id" => $bookMark->id,
                "tag_id"       => $tag->id
            ]);
        }

        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/bookmark/update/',[
            'bookMarkId'    => $bookMark->id,
            'bookMarkTitle' => "タグがついていたブックマークのタグをすべて消す",
            'bookMarkUrl'   => "タグがついていたブックマークのタグをすべて消す" ,
            'tagList' => [],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // ブックマーク
        $this->assertDatabaseHas('book_marks',[
            'user_id'=> $this->user->id,
            'title'  => "タグがついていたブックマークのタグをすべて消す",
            'url'    => "タグがついていたブックマークのタグをすべて消す",
            'deleted_at' => null,
        ]);

        //タグ
        //追加したタグ
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMark->id,
            'tag_id'     => null,
            'deleted_at' => null,
        ]);

        //削除したタグ
        foreach ($tags as $tag){
            $this->assertDatabaseHas('book_mark_tags',[
                'book_mark_id' => $bookMark->id,
                'tag_id'     => $tag->id,
                'deleted_at' => Carbon::now(),
            ]);
        }
    }


    // 期待
    // * タイトル､urlが更新されている
    // * tag_idがnullのまま
    // 条件
    // * タグがついてなかったブックマークにをタグなしで更新､再度タグなしで更新
    public function test_bookMarkUpdate_タグなし登録_タグなし更新_タグなし更新()
    {
        // carbonの時間固定

        // ブックマークなどを作成
        $bookMark = BookMark::factory()->create(['user_id' => $this->user->id]);

        BookMarkTag::create([
            "book_mark_id" => $bookMark->id,
            "tag_id"     => null
        ]);

        // 更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/bookmark/update/',[
            'bookMarkId'     => $bookMark->id,
            'bookMarkTitle'  => "更新",
            'bookMarkUrl'    => "更新" ,
            'tagList' => [],
        ]);

        // ステータス
        $response->assertStatus(200);

        // 再度更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/bookmark/update/',[
            'bookMarkId'     => $bookMark->id,
            'bookMarkTitle'  => "再度更新",
            'bookMarkUrl'    => "再度更新" ,
            'tagList' => [],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // ブックマーク
        $this->assertDatabaseHas('book_marks',[
            'user_id'=> $this->user->id,
            'title'  => "再度更新",
            'url'    => "再度更新",
            'deleted_at' => null,
        ]);

        //タグ
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMark->id,
            'tag_id'     => null,
            'deleted_at' => null,
        ]);
    }

    // 期待
    // * タイトル､urlが更新されている
    // * bookmarkTagが更新されている
    // 条件
    // * タグがついてなかったブックマークにをタグなしで更新､タグつけてで更新
    public function test_bookMarkUpdate_タグなし登録_タグなし更新_タグあり更新()
    {
        // carbonの時間固定

        // ブックマークなどを作成
        $bookMark = BookMark::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);

        BookMarkTag::create([
            "book_mark_id" => $bookMark->id,
            "tag_id"     => null
        ]);

        // 更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/bookmark/update/',[
            'bookMarkId'     => $bookMark->id,
            'bookMarkTitle'  => "更新",
            'bookMarkUrl'    => "更新" ,
            'tagList' => [],
        ]);

        // ステータス
        $response->assertStatus(200);

        // 再度更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/bookmark/update/',[
            'bookMarkId'     => $bookMark->id,
            'bookMarkTitle'  => "再度更新",
            'bookMarkUrl'    => "再度更新" ,
            'tagList' => [$tags[0]->id,$tags[1]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // ブックマーク
        $this->assertDatabaseHas('book_marks',[
            'user_id'=> $this->user->id,
            'title'  => "再度更新",
            'url'    => "再度更新",
            'deleted_at' => null,
        ]);

        //タグ
        //追加したタグ
        foreach ($tags as $tag){
            $this->assertDatabaseHas('book_mark_tags',[
                'book_mark_id' => $bookMark->id,
                'tag_id'     => $tag->id,
                'deleted_at' => null,
            ]);
        }

        //削除したタグ
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMark->id,
            'tag_id'     => null,
            'deleted_at' => Carbon::now(),
        ]);
    }

    // 期待
    // * タイトル､urlが更新されている
    // * bookmarkTagが更新されている
    // 条件
    // * タグがついてなかったブックマークにをタグありで更新､タグなしで更新
    public function test_bookMarkUpdate_タグなし登録_タグあり更新_タグなし更新()
    {
        // carbonの時間固定

        // ブックマークなどを作成
        $bookMark = BookMark::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);

        BookMarkTag::create([
            "book_mark_id" => $bookMark->id,
            "tag_id"     => null
        ]);

        // 更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/bookmark/update/',[
            'bookMarkId'     => $bookMark->id,
            'bookMarkTitle'  => "更新",
            'bookMarkUrl'    => "更新" ,
            'tagList' => [$tags[0]->id,$tags[1]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // 再度更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/bookmark/update/',[
            'bookMarkId'     => $bookMark->id,
            'bookMarkTitle'  => "再度更新",
            'bookMarkUrl'    => "再度更新" ,
            'tagList' => [],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // ブックマーク
        $this->assertDatabaseHas('book_marks',[
            'user_id'=> $this->user->id,
            'title'  => "再度更新",
            'url'    => "再度更新",
            'deleted_at' => null,
        ]);

        //タグ
        //追加したタグ
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMark->id,
            'tag_id'     => null,
            'deleted_at' => null,
        ]);

        //削除したタグ
        foreach ($tags as $tag){
            $this->assertDatabaseHas('book_mark_tags',[
                'book_mark_id' => $bookMark->id,
                'tag_id'     => $tag->id,
                'deleted_at' => Carbon::now(),
            ]);
        }
    }

    // 期待
    // * タイトル､urlが更新されている
    // * bookmarkTagが更新されている
    // 条件
    // * タグがついてなかったブックマークにをタグありで更新､タグありで更新
    public function test_bookMarkUpdate_タグなし登録_タグあり更新_タグあり更新()
    {
        // carbonの時間固定

        // ブックマークなどを作成
        $bookMark = BookMark::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(4)->create(['user_id' => $this->user->id]);

        BookMarkTag::create([
            "book_mark_id" => $bookMark->id,
            "tag_id"     => null
        ]);

        // 更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/bookmark/update/',[
            'bookMarkId'     => $bookMark->id,
            'bookMarkTitle'  => "更新",
            'bookMarkUrl'    => "更新" ,
            'tagList' => [$tags[0]->id,$tags[1]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // 再度更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/bookmark/update/',[
            'bookMarkId'     => $bookMark->id,
            'bookMarkTitle'  => "再度更新",
            'bookMarkUrl'    => "再度更新" ,
            'tagList' => [$tags[1]->id,$tags[2]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // ブックマーク
        $this->assertDatabaseHas('book_marks',[
            'user_id'=> $this->user->id,
            'title'  => "再度更新",
            'url'    => "再度更新",
            'deleted_at' => null,
        ]);

        //タグ
        //追加したタグ
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMark->id,
            'tag_id'     => $tags[1]->id,
            'deleted_at' => null,
        ]);

        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMark->id,
            'tag_id'     => $tags[2]->id,
            'deleted_at' => null,
        ]);

        //削除したタグ
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMark->id,
            'tag_id'     => $tags[0]->id,
            'deleted_at' => Carbon::now(),
        ]);

        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMark->id,
            'tag_id'     => null,
            'deleted_at' => Carbon::now(),
        ]);
    }

    // 期待
    // * タイトル､urlが更新されている
    // * bookmarkTagが更新されている
    // 条件
    // * タグがついてるブックマークにをタグなしで更新､再度タグなしで更新
    public function test_bookMarkUpdate_タグあり登録_タグなし更新_タグなし更新()
    {
        // carbonの時間固定

        // ブックマークなどを作成
        $bookMark = BookMark::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(4)->create(['user_id' => $this->user->id]);

        BookMarkTag::create([
            "book_mark_id" => $bookMark->id,
            "tag_id"     => $tags[0]->id
        ]);

        // 更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/bookmark/update/',[
            'bookMarkId'     => $bookMark->id,
            'bookMarkTitle'  => "更新",
            'bookMarkUrl'    => "更新" ,
            'tagList' => [],
        ]);

        // ステータス
        $response->assertStatus(200);

        // 再度更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/bookmark/update/',[
            'bookMarkId'     => $bookMark->id,
            'bookMarkTitle'  => "再度更新",
            'bookMarkUrl'    => "再度更新" ,
            'tagList' => [],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // ブックマーク
        $this->assertDatabaseHas('book_marks',[
            'user_id'=> $this->user->id,
            'title'  => "再度更新",
            'url'    => "再度更新",
            'deleted_at' => null,
        ]);

        //タグ
        //追加
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMark->id,
            'tag_id'     => null,
            'deleted_at' => null,
        ]);

        // 削除
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMark->id,
            'tag_id'     => $tags[0]->id,
            'deleted_at' => Carbon::now(),
        ]);
    }

    // 期待
    // * タイトル､urlが更新されている
    // * bookmarkTagが更新されている
    // 条件
    // * タグがついてたブックマークにをタグなしで更新､タグつけてで更新
    public function test_bookMarkUpdate_タグあり登録_タグなし更新_タグあり更新()
    {
        // carbonの時間固定

        // ブックマークなどを作成
        $bookMark = BookMark::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(3)->create(['user_id' => $this->user->id]);

        BookMarkTag::create([
            "book_mark_id" => $bookMark->id,
            "tag_id"       => $tags[0]->id,
        ]);

        // 更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/bookmark/update/',[
            'bookMarkId'     => $bookMark->id,
            'bookMarkTitle'  => "更新",
            'bookMarkUrl'    => "更新" ,
            'tagList' => [],
        ]);

        // ステータス
        $response->assertStatus(200);

        // 再度更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/bookmark/update/',[
            'bookMarkId'     => $bookMark->id,
            'bookMarkTitle'  => "再度更新",
            'bookMarkUrl'    => "再度更新" ,
            'tagList' => [$tags[1]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // ブックマーク
        $this->assertDatabaseHas('book_marks',[
            'user_id'=> $this->user->id,
            'title'  => "再度更新",
            'url'    => "再度更新",
            'deleted_at' => null,
        ]);

        //タグ
        //追加したタグ
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMark->id,
            'tag_id'     => $tags[1]->id,
            'deleted_at' => null,
        ]);

        //削除したタグ
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMark->id,
            'tag_id'     => null,
            'deleted_at' => Carbon::now(),
        ]);

        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMark->id,
            'tag_id'     => $tags[0]->id,
            'deleted_at' => Carbon::now(),
        ]);
    }

    // 期待
    // * タイトル､urlが更新されている
    // * bookmarkTagが更新されている
    // 条件
    // * タグがついてたブックマークにをタグありで更新､タグなしで更新
    public function test_bookMarkUpdate_タグあり登録_タグあり更新_タグなし更新()
    {
        // carbonの時間固定

        // ブックマークなどを作成
        $bookMark = BookMark::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(3)->create(['user_id' => $this->user->id]);

        BookMarkTag::create([
            "book_mark_id" => $bookMark->id,
            "tag_id"       => $tags[0]->id
        ]);

        // 更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/bookmark/update/',[
            'bookMarkId'     => $bookMark->id,
            'bookMarkTitle'  => "更新",
            'bookMarkUrl'    => "更新" ,
            'tagList' => [$tags[1]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // 再度更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/bookmark/update/',[
            'bookMarkId'     => $bookMark->id,
            'bookMarkTitle'  => "再度更新",
            'bookMarkUrl'    => "再度更新" ,
            'tagList' => [],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // ブックマーク
        $this->assertDatabaseHas('book_marks',[
            'user_id'=> $this->user->id,
            'title'  => "再度更新",
            'url'    => "再度更新",
            'deleted_at' => null,
        ]);

        //タグ
        //追加したタグ
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMark->id,
            'tag_id'     => null,
            'deleted_at' => null,
        ]);

        //削除したタグ
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMark->id,
            'tag_id'     => $tags[0]->id,
            'deleted_at' => Carbon::now(),
        ]);

        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMark->id,
            'tag_id'     => $tags[1]->id,
            'deleted_at' => Carbon::now(),
        ]);
    }

    // 期待
    // * タイトル､urlが更新されている
    // * bookmarkTagが更新されている
    // 条件
    // * タグがついてたブックマークにをタグありで更新､タグなしで更新
    public function test_bookMarkUpdate_タグあり登録_タグあり更新_タグあり更新()
    {
        // carbonの時間固定

        // ブックマークなどを作成
        $bookMark = BookMark::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(4)->create(['user_id' => $this->user->id]);

        BookMarkTag::create([
            "book_mark_id" => $bookMark->id,
            "tag_id"     => $tags[0]->id
        ]);

        // 更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/bookmark/update/',[
            'bookMarkId'     => $bookMark->id,
            'bookMarkTitle'  => "更新",
            'bookMarkUrl'    => "更新" ,
            'tagList' => [$tags[1]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // 再度更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/bookmark/update/',[
            'bookMarkId'     => $bookMark->id,
            'bookMarkTitle'  => "再度更新",
            'bookMarkUrl'    => "再度更新" ,
            'tagList' => [$tags[2]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // ブックマーク
        $this->assertDatabaseHas('book_marks',[
            'user_id'=> $this->user->id,
            'title'  => "再度更新",
            'url'    => "再度更新",
            'deleted_at' => null,
        ]);

        //タグ
        //追加したタグ

        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMark->id,
            'tag_id'     => $tags[2]->id,
            'deleted_at' => null,
        ]);

        //削除したタグ
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMark->id,
            'tag_id'     => $tags[0]->id,
            'deleted_at' => Carbon::now(),
        ]);

        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $bookMark->id,
            'tag_id'     => $tags[1]->id,
            'deleted_at' => Carbon::now(),
        ]);
    }
}