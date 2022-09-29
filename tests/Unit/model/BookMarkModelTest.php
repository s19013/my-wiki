<?php

namespace Tests\Unit\model;

use Tests\TestCase;
use App\Models\BookMark;
use App\Models\User;

// データベース関係で使う
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;


use Carbon\Carbon;

class BookMarkModelTest extends TestCase
{
    // テストしたらリセットする
    use RefreshDatabase;

    private $bookMarkModel;
    private $userId;

    public function setup():void
    {
        parent::setUp();
        $this->bookMarkModel = new BookMark();

        // ユーザーを用意
        $user = User::create([
            'name'     => "tester",
            'email'    => "hideya670@gmail.com",
            'password' => "gemini0522",
        ]);

        $this->userId = $user->id;
        // echo $this->userId;

        // ob_flush();
        // flush();
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    // public function test_example()
    // {
    //     $this->assertTrue(true);
    // }

    public function test_storeBookMark_ブックマークデータを登録した時のidを取ってこれるか(): void
    {
        $returnedId = $this->bookMarkModel->storeBookMark("testTitle","testUrl",$this->userId);

        // このテスト関数は一番最初に動く(このブックマークは一番最初に登録されるので) idは必ず[1]が帰ってくるはず
        // なんでって言われてもそれがdbの連番の仕様だからとしか答え切れない

        $this->assertEquals($returnedId,1);
    }

    // 期待
    // データがdbに登録されている
    // * 入力したタイトル
    // * 入力したurl
    //
    // 条件
    // タイトル入力済み

    public function test_storeBookMark_タイトルを入力(): void
    {
        // データを登録
        $this->bookMarkModel->storeBookMark("testTitle","testUrl",$this->userId);

        // 登録したデータがあるか確認
        $this->assertDatabaseHas('book_marks',[
            'title' => 'testTitle',
            'url'  => 'testUrl'
        ]);
    }

    // 期待
    // データがdbに登録されている
    // * タイトルには今の日時が登録されてる
    // * 入力したタイトル
    //
    // 条件
    // タイトルを入力しなかった
    public function test_storeBookMark_タイトルを入力しなかった()
    {
        //これで､Carbon::now()で呼び出される時間を固定化できるらしい
        Carbon::setTestNow(Carbon::now());

        $this->bookMarkModel->storeBookMark('',"testUrl",$this->userId);
        $this->assertDatabaseHas('book_marks',[
            'title' => Carbon::now(),
            'url'  => 'testUrl'
        ]);
    }

    // 期待
    // 指定したIDのブックマークが更新されている
    public function test_updateBookMark_指定したIDのブックマークが更新されている()
    {
        $bookMark = BookMark::factory()->create(['user_id' => $this->userId]);
        $this->bookMarkModel->updateBookMark($bookMark->id,"updatedTitle","updatedUrl");

        $this->assertDatabaseHas('book_marks',[
            'id'    => $bookMark->id,
            'title' => 'updatedTitle',
            'url'   => 'updatedUrl'
        ]);
    }

    // 期待
    // 指定したブックマークが論理削除されている
    public function test_deleteBookMark_指定したブックマークが論理削除されている()
    {
        Carbon::setTestNow(Carbon::now());

        $bookMark = BookMark::factory()->create(['user_id' => $this->userId]);
        $this->bookMarkModel->deleteBookMark($bookMark->id);

        $this->assertDatabaseHas('book_marks',[
            'id'          => $bookMark->id,
            'deleted_at'  => Carbon::now()
        ]);
    }

    // 指定したブックマークを取って来れるか
    public function test_serveBookMark_指定したブックマークを取って来れるか()
    {
        // ブックマーク登録
        $newBookMark = BookMark::factory()->create(['user_id' => $this->userId]);

        // ブックマーク取得
        $receivedBookMark = $this->bookMarkModel->serveBookMark($returnedId);

        //id
        $this->assertSame($newBookMark->id,$receivedBookMark->id);
        //title
        $this->assertSame("serveTitle",$receivedBookMark->title);
        //url
        $this->assertSame("serveUrl" ,$receivedBookMark->url);
    }

    // 期待
    // 関数checkBookMarkDeletedの帰り値がTrueである
    // 条件
    // 指定したブックマークが論理削除されていた
    public function test_checkBookMarkDeleted_削除済み()
    {
        $bookMark = BookMark::factory()->create(['user_id' => $this->userId]);
        // 削除する
        $this->bookMarkModel->deleteBookMark($bookMark->id);

        $this->assertTrue($this->bookMarkModel->checkBookMarkDeleted($bookMark->id));
    }

    // 期待
    // 関数checkBookMarkDeletedの帰り値がFalseである
    // 条件
    // 指定したブックマークが論理削除されていない
    public function test_checkBookMarkDeleted_削除してない()
    {
        $bookMark = BookMark::factory()->create(['user_id' => $this->userId]);

        $this->assertFalse($this->bookMarkModel->checkBookMarkDeleted($bookMark->id));
    }

    // 期待
    // 関数preventPeepの帰り値がTrueである
    // 条件
    // 指定したブックマークが指定したユーザーが作ったブックマークであった場合
    public function test_preventPeep_同一人物()
    {
        $bookMark = BookMark::factory()->create(['user_id' => $this->userId]);

        $this->assertTrue($this->bookMarkModel->preventPeep($bookMark->id,$this->userId));
    }

    // 期待
    // 関数preventPeepの帰り値がFalseである
    // 条件
    // 指定したブックマークが指定したユーザー以外が作ったブックマークだった場合
    public function test_preventPeep_不正()
    {
        $bookMark = BookMark::factory()->create(['user_id' => $this->userId]);

        $this->assertFalse($this->bookMarkModel->preventPeep($bookMark->id,$this->userId + 100));
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
        $this->assertTrue($this->bookMarkModel->isAllreadyExists($this->userId,"testUrl"));
    }

    // 期待
    // isAllreadyExistsがFalseを返す
    // 条件
    // 引数のurlがまだデータベースに登録されていない
    public function test_isAllreadyExists_データベースに登録されていない()
    {
        $this->assertFalse($this->bookMarkModel->isAllreadyExists($this->userId,"testUrl"));
    }

}
