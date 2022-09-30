<?php

namespace Tests\Unit\model;

use Tests\TestCase;
use App\Models\BookMark;
use App\Models\BookMarkTag;
use App\Models\Tag;
use App\Models\User;

// データベース関係で使う
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;


use Carbon\Carbon;

class BookMarkModelTest extends TestCase
{
    // テストしたらリセットする
    use RefreshDatabase;

    private $bookmarkModel;
    private $userId;

    public function setup():void
    {
        parent::setUp();
        $this->bookmarkModel = new BookMark();

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
        $returnedId = $this->bookmarkModel->storeBookMark("testTitle","testUrl",$this->userId);

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
        $this->bookmarkModel->storeBookMark("testTitle","testUrl",$this->userId);

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

        $this->bookmarkModel->storeBookMark('',"testUrl",$this->userId);
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
        $this->bookmarkModel->updateBookMark($bookMark->id,"updatedTitle","updatedUrl");

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
        $this->bookmarkModel->deleteBookMark($bookMark->id);

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
        $receivedBookMark = $this->bookmarkModel->serveBookMark($newBookMark->id);

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
    public function test_checkBookMarkDeleted_削除済み()
    {
        $bookMark = BookMark::factory()->create(['user_id' => $this->userId]);
        // 削除する
        $this->bookmarkModel->deleteBookMark($bookMark->id);

        $this->assertTrue($this->bookmarkModel->checkBookMarkDeleted($bookMark->id));
    }

    // 期待
    // 関数checkBookMarkDeletedの帰り値がFalseである
    // 条件
    // 指定したブックマークが論理削除されていない
    public function test_checkBookMarkDeleted_削除してない()
    {
        $bookMark = BookMark::factory()->create(['user_id' => $this->userId]);

        $this->assertFalse($this->bookmarkModel->checkBookMarkDeleted($bookMark->id));
    }

    // 期待
    // 関数preventPeepの帰り値がTrueである
    // 条件
    // 指定したブックマークが指定したユーザーが作ったブックマークであった場合
    public function test_preventPeep_同一人物()
    {
        $bookMark = BookMark::factory()->create(['user_id' => $this->userId]);

        $this->assertTrue($this->bookmarkModel->preventPeep($bookMark->id,$this->userId));
    }

    // 期待
    // 関数preventPeepの帰り値がFalseである
    // 条件
    // 指定したブックマークが指定したユーザー以外が作ったブックマークだった場合
    public function test_preventPeep_不正()
    {
        $bookMark = BookMark::factory()->create(['user_id' => $this->userId]);

        $this->assertFalse($this->bookmarkModel->preventPeep($bookMark->id,$this->userId + 100));
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
        $this->assertTrue($this->bookmarkModel->isAllreadyExists($this->userId,"testUrl"));
    }

    // 期待
    // isAllreadyExistsがFalseを返す
    // 条件
    // 引数のurlがまだデータベースに登録されていない
    public function test_isAllreadyExists_データベースに登録されていない()
    {
        $this->assertFalse($this->bookmarkModel->isAllreadyExists($this->userId,"testUrl"));
    }

        // 期待
    // 何ページ分あるか数える
    // 一度に取得する件数は10件を超えない
    // 条件
    // すべてのデータの合計数が10以上
    public function test_bookmarkSearch_すべてのデータの合計数が10以上()
    {
        //
        Carbon::setTestNow(Carbon::now());

        //テストユーザーの記事
        BookMark::factory()->count(25)->create(['user_id' => $this->userId]);

        $response = $this->bookmarkModel->searchBookMark(
            userId:$this->userId,
            bookMarkToSearch:'',
            currentPage:1,
            tagList:null,
            searchTarget:'title'
        );

        $bookmarkList = $response['bookMarkList'];
        $pageCount   = $response['pageCount'];

        //帰ってきたbookmarkListの数を数える(10個以上あっても一度に10個までしか返さない)
        $this->assertCount(10,$bookmarkList);

        // 何ページ分あるか確認
        // 今回は全部で20件ある,1ページ10件までなので,10件 + 10件 + 5件の3ページに分かれる
        $this->assertEquals(3, $pageCount);
    }

    // 期待
    // 何ページ分あるか数える
    // 一度に取得する件数は10件を超えない
    // 条件
    // すべてのデータの合計数が10以下
    public function test_bookmarkSearch_すべてのデータの合計数が10以下()
    {
        //
        Carbon::setTestNow(Carbon::now());

        //テストユーザーの記事
        BookMark::factory()->count(5)->create(['user_id' => $this->userId]);

        $response = $this->bookmarkModel->searchBookMark(
            userId:$this->userId,
            bookMarkToSearch:'',
            currentPage:1,
            tagList:null,
            searchTarget:'title'
        );

        $bookmarkList = $response['bookMarkList'];
        $pageCount   = $response['pageCount'];

        //帰ってきたbookmarkListの数を数える
        $this->assertCount(5,$bookmarkList);

        // 何ページ分あるか確認
        $this->assertEquals(1, $pageCount);
    }

    // 期待
    // * 指定したユーザーの記事だけを取ってくる
    // * 削除した記事は取ってこない
    // 条件
    // * タグ指定なし
    // * タイトル検索
    // * キーワードなし
    public function test_bookmarkSearch_タグ指定なし_タイトル検索_キーワードなし()
    {
        //
        Carbon::setTestNow(Carbon::now());

        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーの記事
        $bookmarks = BookMark::factory()->count(20)->create(['user_id' => $this->userId]);

        //わざと記事を消す
        BookMark::where('id','=',$bookmarks[0]->id)
        ->update(['deleted_at' => Carbon::now()]);
        BookMark::where('id','=',$bookmarks[1]->id)
        ->update(['deleted_at' => Carbon::now()]);

        // ダミーの記事
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        $response = $this->bookmarkModel->searchBookMark(
            userId:$this->userId,
            bookMarkToSearch:'',
            currentPage:1,
            tagList:null,
            searchTarget:'title'
        );

        $bookmarkList = $response['bookMarkList'];

        $idList = [];
        foreach ($bookmarkList as $data){ array_push($idList,$data->id); }

        // 全部指定したユーザーの記事か
        foreach ($bookmarkList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除した記事は含んでないか
        $this->assertNotContains($bookmarks[0]->id,$idList);
        $this->assertNotContains($bookmarks[1]->id,$idList);
    }

    // 期待
    //  * titelカラムに"apple"と"make"の文字列が入っているデータをとってくる
    //  * 指定したユーザーの記事だけを取ってくる
    //  * 削除した記事は取ってこない
    // 条件
    // * タグ指定なし
    // * タイトル検索
    // * キーワードあり
    public function test_bookmarkSearch_タグ指定なし_タイトル検索_キーワードあり()
    {
        //
        Carbon::setTestNow(Carbon::now());


        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーの記事
        $bookmarks = BookMark::factory()->count(10)->create(['user_id' => $this->userId]);

        //検索で引っかかるような記事作成
        $hitBookMark1 = BookMark::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make applePie'
        ]);

        $hitBookMark2 = BookMark::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make appleTea'
        ]);

        // わざと消す記事
        $deleteBookMark = BookMark::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make appleJam'
        ]);

        //わざと記事を消す
        BookMark::where('id','=',$deleteBookMark->id)->update(['deleted_at' => Carbon::now()]);

        // ダミーの記事
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        $response = $this->bookmarkModel->searchBookMark(
            userId:$this->userId,
            bookMarkToSearch:"apple make",
            currentPage:1,
            tagList:null,
            searchTarget:'title'
        );

        $bookmarkList = $response['bookMarkList'];

        $idList = [];
        foreach ($bookmarkList as $data){ array_push($idList,$data->id); }


        // 全部指定したユーザーの記事か
        foreach ($bookmarkList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除した記事は含んでないか
        $this->assertNotContains($deleteBookMark->id,$idList);

        // ヒットするはずの記事を取ってきているか
        $this->assertContains($hitBookMark1->id, $idList);
        $this->assertContains($hitBookMark2->id, $idList);

    }

    // 期待
    //  * 指定したタグがついている記事をとってくる
    //  * 指定したユーザーの記事を取ってくる
    //  * 削除した記事は取ってこない
    // 条件
    // * タグ指定:'recipe'
    // * タイトル検索
    // * キーワードなし
    public function test_bookmarkSearch_タグ指定あり_タイトル検索_キーワードなし_()
    {
        //
        Carbon::setTestNow(Carbon::now());

        // 検索でヒットするはずのタグを作る
        $hitTag = Tag::factory()->create([
            'name'    => 'recipe',
            'user_id' => $this->userId
        ]);

        //検索で引っかかるような記事作成
        $hitBookMark1 = BookMark::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make applePie'
        ]);

        BookMarkTag::create([
            'book_mark_id' => $hitBookMark1->id,
            'tag_id'     => $hitTag->id,
        ]);

        // ダミー
        $hitBookMark2 = BookMark::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make berryPie'
        ]);

        $dammyTag1 = Tag::factory()->create([
            'name'    => 'berry',
            'user_id' => $this->userId
        ]);

        BookMarkTag::create([
            'book_mark_id' => $hitBookMark2->id,
            'tag_id'     => $dammyTag1->id,
        ]);

        BookMarkTag::create([
            'book_mark_id' => $hitBookMark2->id,
            'tag_id'     => $hitTag->id,
        ]);

        // わざと消す記事
        $deleteBookMark = BookMark::factory()->create([
            'user_id' => $this->userId,
            'title'   => 'how to make peachPie'
        ]);

        $dammyTag2 = Tag::factory()->create([
            'name'    => 'peach',
            'user_id' => $this->userId
        ]);

        BookMarkTag::create([
            'book_mark_id' => $deleteBookMark->id,
            'tag_id'     => $dammyTag2->id,
        ]);

        BookMarkTag::create([
            'book_mark_id' => $deleteBookMark->id,
            'tag_id'     => $hitTag->id,
        ]);

        //わざと記事を消す
        BookMark::where('id','=',$deleteBookMark->id)
        ->update(['deleted_at' => Carbon::now()]);

        $response = $this->bookmarkModel->searchBookMark(
            userId:$this->userId,
            bookMarkToSearch:"",
            currentPage:1,
            tagList:[$hitTag->id],
            searchTarget:'title'
        );

        $bookmarkList = $response['bookMarkList'];

        $idList = [];
        foreach ($bookmarkList as $data){ array_push($idList,$data->id); }

        // 全部指定したユーザーの記事か
        foreach ($bookmarkList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除した記事は含んでないか
        $this->assertNotContains($deleteBookMark->id,$idList);

        // ヒットするはずの記事を取ってきているか
        $this->assertContains($hitBookMark1->id, $idList);
    }

    // 期待
    //  * titelカラムに"make"の文字列が入っているデータをとってくる
    //  * 指定したタグがついている記事をとってくる
    //  * 指定したユーザーの記事だけを取ってくる
    //  * 削除した記事は取ってこない
    // 条件
    // * タグ指定:'recipe'
    // * タイトル検索
    // * キーワードなし
    public function test_bookmarkSearch_タグ指定あり_タイトル検索_キーワードあり()
    {
        //
        Carbon::setTestNow(Carbon::now());


        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーの記事
        $bookmarks = BookMark::factory()->count(10)->create(['user_id' => $this->userId]);


        //検索で引っかかるような記事作成
        $hitBookMark = BookMark::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make applePie'
        ]);

        $hitTag = Tag::factory()->create([
            'name'    => 'apple',
            'user_id' => $this->userId
        ]);

        // 他のダミーにも付けるタグ
        $tag = Tag::factory()->create([
            'name'    => 'recipe',
            'user_id' => $this->userId
        ]);

        BookMarkTag::create([
            'book_mark_id' => $hitBookMark->id,
            'tag_id'     => $hitTag->id,
        ]);

        BookMarkTag::create([
            'book_mark_id' => $hitBookMark->id,
            'tag_id'     => $tag->id,
        ]);

        // ダミー
        $dammyBookMark = BookMark::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make berryPie'
        ]);

        $dammyTag1 = Tag::factory()->create([
            'name'    => 'berry',
            'user_id' => $this->userId
        ]);

        BookMarkTag::create([
            'book_mark_id' => $dammyBookMark->id,
            'tag_id'     => $dammyTag1->id,
        ]);

        BookMarkTag::create([
            'book_mark_id' => $dammyBookMark->id,
            'tag_id'     => $tag->id,
        ]);

        // わざと消す記事
        $deleteBookMark = BookMark::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make peachPie'
        ]);

        $dammyTag2 = Tag::factory()->create([
            'name'    => 'berry',
            'user_id' => $this->userId
        ]);

        BookMarkTag::create([
            'book_mark_id' => $deleteBookMark->id,
            'tag_id'     => $dammyTag2->id,
        ]);

        BookMarkTag::create([
            'book_mark_id' => $deleteBookMark->id,
            'tag_id'     => $tag->id,
        ]);

        //他のダミー記事
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        Tag::factory()->count(2)->create(['user_id' => $anotherUsers[0]->id]);
        Tag::factory()->count(2)->create(['user_id' => $anotherUsers[0]->id]);


        //わざと記事を消す
        BookMark::where('id','=',$deleteBookMark->id)
        ->update(['deleted_at' => Carbon::now()]);

        $response = $this->bookmarkModel->searchBookMark(
            userId:$this->userId,
            bookMarkToSearch:"make",
            currentPage:1,
            tagList:[$hitTag->id],
            searchTarget:'title'
        );

        $bookmarkList = $response['bookMarkList'];

        $idList = [];
        foreach ($bookmarkList as $data){ array_push($idList,$data->id); }

        // 全部指定したユーザーの記事か
        foreach ($bookmarkList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除した記事は含んでないか
        $this->assertNotContains($deleteBookMark->id,$idList);

        // ヒットするはずの記事を取ってきているか
        $this->assertContains($hitBookMark->id, $idList);
    }

    // 期待
    // * 指定したユーザーの記事だけを取ってくる
    // * 削除した記事は取ってこない
    // 条件
    // * タグ指定なし
    // * 本文検索
    // * キーワードなし
    public function test_bookmarkSearch_タグ指定なし_本文検索_キーワードなし()
    {
        //
        Carbon::setTestNow(Carbon::now());

        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーの記事
        $bookmarks = BookMark::factory()->count(20)->create(['user_id' => $this->userId]);

        //わざと記事を消す
        BookMark::where('id','=',$bookmarks[0]->id)
        ->update(['deleted_at' => Carbon::now()]);
        BookMark::where('id','=',$bookmarks[1]->id)
        ->update(['deleted_at' => Carbon::now()]);

        // ダミーの記事
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        $response = $this->bookmarkModel->searchBookMark(
            userId:$this->userId,
            bookMarkToSearch:'',
            currentPage:1,
            tagList:null,
            searchTarget:'url'
        );

        $bookmarkList = $response['bookMarkList'];

        $idList = [];
        foreach ($bookmarkList as $data){ array_push($idList,$data->id); }

        // 全部指定したユーザーの記事か
        foreach ($bookmarkList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除した記事は含んでないか
        $this->assertNotContains($bookmarks[0]->id,$idList);
        $this->assertNotContains($bookmarks[1]->id,$idList);
    }

    // 期待
    //  * titelカラムに"apple"と"make"の文字列が入っているデータをとってくる
    //  * 指定したユーザーの記事だけを取ってくる
    //  * 削除した記事は取ってこない
    // 条件
    // * タグ指定なし
    // * 本文検索
    // * キーワードあり
    public function test_bookmarkSearch_タグ指定なし_本文検索_キーワードあり()
    {
        //
        Carbon::setTestNow(Carbon::now());


        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーの記事
        $bookmarks = BookMark::factory()->count(10)->create(['user_id' => $this->userId]);

        //検索で引っかかるような記事作成
        $hitBookMark1 = BookMark::factory()->create([
            'user_id' => $this->userId,
            'url' => 'how to make applePie'
        ]);

        $hitBookMark2 = BookMark::factory()->create([
            'user_id' => $this->userId,
            'url' => 'how to make appleTea'
        ]);

        // わざと消す記事
        $deleteBookMark = BookMark::factory()->create([
            'user_id' => $this->userId,
            'url' => 'how to make appleJam'
        ]);

        //わざと記事を消す
        BookMark::where('id','=',$deleteBookMark->id)->update(['deleted_at' => Carbon::now()]);

        // ダミーの記事
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        $response = $this->bookmarkModel->searchBookMark(
            userId:$this->userId,
            bookMarkToSearch:"apple make",
            currentPage:1,
            tagList:null,
            searchTarget:'url'
        );

        $bookmarkList = $response['bookMarkList'];

        $idList = [];
        foreach ($bookmarkList as $data){ array_push($idList,$data->id); }


        // 全部指定したユーザーの記事か
        foreach ($bookmarkList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除した記事は含んでないか
        $this->assertNotContains($deleteBookMark->id,$idList);

        // ヒットするはずの記事を取ってきているか
        $this->assertContains($hitBookMark1->id, $idList);
        $this->assertContains($hitBookMark2->id, $idList);

    }

    // 期待
    //  * 指定したタグがついている記事をとってくる
    //  * 指定したユーザーの記事を取ってくる
    //  * 削除した記事は取ってこない
    // 条件
    // * タグ指定:'recipe'
    // * 本文検索
    // * キーワードなし
    public function test_bookmarkSearch_タグ指定あり_本文検索_キーワードなし_()
    {
        //
        Carbon::setTestNow(Carbon::now());

        // 検索でヒットするはずのタグを作る
        $hitTag = Tag::factory()->create([
            'name'    => 'recipe',
            'user_id' => $this->userId
        ]);

        //検索で引っかかるような記事作成
        $hitBookMark1 = BookMark::factory()->create([
            'user_id' => $this->userId,
            'url' => 'how to make applePie'
        ]);

        BookMarkTag::create([
            'book_mark_id' => $hitBookMark1->id,
            'tag_id'     => $hitTag->id,
        ]);

        // ダミー
        $hitBookMark2 = BookMark::factory()->create([
            'user_id' => $this->userId,
            'url' => 'how to make berryPie'
        ]);

        $dammyTag1 = Tag::factory()->create([
            'name'    => 'berry',
            'user_id' => $this->userId
        ]);

        BookMarkTag::create([
            'book_mark_id' => $hitBookMark2->id,
            'tag_id'     => $dammyTag1->id,
        ]);

        BookMarkTag::create([
            'book_mark_id' => $hitBookMark2->id,
            'tag_id'     => $hitTag->id,
        ]);

        // わざと消す記事
        $deleteBookMark = BookMark::factory()->create([
            'user_id' => $this->userId,
            'url'   => 'how to make peachPie'
        ]);

        $dammyTag2 = Tag::factory()->create([
            'name'    => 'peach',
            'user_id' => $this->userId
        ]);

        BookMarkTag::create([
            'book_mark_id' => $deleteBookMark->id,
            'tag_id'     => $dammyTag2->id,
        ]);

        BookMarkTag::create([
            'book_mark_id' => $deleteBookMark->id,
            'tag_id'     => $hitTag->id,
        ]);

        //わざと記事を消す
        BookMark::where('id','=',$deleteBookMark->id)
        ->update(['deleted_at' => Carbon::now()]);

        $response = $this->bookmarkModel->searchBookMark(
            userId:$this->userId,
            bookMarkToSearch:"",
            currentPage:1,
            tagList:[$hitTag->id],
            searchTarget:'url'
        );

        $bookmarkList = $response['bookMarkList'];

        $idList = [];
        foreach ($bookmarkList as $data){ array_push($idList,$data->id); }

        // 全部指定したユーザーの記事か
        foreach ($bookmarkList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除した記事は含んでないか
        $this->assertNotContains($deleteBookMark->id,$idList);

        // ヒットするはずの記事を取ってきているか
        $this->assertContains($hitBookMark1->id, $idList);
    }

    // 期待
    //  * titelカラムに"make"の文字列が入っているデータをとってくる
    //  * 指定したタグがついている記事をとってくる
    //  * 指定したユーザーの記事だけを取ってくる
    //  * 削除した記事は取ってこない
    // 条件
    // * タグ指定:'recipe'
    // * 本文検索
    // * キーワードなし
    public function test_bookmarkSearch_タグ指定あり_本文検索_キーワードあり()
    {
        //
        Carbon::setTestNow(Carbon::now());


        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーの記事
        $bookmarks = BookMark::factory()->count(10)->create(['user_id' => $this->userId]);


        //検索で引っかかるような記事作成
        $hitBookMark = BookMark::factory()->create([
            'user_id' => $this->userId,
            'url' => 'how to make applePie'
        ]);

        $hitTag = Tag::factory()->create([
            'name'    => 'apple',
            'user_id' => $this->userId
        ]);

        // 他のダミーにも付けるタグ
        $tag = Tag::factory()->create([
            'name'    => 'recipe',
            'user_id' => $this->userId
        ]);

        BookMarkTag::create([
            'book_mark_id' => $hitBookMark->id,
            'tag_id'     => $hitTag->id,
        ]);

        BookMarkTag::create([
            'book_mark_id' => $hitBookMark->id,
            'tag_id'     => $tag->id,
        ]);

        // ダミー
        $dammyBookMark = BookMark::factory()->create([
            'user_id' => $this->userId,
            'url' => 'how to make berryPie'
        ]);

        $dammyTag1 = Tag::factory()->create([
            'name'    => 'berry',
            'user_id' => $this->userId
        ]);

        BookMarkTag::create([
            'book_mark_id' => $dammyBookMark->id,
            'tag_id'     => $dammyTag1->id,
        ]);

        BookMarkTag::create([
            'book_mark_id' => $dammyBookMark->id,
            'tag_id'     => $tag->id,
        ]);

        // わざと消す記事
        $deleteBookMark = BookMark::factory()->create([
            'user_id' => $this->userId,
            'url' => 'how to make peachPie'
        ]);

        $dammyTag2 = Tag::factory()->create([
            'name'    => 'berry',
            'user_id' => $this->userId
        ]);

        BookMarkTag::create([
            'book_mark_id' => $deleteBookMark->id,
            'tag_id'     => $dammyTag2->id,
        ]);

        BookMarkTag::create([
            'book_mark_id' => $deleteBookMark->id,
            'tag_id'     => $tag->id,
        ]);

        //他のダミー記事
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        Tag::factory()->count(2)->create(['user_id' => $anotherUsers[0]->id]);
        Tag::factory()->count(2)->create(['user_id' => $anotherUsers[0]->id]);


        //わざと記事を消す
        BookMark::where('id','=',$deleteBookMark->id)
        ->update(['deleted_at' => Carbon::now()]);

        $response = $this->bookmarkModel->searchBookMark(
            userId:$this->userId,
            bookMarkToSearch:"make",
            currentPage:1,
            tagList:[$hitTag->id],
            searchTarget:'url'
        );

        $bookmarkList = $response['bookMarkList'];

        $idList = [];
        foreach ($bookmarkList as $data){ array_push($idList,$data->id); }

        // 全部指定したユーザーの記事か
        foreach ($bookmarkList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除した記事は含んでないか
        $this->assertNotContains($deleteBookMark->id,$idList);

        // ヒットするはずの記事を取ってきているか
        $this->assertContains($hitBookMark->id, $idList);
    }

}
