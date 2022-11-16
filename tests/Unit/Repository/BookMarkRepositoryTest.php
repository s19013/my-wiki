<?php

namespace Tests\Unit\Repository;

use Tests\TestCase;
use App\Models\BookMark;
use App\Models\BookMarkTag;
use App\Models\Tag;
use App\Models\User;

use App\Repository\BookMarkRepository;

// データベース関係で使う
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;


use Carbon\Carbon;

class BookMarkRepositoryTest extends TestCase
{
    // テストしたらリセットする
    use RefreshDatabase;

    private $bookMarkModel;
    private $bookMarkRepository;
    private $userId;

    public function setup():void
    {
        parent::setUp();
        $this->bookMarkRepository = new BookMarkRepository();

        // ユーザーを用意
        $user = User::create([
            'name'     => "tester",
            'email'    => "hideya670@gmail.com",
            'password' => "gemini0522",
        ]);

        $this->userId = $user->id;

        // テストで使う時間を固定
        Carbon::setTestNow(Carbon::now());
    }

    // このテストはこの場所から動かしては行けない
    // 期待
    // ブックマークデータを登録した時にそのブックマークのIDを取ってこれるか
    public function test_store_ブックマークデータを登録した時にそのブックマークのidを取ってこれるか(): void
    {
        // 正しく動けば､ブックマークを保存したと同時にブックマークのIdが帰ってくる
        $returnedId = $this->bookMarkRepository->store("testTitle","testBody",$this->userId);

        // このテスト関数は一番最初に動く(このブックマークは一番最初に登録されるので) idは必ず[1]が帰ってくるはず
        // なんでって言われてもそれがdbの連番の仕様だからとしか答え切れない

        $this->assertEquals($returnedId,1);
    }

    // 期待
    // データがdbに登録されている
    // * 入力したタイトル
    // * 入力した本文
    //
    // 条件
    // タイトル入力済み
    public function test_store_タイトルを入力(): void
    {
        // データを登録
        $this->bookMarkRepository->store(
            "testTitle_test_storeBookMark_タイトルを入力",
            "testBody_test_storeBookMark_タイトルを入力" ,
            $this->userId
        );

        // 登録したデータがあるか確認
        $this->assertDatabaseHas('book_marks',[
            'title' => "testTitle_test_storeBookMark_タイトルを入力",
            'url'  => "testBody_test_storeBookMark_タイトルを入力",
        ]);
    }

    // 期待
    // データがdbに登録されている
    // * タイトルには今の日時が登録されてる
    // * 入力したタイトル
    //
    // 条件
    // タイトルを入力しなかった
    public function test_store_タイトルを入力しなかった()
    {
        //これで､Carbon::now()で呼び出される時間を固定化できるらしい
        Carbon::setTestNow(Carbon::now());

        // データを登録
        $this->bookMarkRepository->store(
            '',
            "testBody_test_storeBookMark_タイトルを入力しなかった",
            $this->userId
        );

        $this->assertDatabaseHas('book_marks',[
            'title' => Carbon::now(),
            'url'  => 'testBody_test_storeBookMark_タイトルを入力しなかった'
        ]);
    }

    // 期待
    // 指定したIDのブックマークが更新されている
    public function test_update_指定したIDのブックマークが更新されている()
    {
        $bookMark = BookMark::factory()->create(['user_id' => $this->userId]);

        // データを更新
        $this->bookMarkRepository->update($bookMark->id,"updatedTitle","updatedBody");

        $this->assertDatabaseHas('book_marks',[
            'id'    => $bookMark->id,
            'title' => 'updatedTitle',
            'url'  => 'updatedBody'
        ]);
    }

    // 期待
    // 更新したブックマークを再度更新する
    public function test_update_更新したブックマークを再度更新する()
    {
        $bookMark = BookMark::factory()->create(['user_id' => $this->userId]);

        // データを更新
        $this->bookMarkRepository->update($bookMark->id,"updatedTitle","updatedBody");

        $this->assertDatabaseHas('book_marks',[
            'id'    => $bookMark->id,
            'title' => 'updatedTitle',
            'url'  => 'updatedBody'
        ]);

        // データを再度更新
        $this->bookMarkRepository->update($bookMark->id,"updatedTitleAgain","updatedBodyAgain");

        $this->assertDatabaseHas('book_marks',[
            'id'    => $bookMark->id,
            'title' => 'updatedTitleAgain',
            'url'  => 'updatedBodyAgain'
        ]);
    }

    // 期待
    // 指定したブックマークが論理削除されている
    public function test_deleteBookMark_指定したブックマークが論理削除されている()
    {
        Carbon::setTestNow(Carbon::now());

        $bookMark = BookMark::factory()->create(['user_id' => $this->userId]);

        // 更新を削除
        $this->bookMarkRepository->delete($bookMark->id);

        // 論理削除されているか
        $this->assertDatabaseHas('book_marks',[
            'id' => $bookMark->id,
            'deleted_at'  => Carbon::now()
        ]);
    }

    // 指定したブックマークをとってこれているか
    public function test_serve_指定したブックマークをとってこれているか()
    {
        // ブックマークを登録する
        $newBookMark = BookMark::factory()->create(['user_id' => $this->userId]);

        // ブックマークを取ってくる
        $receivedBookMark = $this->bookMarkRepository->serve($newBookMark->id);

        //id
        $this->assertSame($newBookMark->id,$receivedBookMark->id);
        //title
        $this->assertSame($newBookMark->title,$receivedBookMark->title);
        //url
        $this->assertSame($newBookMark->url,$receivedBookMark->url);
    }

    // 期待
    // 関数checkBookMarkDeletedの帰り値がTrueである
    // 条件
    // 指定したブックマークが論理削除されていた
    public function test_isDeleted_削除済み()
    {
        $bookMark = BookMark::factory()->create(['user_id' => $this->userId]);

        // 削除する
        $this->bookMarkRepository->delete($bookMark->id);

        $this->assertTrue($this->bookMarkRepository->isDeleted($bookMark->id));
    }

    // 期待
    // 関数checkBookMarkDeletedの帰り値がFalseである
    // 条件
    // 指定したブックマークが論理削除されていない
    public function test_isDeleted_削除してない()
    {
        $bookMark = BookMark::factory()->create(['user_id' => $this->userId]);

        $this->assertFalse($this->bookMarkRepository->isDeleted($bookMark->id));
    }

    // 期待
    // 関数isSameUserの帰り値がTrueである
    // 条件
    // 指定したブックマークが指定したユーザーが作ったブックマークであった場合
    public function test_isSameUser_同一人物()
    {
        $bookMark = BookMark::factory()->create(['user_id' => $this->userId]);

        $this->assertTrue($this->bookMarkRepository->isSameUser($bookMark->id,$this->userId));
    }

    // 期待
    // 関数isSameUserの帰り値がFalseである
    // 条件
    // 指定したブックマークが指定したユーザー以外が作ったブックマークだった場合
    public function test_isSameUser_不正()
    {
        $bookMark = BookMark::factory()->create(['user_id' => $this->userId]);

        $this->assertFalse($this->bookMarkRepository->isSameUser($bookMark->id,$this->userId + 100));
    }

    // 期待
    // isAllreadyExistsがTrueを返す
    // 条件
    // 引数のurlがすでにデータベースに登録されている
    public function test_isAllreadyExists_すでにデータベースに登録されている()
    {
        BookMark::factory()->create([
            'url'     => "testUrl",
            'user_id' => $this->userId
        ]);
        $this->assertTrue($this->bookMarkRepository->isAllreadyExists($this->userId,"testUrl"));
    }

    // 期待
    // isAllreadyExistsがFalseを返す
    // 条件
    // 引数のurlがまだデータベースに登録されていない
    public function test_isAllreadyExists_データベースに登録されていない()
    {
        $this->assertFalse($this->bookMarkRepository->isAllreadyExists($this->userId,"testUrl"));
    }
}
