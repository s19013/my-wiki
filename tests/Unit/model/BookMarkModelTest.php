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

    public function test_storeBookMark(): void
    {
        // データを登録
        $this->bookMarkModel->storeBookMark("testTitle","testUrl",$this->userId);

        // 登録したデータがあるか確認
        $this->assertDatabaseHas('book_marks',[
            'title' => 'testTitle',
            'url'  => 'testUrl'
        ]);
    }

    // このテストはCarbon::now()をつかうので時間帯によってはエラーがでるかもしれない
    public function test_storeBookMark_タイトルを入力しなかった場合()
    {
        //これで､Carbon::now()で呼び出される時間を固定化できるらしい
        Carbon::setTestNow(Carbon::now());

        $this->bookMarkModel->storeBookMark('',"testUrl",$this->userId);
        $this->assertDatabaseHas('book_marks',[
            'title' => Carbon::now(),
            'url'  => 'testUrl'
        ]);
    }

    public function test_記事データを登録した時のidを取ってこれるか(): void
    {
        $returnedId = $this->bookMarkModel->storeBookMark("testTitle","testUrl",$this->userId);

        $this->assertDatabaseHas('book_marks',[
            'id'    => $returnedId,
            'title' => 'testTitle',
            'url'  => 'testUrl'
        ]);
    }

    public function test_updateBookMark()
    {
        $returnedId = $this->bookMarkModel->storeBookMark("testTitle","testUrl",$this->userId);
        $this->bookMarkModel->updateBookMark($returnedId,"updatedTitle","updatedUrl");

        $this->assertDatabaseHas('book_marks',[
            'title' => 'updatedTitle',
            'url'  => 'updatedUrl'
        ]);
    }

    public function test_記事論理削除()
    {
        Carbon::setTestNow(Carbon::now());

        $returnedId = $this->bookMarkModel->storeBookMark("testTitle","testUrl",$this->userId);
        $this->bookMarkModel->deleteBookMark($returnedId);

        $this->assertDatabaseHas('book_marks',[
            'id' => $returnedId,
            'deleted_at'  => Carbon::now()
        ]);
    }

    public function test_serveBookMark(Type $var = null)
    {
        $returnedId = $this->bookMarkModel->storeBookMark("serveTitle","serveUrl",$this->userId);
        $book_mark = $this->bookMarkModel->serveBookMark($returnedId);

        //id
        $this->assertSame($returnedId,$book_mark->id);
        //title
        $this->assertSame("serveTitle",$book_mark->title);
        //url
        $this->assertSame("serveUrl" ,$book_mark->url);
    }

    public function test_checkBookMarkDeleted_削除済み()
    {
        $returnedId = $this->bookMarkModel->storeBookMark("testTitle","testUrl",$this->userId);
        // 削除する
        $this->bookMarkModel->deleteBookMark($returnedId);

        $this->assertTrue($this->bookMarkModel->checkBookMarkDeleted($returnedId));
    }

    public function test_checkBookMarkDeleted_削除してない()
    {
        $returnedId = $this->bookMarkModel->storeBookMark("testTitle","testUrl",$this->userId);

        $this->assertFalse($this->bookMarkModel->checkBookMarkDeleted($returnedId));
    }

    public function test_preventPeep_同一人物()
    {
        $returnedId = $this->bookMarkModel->storeBookMark("testTitle","testUrl",$this->userId);
        $this->assertTrue($this->bookMarkModel->preventPeep($returnedId,$this->userId));
    }

    public function test_preventPeep_不正()
    {
        $returnedId = $this->bookMarkModel->storeBookMark("testTitle","testUrl",$this->userId);
        $this->assertFalse($this->bookMarkModel->preventPeep($returnedId,$this->userId + 100));
    }

    public function test_isAllreadyExists_被っているブックマークがある()
    {
        $returnedId = $this->bookMarkModel->storeBookMark("testTitle","testUrl",$this->userId);
        $this->assertTrue($this->bookMarkModel->isAllreadyExists($this->userId,"testUrl"));
    }

    public function test_isAllreadyExists_ブックマークが一意である()
    {
        $this->assertFalse($this->bookMarkModel->isAllreadyExists($this->userId,"testUrl"));
    }

}
