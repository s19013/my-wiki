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

class BookMarkRepository_SearchTest extends TestCase
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

    // 期待
    // 何ページ分あるか数える
    // 一度に取得する件数は10件を超えない
    // 条件
    // すべてのデータの合計数が10以上
    public function test_Search_すべてのデータの合計数が10以上()
    {
        //
        Carbon::setTestNow(Carbon::now());

        //テストユーザーのブックマーク
        BookMark::factory()->count(25)->create(['user_id' => $this->userId]);

        $response = $this->bookMarkRepository->search(
            userId:$this->userId,
            keyword:'',
            page:1,
            tagList:null,
            searchTarget:'title'
        );

        //帰ってきたbookMarkListの数を数える(10個以上あっても一度に10個までしか返さない)
        $this->assertCount(10,$response['data']);

        // 何ページ分あるか確認
        // 今回は全部で20件ある,1ページ10件までなので,10件 + 10件 + 5件の3ページに分かれる
        $this->assertEquals(3, $response['last_page']);
    }

    // 期待
    // 何ページ分あるか数える
    // 一度に取得する件数は10件を超えない
    // 条件
    // すべてのデータの合計数が10以下
    public function test_Search_すべてのデータの合計数が10以下()
    {
        //
        Carbon::setTestNow(Carbon::now());

        //テストユーザーのブックマーク
        BookMark::factory()->count(5)->create(['user_id' => $this->userId]);

        $response = $this->bookMarkRepository->search(
            userId:$this->userId,
            keyword:'',
            page:1,
            tagList:null,
            searchTarget:'title'
        );

        //帰ってきたbookMarkListの数を数える
        $this->assertCount(5,$response['data']);

        // 何ページ分あるか確認
        $this->assertEquals(1, $response['last_page']);
    }

    // 期待
    // 11件目から20件目のデータを取得する
    //
    public function test_Search_11件目から20件目のデータを取得する()
    {
        //
        Carbon::setTestNow(Carbon::now());

        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        for ($i=0; $i <=30 ; $i++) {
            BookMark::create([
                'title'   => "title ${i}",
                'url'    => "url ${i}",
                'user_id' => $this->userId
            ]);
        }

        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        $response = $this->bookMarkRepository->search(
            userId:$this->userId,
            keyword:'',
            page:2,
            tagList:null,
            searchTarget:'title'
        );

        $bookMarkList = $response['data'];

        //帰ってきたbookMarkListの数を数える(10個以上あっても一度に10個までしか返さない)
        for ($i=10; $i <20 ; $i++) {
            $this->assertSame($bookMarkList[$i - 10]['title'],"title ${i}");
        }
    }

    // 期待
    // * 指定したユーザーのブックマークだけを取ってくる
    // * 削除したブックマークは取ってこない
    // 条件
    // * タグ指定なし
    // * タイトル検索
    // * キーワードなし
    public function test_Search_タグ指定なし_タイトル検索_キーワードなし()
    {
        //
        Carbon::setTestNow(Carbon::now());

        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーのブックマーク
        $bookMarks = BookMark::factory()->count(20)->create(['user_id' => $this->userId]);

        //わざとブックマークを消す
        BookMark::where('id','=',$bookMarks[0]->id)
        ->update(['deleted_at' => Carbon::now()]);
        BookMark::where('id','=',$bookMarks[1]->id)
        ->update(['deleted_at' => Carbon::now()]);

        // ダミーのブックマーク
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        $response = $this->bookMarkRepository->search(
            userId:$this->userId,
            keyword:'',
            page:1,
            tagList:null,
            searchTarget:'title'
        );

        $bookMarkList = $response['data'];

        // dd($bookMarkList);

        $idList = [];
        foreach ($bookMarkList as $data){ array_push($idList,$data['id']); }

        // 全部指定したユーザーのブックマークか
        foreach ($bookMarkList as $data){ $this->assertEquals($data['user_id'],$this->userId); }

        // 削除したブックマークは含んでないか
        $this->assertNotContains($bookMarks[0]['id'],$idList);
        $this->assertNotContains($bookMarks[1]['id'],$idList);
    }

    // 期待
    //  * titelカラムに"apple"と"make"の文字列が入っているデータをとってくる
    //  * 指定したユーザーのブックマークだけを取ってくる
    //  * 削除したブックマークは取ってこない
    // 条件
    // * タグ指定なし
    // * タイトル検索
    // * キーワードあり
    public function test_Search_タグ指定なし_タイトル検索_キーワードあり()
    {
        //
        Carbon::setTestNow(Carbon::now());


        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーのブックマーク
        $bookMarks = BookMark::factory()->count(10)->create(['user_id' => $this->userId]);

        //検索で引っかかるようなブックマーク作成
        $hitBookMark1 = BookMark::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make applePie'
        ]);

        $hitBookMark2 = BookMark::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make appleTea'
        ]);

        // わざと消すブックマーク
        $deleteBookMark = BookMark::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make appleJam'
        ]);

        //わざとブックマークを消す
        BookMark::where('id','=',$deleteBookMark->id)->update(['deleted_at' => Carbon::now()]);

        // ダミーのブックマーク
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        $response = $this->bookMarkRepository->search(
            userId:$this->userId,
            keyword:"apple make",
            page:1,
            tagList:null,
            searchTarget:'title'
        );

        $bookMarkList = $response['data'];

        $idList = [];
        foreach ($bookMarkList as $data){ array_push($idList,$data['id']); }


        // 全部指定したユーザーのブックマークか
        foreach ($bookMarkList as $data){ $this->assertEquals($data['user_id'],$this->userId); }

        // 削除したブックマークは含んでないか
        $this->assertNotContains($deleteBookMark['id'],$idList);

        // ヒットするはずのブックマークを取ってきているか
        $this->assertContains($hitBookMark1['id'], $idList);
        $this->assertContains($hitBookMark2['id'], $idList);

    }

    // 期待
    //  * 指定したタグがついているブックマークをとってくる
    //  * 指定したユーザーのブックマークを取ってくる
    //  * 削除したブックマークは取ってこない
    // 条件
    // * タグ指定:'recipe'
    // * タイトル検索
    // * キーワードなし
    public function test_Search_タグ指定あり_タイトル検索_キーワードなし_()
    {
        //
        Carbon::setTestNow(Carbon::now());

        // 検索でヒットするはずのタグを作る
        $hitTag = Tag::factory()->create([
            'name'    => 'recipe',
            'user_id' => $this->userId
        ]);

        //検索で引っかかるようなブックマーク作成
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

        // わざと消すブックマーク
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

        //わざとブックマークを消す
        BookMark::where('id','=',$deleteBookMark->id)
        ->update(['deleted_at' => Carbon::now()]);

        $response = $this->bookMarkRepository->search(
            userId:$this->userId,
            keyword:"",
            page:1,
            tagList:[$hitTag->id],
            searchTarget:'title'
        );

        $bookMarkList = $response['data'];

        $idList = [];
        foreach ($bookMarkList as $data){ array_push($idList,$data['id']); }

        // 全部指定したユーザーのブックマークか
        foreach ($bookMarkList as $data){ $this->assertEquals($data['user_id'],$this->userId); }

        // 削除したブックマークは含んでないか
        $this->assertNotContains($deleteBookMark['id'],$idList);

        // ヒットするはずのブックマークを取ってきているか
        $this->assertContains($hitBookMark1['id'], $idList);
    }

    // 期待
    //  * titelカラムに"make"の文字列が入っているデータをとってくる
    //  * 指定したタグがついているブックマークをとってくる
    //  * 指定したユーザーのブックマークだけを取ってくる
    //  * 削除したブックマークは取ってこない
    // 条件
    // * タグ指定:'recipe'
    // * タイトル検索
    // * キーワードなし
    public function test_Search_タグ指定あり_タイトル検索_キーワードあり()
    {
        //
        Carbon::setTestNow(Carbon::now());


        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーのブックマーク
        $bookMarks = BookMark::factory()->count(10)->create(['user_id' => $this->userId]);


        //検索で引っかかるようなブックマーク作成
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

        // わざと消すブックマーク
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

        //他のダミーブックマーク
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        Tag::factory()->count(2)->create(['user_id' => $anotherUsers[0]->id]);
        Tag::factory()->count(2)->create(['user_id' => $anotherUsers[0]->id]);


        //わざとブックマークを消す
        BookMark::where('id','=',$deleteBookMark->id)
        ->update(['deleted_at' => Carbon::now()]);

        $response = $this->bookMarkRepository->search(
            userId:$this->userId,
            keyword:"make",
            page:1,
            tagList:[$hitTag->id],
            searchTarget:'title'
        );

        $bookMarkList = $response['data'];

        $idList = [];
        foreach ($bookMarkList as $data){ array_push($idList,$data['id']); }

        // 全部指定したユーザーのブックマークか
        foreach ($bookMarkList as $data){ $this->assertEquals($data['user_id'],$this->userId); }

        // 削除したブックマークは含んでないか
        $this->assertNotContains($deleteBookMark->id,$idList);

        // ヒットするはずのブックマークを取ってきているか
        $this->assertContains($hitBookMark->id, $idList);
    }

    // 期待
    // * 指定したユーザーのブックマークだけを取ってくる
    // * 削除したブックマークは取ってこない
    // 条件
    // * タグ指定なし
    // * 本文検索
    // * キーワードなし
    public function test_Search_タグ指定なし_本文検索_キーワードなし()
    {
        //
        Carbon::setTestNow(Carbon::now());

        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーのブックマーク
        $bookMarks = BookMark::factory()->count(20)->create(['user_id' => $this->userId]);

        //わざとブックマークを消す
        BookMark::where('id','=',$bookMarks[0]->id)
        ->update(['deleted_at' => Carbon::now()]);
        BookMark::where('id','=',$bookMarks[1]->id)
        ->update(['deleted_at' => Carbon::now()]);

        // ダミーのブックマーク
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        $response = $this->bookMarkRepository->search(
            userId:$this->userId,
            keyword:'',
            page:1,
            tagList:null,
            searchTarget:'url'
        );

        $bookMarkList = $response['data'];

        $idList = [];
        foreach ($bookMarkList as $data){ array_push($idList,$data['id']); }

        // 全部指定したユーザーのブックマークか
        foreach ($bookMarkList as $data){ $this->assertEquals($data['user_id'],$this->userId); }

        // 削除したブックマークは含んでないか
        $this->assertNotContains($bookMarks[0]['id'],$idList);
        $this->assertNotContains($bookMarks[1]['id'],$idList);
    }

    // 期待
    //  * titelカラムに"apple"と"make"の文字列が入っているデータをとってくる
    //  * 指定したユーザーのブックマークだけを取ってくる
    //  * 削除したブックマークは取ってこない
    // 条件
    // * タグ指定なし
    // * 本文検索
    // * キーワードあり
    public function test_Search_タグ指定なし_本文検索_キーワードあり()
    {
        //
        Carbon::setTestNow(Carbon::now());


        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーのブックマーク
        $bookMarks = BookMark::factory()->count(10)->create(['user_id' => $this->userId]);

        //検索で引っかかるようなブックマーク作成
        $hitBookMark1 = BookMark::factory()->create([
            'user_id' => $this->userId,
            'url' => 'how to make applePie'
        ]);

        $hitBookMark2 = BookMark::factory()->create([
            'user_id' => $this->userId,
            'url' => 'how to make appleTea'
        ]);

        // わざと消すブックマーク
        $deleteBookMark = BookMark::factory()->create([
            'user_id' => $this->userId,
            'url' => 'how to make appleJam'
        ]);

        //わざとブックマークを消す
        BookMark::where('id','=',$deleteBookMark->id)->update(['deleted_at' => Carbon::now()]);

        // ダミーのブックマーク
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        $response = $this->bookMarkRepository->search(
            userId:$this->userId,
            keyword:"apple make",
            page:1,
            tagList:null,
            searchTarget:'url'
        );

        $bookMarkList = $response['data'];

        $idList = [];
        foreach ($bookMarkList as $data){ array_push($idList,$data['id']); }


        // 全部指定したユーザーのブックマークか
        foreach ($bookMarkList as $data){ $this->assertEquals($data['user_id'],$this->userId); }

        // 削除したブックマークは含んでないか
        $this->assertNotContains($deleteBookMark['id'],$idList);

        // ヒットするはずのブックマークを取ってきているか
        $this->assertContains($hitBookMark1['id'], $idList);
        $this->assertContains($hitBookMark2['id'], $idList);

    }

    // 期待
    //  * 指定したタグがついているブックマークをとってくる
    //  * 指定したユーザーのブックマークを取ってくる
    //  * 削除したブックマークは取ってこない
    // 条件
    // * タグ指定:'recipe'
    // * 本文検索
    // * キーワードなし
    public function test_Search_タグ指定あり_本文検索_キーワードなし_()
    {
        //
        Carbon::setTestNow(Carbon::now());

        // 検索でヒットするはずのタグを作る
        $hitTag = Tag::factory()->create([
            'name'    => 'recipe',
            'user_id' => $this->userId
        ]);

        //検索で引っかかるようなブックマーク作成
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

        // わざと消すブックマーク
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

        //わざとブックマークを消す
        BookMark::where('id','=',$deleteBookMark->id)
        ->update(['deleted_at' => Carbon::now()]);

        $response = $this->bookMarkRepository->search(
            userId:$this->userId,
            keyword:"",
            page:1,
            tagList:[$hitTag->id],
            searchTarget:'url'
        );

        $bookMarkList = $response['data'];

        $idList = [];
        foreach ($bookMarkList as $data){ array_push($idList,$data['id']); }

        // 全部指定したユーザーのブックマークか
        foreach ($bookMarkList as $data){ $this->assertEquals($data['user_id'],$this->userId); }

        // 削除したブックマークは含んでないか
        $this->assertNotContains($deleteBookMark->id,$idList);

        // ヒットするはずのブックマークを取ってきているか
        $this->assertContains($hitBookMark1->id, $idList);
    }

    // 期待
    //  * titelカラムに"make"の文字列が入っているデータをとってくる
    //  * 指定したタグがついているブックマークをとってくる
    //  * 指定したユーザーのブックマークだけを取ってくる
    //  * 削除したブックマークは取ってこない
    // 条件
    // * タグ指定:'recipe'
    // * 本文検索
    // * キーワードなし
    public function test_Search_タグ指定あり_本文検索_キーワードあり()
    {
        //
        Carbon::setTestNow(Carbon::now());


        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーのブックマーク
        $bookMarks = BookMark::factory()->count(10)->create(['user_id' => $this->userId]);


        //検索で引っかかるようなブックマーク作成
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

        // わざと消すブックマーク
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

        //他のダミーブックマーク
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        Tag::factory()->count(2)->create(['user_id' => $anotherUsers[0]->id]);
        Tag::factory()->count(2)->create(['user_id' => $anotherUsers[0]->id]);


        //わざとブックマークを消す
        BookMark::where('id','=',$deleteBookMark->id)
        ->update(['deleted_at' => Carbon::now()]);

        $response = $this->bookMarkRepository->search(
            userId:$this->userId,
            keyword:"make",
            page:1,
            tagList:[$hitTag->id],
            searchTarget:'url'
        );

        $bookMarkList = $response['data'];

        $idList = [];
        foreach ($bookMarkList as $data){ array_push($idList,$data['id']); }

        // 全部指定したユーザーのブックマークか
        foreach ($bookMarkList as $data){ $this->assertEquals($data['user_id'],$this->userId); }

        // 削除したブックマークは含んでないか
        $this->assertNotContains($deleteBookMark->id,$idList);

        // ヒットするはずのブックマークを取ってきているか
        $this->assertContains($hitBookMark->id , $idList);
    }
}
