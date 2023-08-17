<?php

namespace Tests\Unit\Repository\BookMarkRepository;

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
        //テストユーザーのブックマーク
        BookMark::factory()->count(25)->create(['user_id' => $this->userId]);

        $response = $this->bookMarkRepository->search(
            userId:$this->userId,
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
        //テストユーザーのブックマーク
        BookMark::factory()->count(5)->create(['user_id' => $this->userId]);

        $response = $this->bookMarkRepository->search(
            userId:$this->userId,
            searchQuantity:10
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
            page:2,
            searchQuantity:10
        );

        $bookMarkList = $response['data'];

        //帰ってきたbookMarkListの数を数える(10個以上あっても一度に10個までしか返さない)
        for ($i=10; $i <20 ; $i++) {
            $this->assertSame($bookMarkList[$i - 10]->title,"title ${i}");
        }
    }

    // 期待
    // * 指定したユーザーのブックマークだけを取ってくる
    // * 削除したブックマークは取ってこない
    // 条件
    // * タグ指定なし
    // * タイトル検索
    // * キーワードなし
    public function test_Search_タグ指定なし_キーワードなし()
    {
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
        );

        $bookMarkList = $response['data'];

        $idList = [];
        foreach ($bookMarkList as $data){ array_push($idList,$data->id); }

        // 全部指定したユーザーのブックマークか
        foreach ($bookMarkList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除したブックマークは含んでないか
        $this->assertNotContains($bookMarks[0]->id,$idList);
        $this->assertNotContains($bookMarks[1]->id,$idList);
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
            title:"apple make",
        );

        $bookMarkList = $response['data'];

        $idList = [];
        foreach ($bookMarkList as $data){ array_push($idList,$data->id); }


        // 全部指定したユーザーのブックマークか
        foreach ($bookMarkList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除したブックマークは含んでないか
        $this->assertNotContains($deleteBookMark->id,$idList);

        // ヒットするはずのブックマークを取ってきているか
        $this->assertContains($hitBookMark1->id, $idList);
        $this->assertContains($hitBookMark2->id, $idList);

    }

    // 期待
    //  * 指定したタグがついているブックマークをとってくる
    //  * 指定したユーザーのブックマークを取ってくる
    //  * 削除したブックマークは取ってこない
    // 条件
    // * タグ指定:'recipe'
    // * タイトル検索
    // * キーワードなし
    public function test_Search_タグ指定あり_キーワードなし()
    {
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
            tagList:[$hitTag->id],
        );

        $bookMarkList = $response['data'];

        $idList = [];
        foreach ($bookMarkList as $data){ array_push($idList,$data->id); }

        // 全部指定したユーザーのブックマークか
        foreach ($bookMarkList as $data){ $this->assertEquals($data->user_id,$this->userId); }

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
    // * タイトル検索
    // * キーワードあり
    public function test_Search_タグ指定あり_タイトル検索_キーワードあり()
    {

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
            title:"make",
            tagList:[$hitTag->id],
        );

        $bookMarkList = $response['data'];

        $idList = [];
        foreach ($bookMarkList as $data){ array_push($idList,$data->id); }

        // 全部指定したユーザーのブックマークか
        foreach ($bookMarkList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除したブックマークは含んでないか
        $this->assertNotContains($deleteBookMark->id,$idList);

        // ヒットするはずのブックマークを取ってきているか
        $this->assertContains($hitBookMark->id, $idList);
    }

    // 期待
    //  * titelカラムに"apple"と"make"の文字列が入っているデータをとってくる
    //  * 指定したユーザーのブックマークだけを取ってくる
    //  * 削除したブックマークは取ってこない
    // 条件
    // * タグ指定なし
    // * url検索
    // * キーワードあり
    public function test_Search_タグ指定なし_url検索_キーワードあり()
    {


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
            url:"apple make",
        );

        $bookMarkList = $response['data'];

        $idList = [];
        foreach ($bookMarkList as $data){ array_push($idList,$data->id); }


        // 全部指定したユーザーのブックマークか
        foreach ($bookMarkList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除したブックマークは含んでないか
        $this->assertNotContains($deleteBookMark->id,$idList);

        // ヒットするはずのブックマークを取ってきているか
        $this->assertContains($hitBookMark1->id, $idList);
        $this->assertContains($hitBookMark2->id, $idList);

    }

    // 期待
    //  * titelカラムに"make"の文字列が入っているデータをとってくる
    //  * 指定したタグがついているブックマークをとってくる
    //  * 指定したユーザーのブックマークだけを取ってくる
    //  * 削除したブックマークは取ってこない
    // 条件
    // * タグ指定:'recipe'
    // * url検索
    // * キーワードあり
    public function test_Search_タグ指定あり_url検索_キーワードあり()
    {
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
            url:"make",
            tagList:[$hitTag->id],
        );

        $bookMarkList = $response['data'];

        $idList = [];
        foreach ($bookMarkList as $data){ array_push($idList,$data->id); }

        // 全部指定したユーザーのブックマークか
        foreach ($bookMarkList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除したブックマークは含んでないか
        $this->assertNotContains($deleteBookMark->id,$idList);

        // ヒットするはずのブックマークを取ってきているか
        $this->assertContains($hitBookMark->id , $idList);
    }


    // 期待
    //  * titelカラムに"apple"と"make"の文字列が入っているデータをとってくる
    //  * urlカラムに"qiita.com"の文字列が入っているデータをとってくる
    //  * 指定したユーザーのブックマークだけを取ってくる
    //  * 削除したブックマークは取ってこない
    // 条件
    // * タグ指定なし
    // * url検索
    // * キーワードあり
    public function test_Search_タグ指定なし_title検索_url検索_キーワードあり()
    {

        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーのブックマーク
        $bookMarks = BookMark::factory()->count(10)->create(['user_id' => $this->userId]);

        //検索で引っかかるようなブックマーク作成
        $hitBookMark1 = BookMark::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make applePie',
            'url' => 'qiita.com/1'
        ]);

        $hitBookMark2 = BookMark::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make appleTea',
            'url' => 'qiita.com/2'
        ]);

        // わざと消すブックマーク
        $deleteBookMark = BookMark::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make appleJam',
            'url' => 'qiita.com/3'
        ]);

        //わざとブックマークを消す
        BookMark::where('id','=',$deleteBookMark->id)->update(['deleted_at' => Carbon::now()]);

        // ダミーのブックマーク
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        BookMark::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        $response = $this->bookMarkRepository->search(
            userId:$this->userId,
            title:"apple make",
            url:"qiita.com"
        );

        $bookMarkList = $response['data'];

        $idList = [];
        foreach ($bookMarkList as $data){ array_push($idList,$data->id); }


        // 全部指定したユーザーのブックマークか
        foreach ($bookMarkList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除したブックマークは含んでないか
        $this->assertNotContains($deleteBookMark->id,$idList);

        // ヒットするはずのブックマークを取ってきているか
        $this->assertContains($hitBookMark1->id, $idList);
        $this->assertContains($hitBookMark2->id, $idList);

    }

    // 期待
    //  * titelカラムに"apple"と"make"の文字列が入っているデータをとってくる
    //  * urlカラムに"qiita.com"の文字列が入っているデータをとってくる
    //  * 指定したタグがついているブックマークをとってくる
    //  * 指定したユーザーのブックマークだけを取ってくる
    //  * 削除したブックマークは取ってこない
    // 条件
    // * タグ指定:'recipe'
    // * url検索
    // * キーワードあり
    public function test_Search_タグ指定あり_title検索_url検索_キーワードあり()
    {
        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーのブックマーク
        $bookMarks = BookMark::factory()->count(10)->create(['user_id' => $this->userId]);


        //検索で引っかかるようなブックマーク作成
        $hitBookMark = BookMark::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make applePie',
            'url' => 'qiita.com/1'
        ]);

        $hitTag = Tag::factory()->create([
            'name'    => 'recipe',
            'user_id' => $this->userId
        ]);

        // 他のダミーにも付けるタグ
        $tag = Tag::factory()->create([
            'name'    => 'rest',
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
            'title' => 'how to make berryPie',
            'url' => 'qiita.com/2'
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
            'title' => 'how to make peachPie',
            'url' => 'qiita.com/3'
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
            title:"apple make",
            url:"qiita.com",
            tagList:[$hitTag->id],
        );

        $bookMarkList = $response['data'];

        $idList = [];
        foreach ($bookMarkList as $data){ array_push($idList,$data->id); }

        // 全部指定したユーザーのブックマークか
        foreach ($bookMarkList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除したブックマークは含んでないか
        $this->assertNotContains($deleteBookMark->id,$idList);

        // ヒットするはずのブックマークを取ってきているか
        $this->assertContains($hitBookMark->id , $idList);
    }

    // 期待
    // * 取ってきたデータの数が20こ
    // 条件
    // * 検索量を指定する
    public function test_Search_searchQuantity()
    {
        BookMark::factory()->count(30)->create(['user_id' => $this->userId]);

        $response = $this->bookMarkRepository->search(
            userId:$this->userId,
            searchQuantity:20
        );

        $this->assertCount(20,$response['data']);
    }

    // 期待
    // * 帰ってきたデータがZ -> A
    public function test_Search_sort()
    {
        BookMark::factory()->create([
            'title'   => "A",
            'user_id' => $this->userId
        ]);

        BookMark::factory()->create([
            'title'   => "B",
            'user_id' => $this->userId
        ]);

        BookMark::factory()->create([
            'title'   => "C",
            'user_id' => $this->userId
        ]);


        $response = $this->bookMarkRepository->search(
            userId:$this->userId,
            searchQuantity:10,
            sortType:"title_desc"
        );

        $bookMarkList = $response['data'];
        $this->assertSame("C",$bookMarkList[0]->title);
        $this->assertSame("B",$bookMarkList[1]->title);
        $this->assertSame("A",$bookMarkList[2]->title);
    }

    // 期待
    // * タグが1つもついてないデータのみを取ってくる
    public function test_search_untagged()
    {
        $bookMarkList = BookMark::factory()->count(5)->create(['user_id' => $this->userId]);

        foreach ($bookMarkList as $bookMark){
            BookMarkTag::create([
                'book_mark_id' => $bookMark->id,
                'tag_id'     => null,
            ]);
        }

        // ダミー
        $dammyBookMarkList = BookMark::factory()->count(5)->create(['user_id' => $this->userId]);
        $tag = Tag::factory()->create(['user_id' => $this->userId]);

        foreach ($dammyBookMarkList as $bookMark){
            BookMarkTag::create([
                'book_mark_id' => $bookMark->id,
                'tag_id'     => $tag->id,
            ]);
        }

        $response = $this->bookMarkRepository->search(
            userId:$this->userId,
            searchQuantity:10,
            sortType:"title_desc",
            isSearchUntagged:true,
        );

        // 帰ってきたかず
        $bookMarkList = $response['data'];
        $this->assertCount(5,$response['data']);

    }
}
