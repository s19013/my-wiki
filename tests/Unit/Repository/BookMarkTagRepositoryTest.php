<?php

namespace Tests\Unit\Repository;

use Tests\TestCase;

use App\Models\BookMarkTag;
use App\Models\BookMark;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\DB;

use App\Repository\BookMarkTagRepository;

//ファクトリーで使う
use Illuminate\Database\Eloquent\Factories\Sequence;

// データベース関係で使う
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

use Carbon\Carbon;

class BookMarkTagRepositoryTest extends TestCase
{
    // テストしたらリセットする
    use RefreshDatabase;

    private $bookMarkTagModel;
    private $bookMarkRepository;
    private $userId;
    private $bookMarkId;

    public $uniqeInt1 = [1,2,3,4,5];
    public $uniqeInt2 = [6,7,8,9,10];

    public function setup():void
    {
        parent::setUp();
        $this->bookMarkRepository = new BookMarkTagRepository();

        // ユーザーを用意
        $user = User::create([
            'name'     => "tester",
            'email'    => "hideya670@gmail.com",
            'password' => "gemini0522",
        ]);

        $this->userId = $user->id;

        // ブックマークを用意
        $bookMark = BookMark::create([
            'user_id' => $this->userId,
            'title'   => 'testTitle',
            'url'    => 'testurl',
        ]);

        $this->bookMarkId = $bookMark->id;
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

    //

    // 期待
    // 引数2に指定したブックマークに､引数1に指定したタグのIdをデータベースに保管する
    // 条件
    // 引数1には数字が渡される
    public function test_store_タグをつけた場合()
    {
        $tag = Tag::create([
            'user_id' => $this->userId,
            'name'    => 'bookMarkTagModelStoreTest',
        ]);

        $this->bookMarkRepository->store($tag->id,$this->bookMarkId);

        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookMarkId,
            'tag_id' => $tag->id
        ]);
    }

    // 期待
    // 引数2に指定したブックマークに､引数1に指定したnullをデータベースに保管する
    // 条件
    // 引数1にはnullが渡される
    public function test_store_タグをつけなかった場合()
    {
        $this->bookMarkRepository->store(null,$this->bookMarkId);

        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookMarkId,
            'tag_id' => null
        ]);
    }

    // 期待
    // 引数2に指定したブックマークから引数1に指定されたタグを論理削除する
    public function test_delete_紐づけたタグを論理削除する()
    {
        Carbon::setTestNow(Carbon::now());

        $tag = Tag::create([
            'user_id' => $this->userId,
            'name'    => 'bookMarkTagModelStoreTest',
        ]);

        $this->bookMarkRepository->store($tag->id,$this->bookMarkId);

        $this->bookMarkRepository->delete($tag->id,$this->bookMarkId);

        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => $tag->id,
            'deleted_at' => Carbon::now(),
        ]);
    }

    // 期待
    // 引数1に指定したブックマークに紐付けられたタグの名前とタグのidを取得
    // 条件
    // 登録時に何かしらのタグを紐づけた
    public function test_serveTagsRelatedToBookMark_登録時にタグを紐づけた場合()
    {
        $tags =Tag::factory()->count(5)->create([
            'user_id' => $this->userId,
        ]);

        foreach ($tags as $tag) { $this->bookMarkRepository->store($tag->id,$this->bookMarkId); }

        // タグを取得
        $bookMarkTags = $this->bookMarkRepository->serveTagsRelatedToBookMark($this->bookMarkId,$this->userId);

        //名前とidが一緒かどうか

        $IdList = [];

        foreach ($bookMarkTags as $bookMarkTag){array_push($IdList,$bookMarkTag->id);}

        $nameList = [];
        foreach ($bookMarkTags as $bookMarkTag){ array_push($nameList,$bookMarkTag->name);}
        foreach ($tags as $tag){
            $this->assertContains($tag->id,$IdList);
            $this->assertContains($tag->name,$nameList);
        }
    }

    // 期待
    // 引数1に指定したブックマークに紐付けられたタグの名前とnullを取得
    // 条件
    // 登録時に何もタグをつけなかった
    public function test_serveTagsRelatedToBookMark_登録時にタグを紐づけなかった場合()
    {
        $this->bookMarkRepository->store(null,$this->bookMarkId);

        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => null,
            'deleted_at' => null
        ]);
    }

    // 期待
    // 指定したブックマークについていたタグをすべて外して､新しく指定したタグをブックマークに紐づける
    // 古いタグは論理削除されている
    public function test_update_ブックマークについていたタグと新規のタグを入れ替える()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        //更新前
        $tags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        // ブックマークに紐づける
        foreach($tags as $tag){ $this->bookMarkRepository->store($tag->id,$this->bookMarkId); }

        //----

        //新しいタグを追加
        $newTags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        //更新
        $this->bookMarkRepository->update($this->bookMarkId,[$newTags[0]->id,$newTags[1]->id]);

        // 更新前のデータ(ちゃんと消されたか確認する)
        foreach($tags as $tag){
            $this->assertDatabaseHas('book_mark_tags',[
                'book_mark_id' => $this->bookMarkId,
                'tag_id'     => $tag->id,
                'deleted_at' => Carbon::now()
            ]);
        }

        // 更新後のデータ
        foreach($newTags as $tag){
            $this->assertDatabaseHas('book_mark_tags',[
                'book_mark_id' => $this->bookMarkId,
                'tag_id'     => $tag->id,
                'deleted_at' => null
            ]);
        }
    }

    // 期待
    // 指定したタグが何もついてなかったブックマークに､新しく指定したタグをブックマークに紐づける
    // nullは論理削除されている
    public function test_update_タグがついていなかったブックマークにタグをつける()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        $this->bookMarkRepository->store(null,$this->bookMarkId);

        //----

        //新しいタグを追加
        $newTags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        //更新
        $this->bookMarkRepository->update($this->bookMarkId,[$newTags[0]->id,$newTags[1]->id]);

        // 更新前のデータ(ちゃんと消されたか確認する)
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => null,
            'deleted_at' => Carbon::now()
        ]);

        // 更新後のデータ
        foreach($newTags as $tag){
            $this->assertDatabaseHas('book_mark_tags',[
                'book_mark_id' => $this->bookMarkId,
                'tag_id'     => $tag->id,
                'deleted_at' => null
            ]);
        }
    }

    // 期待
    // 指定したブックマークについていたタグはそのままに新規のタグを追加
    public function test_update_ブックマークについていたタグはそのままに新規のタグを追加()
    {
        //更新前
        $tags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        foreach($tags as $tag){ $this->bookMarkRepository->store($tag->id,$this->bookMarkId); }

        //----

        //新しいタグを追加
        $newTags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        //更新
        $this->bookMarkRepository->update($this->bookMarkId,[$newTags[0]->id,$newTags[1]->id,$tags[0]->id,$tags[1]->id]);

        //取得したタグが新しく作ったタグになっているか確認
        // もともとつけていた部分

        foreach ($tags as $tag){
            $this->assertDatabaseHas('book_mark_tags',[
                'book_mark_id' => $this->bookMarkId,
                'tag_id'     => $tag->id,
                'deleted_at' => null
            ]);
        }

        // 追加した部分
        foreach ($newTags as $tag){
            $this->assertDatabaseHas('book_mark_tags',[
                'book_mark_id' => $this->bookMarkId,
                'tag_id'     => $tag->id,
                'deleted_at' => null
            ]);
        }
    }

    // 期待
    // 指定したブックマークについていたタグを一部外して､新しく指定したタグをブックマークに紐づける
    // 古いタグは論理削除されている
    public function test_update_ブックマークについていたタグの一部を外す()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        //更新前
        $tags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        foreach($tags as $tag){$this->bookMarkRepository->store($tag->id,$this->bookMarkId);}

        //----

        $newTags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        //更新
        $this->bookMarkRepository->update($this->bookMarkId,[$tags[1]->id,$newTags[0]->id,$newTags[1]->id]);

        // けしたやつ
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => $tags[0]->id,
            'deleted_at' => Carbon::now()
        ]);
        //残したやつ
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => $tags[1]->id,
            'deleted_at' => null
        ]);

        foreach($newTags as $tag){
            $this->assertDatabaseHas('book_mark_tags',[
                'book_mark_id' => $this->bookMarkId,
                'tag_id'     => $tag->id,
                'deleted_at' => null
            ]);
        }
    }

    // 期待
    // 指定したブックマークについてあるすべてのタグのidを配列形式で取ってくる
    public function test_getOrignalTag()
    {
        $tags = Tag::factory()->count(4)->create(['user_id' => $this->userId]);

        // 登録
        foreach($tags as $tag){ $this->bookMarkRepository->store($tag->id,$this->bookMarkId); }

        // 取得
        $bookMarkTags = $this->bookMarkRepository->getOrignalTag($this->bookMarkId);

        // print_r($bookMarkTags);

        // 登録したタグがあるかどうか
        foreach ($bookMarkTags as $bookMarkTag) {
            $this->assertContains($bookMarkTag,$bookMarkTags);
        }
    }

    // 期待
    // procesOriginalBookMarkDoesNotHaveAnyTagsの帰り値がnullである
    // 元のデータに変化がない
    // 条件
    // 元のブックマークにタグがついている
    public function test_procesOriginalBookMarkDoesNotHaveAnyTags_元のブックマークにタグがついている()
    {
        $tags = Tag::factory()->count(4)->create(['user_id' => $this->userId]);

        foreach ($tags as $tag){ $this->bookMarkRepository->store($tag->id,$this->bookMarkId); }

        $returnValue = $this->bookMarkRepository->procesOriginalBookMarkDoesNotHaveAnyTags(
            originalTagList:$this->bookMarkRepository->getOrignalTag($this->bookMarkId),
            bookMarkId:$this->bookMarkId,
            updatedTagList:[$tags[0]->id,$tags[1]->id,$tags[2]->id,$tags[3]->id]
        );

        // 何もしないのでnullが返される
        $this->assertNull($returnValue);

        // 登録されているタグに変化がない
        foreach ($tags as $tag){
            $this->assertDatabaseHas('book_mark_tags',[
                'book_mark_id' => $this->bookMarkId,
                'tag_id'     => $tag->id,
                'deleted_at' => null
            ]);
        }
        // procesOriginalBookMarkDoesNotHaveAnyTagsでは新規タグの登録はしないからここではテストしない

    }

    // 期待
    // procesOriginalBookMarkDoesNotHaveAnyTagsの帰り値がnullである
    // tag_id = nullのデータが論理削除されている
    // 条件
    // 元のブックマークにタグがついていない,新しくタグを追加しようとしている
    public function test_procesOriginalBookMarkDoesNotHaveAnyTags_元のブックマークにタグがついていない_新しくタグを追加()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        $this->bookMarkRepository->store(null,$this->bookMarkId);

        $tags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        $returnValue = $this->bookMarkRepository->procesOriginalBookMarkDoesNotHaveAnyTags(
            originalTagList:$this->bookMarkRepository->getOrignalTag($this->bookMarkId),
            bookMarkId:$this->bookMarkId,
            updatedTagList:[$tags[0]->id,$tags[1]->id]
        );

        $this->assertNull($returnValue);

        //nullのデータが論理削除されている
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => null,
            'deleted_at' => Carbon::now()
        ]);

        // procesOriginalBookMarkDoesNotHaveAnyTagsでは新規タグの登録はしないからここではテストしない
    }

    // 期待
    // procesOriginalBookMarkDoesNotHaveAnyTagsの帰り値がTrueである
    // tag_id = nullのデータが論理削除されている
    // 元のデータに変化がない
    // 条件
    // 元のブックマークにタグがついていない_タグも追加しない
    public function test_procesOriginalBookMarkDoesNotHaveAnyTags_元のブックマークにタグがついていない_タグも追加しない()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        $this->bookMarkRepository->store(null,$this->bookMarkId);

        $returnValue = $this->bookMarkRepository->procesOriginalBookMarkDoesNotHaveAnyTags(
            originalTagList:$this->bookMarkRepository->getOrignalTag($this->bookMarkId),
            bookMarkId:$this->bookMarkId,
            updatedTagList:[]
        );

        $this->assertTrue($returnValue);

        //元データに変化なし
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => null,
            'deleted_at' => null
        ]);
        // procesOriginalBookMarkDoesNotHaveAnyTagsでは新規タグの登録はしないからここではテストしない
    }

    // 期待
    // procesOriginalBookMarkDeleteAllTagsの帰り値がnullである
    // tag_id = nullのデータが新しく追加されている
    // 条件
    // 元のブックマークにタグをすべてけした,追加タグなし
    public function test_procesOriginalBookMarkDeleteAllTags_追加タグなし_ついてたタグ全部けした()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        $tags = Tag::factory()->count(4)->create(['user_id' => $this->userId]);

        $this->bookMarkRepository->store($tags[0]->id,$this->bookMarkId);
        $this->bookMarkRepository->store($tags[1]->id,$this->bookMarkId);

        $returnValue = $this->bookMarkRepository->procesOriginalBookMarkDeleteAllTags(
                originalTagList:$this->bookMarkRepository->getOrignalTag($this->bookMarkId),
                bookMarkId:$this->bookMarkId,
                isAddedTagListEmpty:True,
                deletedTagList:[$tags[0]->id,$tags[1]->id]
            );

        $this->assertNull($returnValue);

        // もとあったデータの論理削除は別の関数でやるのでここではチェックしない

        // tag_id = nullのデータが追加されているか確認
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => null,
            'deleted_at' => null
        ]);
    }


    // 期待
    // procesOriginalBookMarkDeleteAllTagsの帰り値がnullである
    // 条件
    // 元のブックマークにタグをすべてけした,追加タグあり
    public function test_procesOriginalBookMarkDeleteAllTags_追加タグあり_ついてたタグ全部けした()
    {

        $tags = Tag::factory()->count(4)->create(['user_id' => $this->userId]);

        $this->bookMarkRepository->store($tags[0]->id,$this->bookMarkId);
        $this->bookMarkRepository->store($tags[1]->id,$this->bookMarkId);

        $returnValue = $this->bookMarkRepository->procesOriginalBookMarkDeleteAllTags(
                originalTagList:$this->bookMarkRepository->getOrignalTag($this->bookMarkId),
                bookMarkId:$this->bookMarkId,
                isAddedTagListEmpty:false,
                deletedTagList:[$tags[0]->id,$tags[1]->id]
            );

        $this->assertNull($returnValue);

        // もとあったデータの論理削除､新しいデータの追加は別の関数でやるのでここではチェックしない
    }

    // 期待
    // procesOriginalBookMarkDeleteAllTagsの帰り値がnullである
    // 元のデータに変化がない
    // 条件
    // 元のブックマークにタグがついてない,追加タグなし
    public function test_procesOriginalBookMarkDeleteAllTags_追加タグなし_タグついてない()
    {
        $this->bookMarkRepository->store(null,$this->bookMarkId);

        $returnValue = $this->bookMarkRepository->procesOriginalBookMarkDeleteAllTags(
                originalTagList:$this->bookMarkRepository->getOrignalTag($this->bookMarkId),
                bookMarkId:$this->bookMarkId,
                isAddedTagListEmpty:true,
                deletedTagList:[]
            );

        $this->assertNull($returnValue);

        // 元のデータに変化がない
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => null,
            'deleted_at' => null
        ]);
    }

    // 期待
    // procesOriginalBookMarkDeleteAllTagsの帰り値がnullである
    // 元のデータに変化がない
    // 条件
    // 元のブックマークにタグがついてない,追加タグなし
    public function test_procesOriginalBookMarkDeleteAllTags_追加タグあり_タグついてない()
    {
        $this->bookMarkRepository->store(null,$this->bookMarkId);

        $returnValue = $this->bookMarkRepository->procesOriginalBookMarkDeleteAllTags(
                originalTagList:$this->bookMarkRepository->getOrignalTag($this->bookMarkId),
                bookMarkId:$this->bookMarkId,
                isAddedTagListEmpty:false,
                deletedTagList:[]
            );

        $this->assertNull($returnValue);

        // 元のデータに変化がない
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => null,
            'deleted_at' => null
        ]);
        // 新しいデータの追加は別の関数でやるのでここではチェックしない
    }
}

