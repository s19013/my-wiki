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
    private $bookMarkTagRepository;
    private $userId;
    private $bookMarkId;

    public $uniqeInt1 = [1,2,3,4,5];
    public $uniqeInt2 = [6,7,8,9,10];

    public function setup():void
    {
        parent::setUp();
        $this->bookMarkTagRepository = new BookMarkTagRepository();

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

        $this->bookMarkTagRepository->store($tag->id,$this->bookMarkId);

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
        $this->bookMarkTagRepository->store(null,$this->bookMarkId);

        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookMarkId,
            'tag_id' => null
        ]);
    }

    // 期待
    // 引数2に指定したブックマークから引数1に指定されたタグを物理削除する
    public function test_delete_紐づけたタグを物理削除する()
    {
        $tag = Tag::create([
            'user_id' => $this->userId,
            'name'    => 'bookMarkTagModelStoreTest',
        ]);

        $this->bookMarkTagRepository->store($tag->id,$this->bookMarkId);

        $this->bookMarkTagRepository->delete($tag->id,$this->bookMarkId);

        $this->assertDatabaseMissing('book_mark_tags',[
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => $tag->id,
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

        foreach ($tags as $tag) { $this->bookMarkTagRepository->store($tag->id,$this->bookMarkId); }

        // タグを取得
        $bookMarkTags = $this->bookMarkTagRepository->serveTagsRelatedToBookMark($this->bookMarkId,$this->userId);

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
    // 空の配列を受け取る
    // 条件
    // 登録時に何もタグをつけなかった
    public function test_serveTagsRelatedToBookMark_登録時にタグを紐づけなかった場合()
    {
        $this->bookMarkTagRepository->store(null,$this->bookMarkId);

        // タグを取得
        $bookMarkTags = $this->bookMarkTagRepository->serveTagsRelatedToBookMark($this->bookMarkId,$this->userId);

        $this->assertEmpty($bookMarkTags);
    }

    // 期待
    // 指定したブックマークについていたタグをすべて外して､新しく指定したタグをブックマークに紐づける
    // 古いタグは論理削除されている
    public function test_update_ブックマークについていたタグと新規のタグを入れ替える()
    {
        // carbonの時間固定


        //更新前
        $tags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        // ブックマークに紐づける
        foreach($tags as $tag){ $this->bookMarkTagRepository->store($tag->id,$this->bookMarkId); }

        //----

        //新しいタグを追加
        $newTags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        //更新
        $this->bookMarkTagRepository->update($this->bookMarkId,[$newTags[0]->id,$newTags[1]->id]);

        // 更新前のデータ(ちゃんと消されたか確認する)
        foreach($tags as $tag){
            $this->assertDatabaseMissing('book_mark_tags',[
                'book_mark_id' => $this->bookMarkId,
                'tag_id'     => $tag->id,
            ]);
        }

        // 更新後のデータ
        foreach($newTags as $tag){
            $this->assertDatabaseHas('book_mark_tags',[
                'book_mark_id' => $this->bookMarkId,
                'tag_id'     => $tag->id,

            ]);
        }
    }

    // 期待
    // 指定したタグが何もついてなかったブックマークに､新しく指定したタグをブックマークに紐づける
    // nullは論理削除されている
    public function test_update_タグがついていなかったブックマークにタグをつける()
    {
        // carbonの時間固定


        $this->bookMarkTagRepository->store(null,$this->bookMarkId);

        //----

        //新しいタグを追加
        $newTags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        //更新
        $this->bookMarkTagRepository->update($this->bookMarkId,[$newTags[0]->id,$newTags[1]->id]);

        // 更新前のデータ(ちゃんと消されたか確認する)
        $this->assertDatabaseMissing('book_mark_tags',[
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => null,
        ]);

        // 更新後のデータ
        foreach($newTags as $tag){
            $this->assertDatabaseHas('book_mark_tags',[
                'book_mark_id' => $this->bookMarkId,
                'tag_id'     => $tag->id,

            ]);
        }
    }

    // 期待
    // 指定したブックマークについていたタグはそのままに新規のタグを追加
    public function test_update_ブックマークについていたタグはそのままに新規のタグを追加()
    {
        //更新前
        $tags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        foreach($tags as $tag){ $this->bookMarkTagRepository->store($tag->id,$this->bookMarkId); }

        //----

        //新しいタグを追加
        $newTags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        //更新
        $this->bookMarkTagRepository->update($this->bookMarkId,[$newTags[0]->id,$newTags[1]->id,$tags[0]->id,$tags[1]->id]);

        //取得したタグが新しく作ったタグになっているか確認
        // もともとつけていた部分

        foreach ($tags as $tag){
            $this->assertDatabaseHas('book_mark_tags',[
                'book_mark_id' => $this->bookMarkId,
                'tag_id'     => $tag->id,

            ]);
        }

        // 追加した部分
        foreach ($newTags as $tag){
            $this->assertDatabaseHas('book_mark_tags',[
                'book_mark_id' => $this->bookMarkId,
                'tag_id'     => $tag->id,

            ]);
        }
    }

    // 期待
    // 指定したブックマークについていたタグを一部外して､新しく指定したタグをブックマークに紐づける
    // 古いタグは論理削除されている
    public function test_update_ブックマークについていたタグの一部を外す()
    {
        // carbonの時間固定


        //更新前
        $tags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        foreach($tags as $tag){$this->bookMarkTagRepository->store($tag->id,$this->bookMarkId);}

        //----

        $newTags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        //更新
        $this->bookMarkTagRepository->update($this->bookMarkId,[$tags[1]->id,$newTags[0]->id,$newTags[1]->id]);

        // けしたやつ
        $this->assertDatabaseMissing('book_mark_tags',[
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => $tags[0]->id,
        ]);
        //残したやつ
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => $tags[1]->id,

        ]);

        foreach($newTags as $tag){
            $this->assertDatabaseHas('book_mark_tags',[
                'book_mark_id' => $this->bookMarkId,
                'tag_id'     => $tag->id,

            ]);
        }
    }

    // 期待
    // 指定したブックマークについてあるすべてのタグのidを配列形式で取ってくる
    public function test_getOrignalTag()
    {
        $tags = Tag::factory()->count(4)->create(['user_id' => $this->userId]);

        // 登録
        foreach($tags as $tag){ $this->bookMarkTagRepository->store($tag->id,$this->bookMarkId); }

        // 取得
        $bookMarkTags = $this->bookMarkTagRepository->getOrignalTag($this->bookMarkId);

        // print_r($bookMarkTags);

        // 登録したタグがあるかどうか
        foreach ($bookMarkTags as $bookMarkTag) {
            $this->assertContains($bookMarkTag,$bookMarkTags);
        }
    }

    // 期待
    // nullのデータが消されて
    // 新しいタグのデータが登録されている
    // 元のデータに変化がない
    // 条件
    // 新しくタグがついていた
    public function test_ProcessingifOriginalHasNoTags_新規タグあり(){
        // tag_id = nullのデータ作成
        $this->bookMarkTagRepository->store(null,$this->bookMarkId);

        // 適当にタグを作る
        $tag = Tag::create([
            'user_id' => $this->userId,
            'name'    => 'test',
        ]);

        $updateTagList = [$tag->id];

        $this->bookMarkTagRepository->ProcessingifOriginalHasNoTags($this->bookMarkId,$updateTagList);

        // nullのデータが消えているか
        $this->assertDatabaseMissing('book_mark_tags',[
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => null,

        ]);

        // 新しいタグが登録されているか
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => $updateTagList[0],

        ]);
    }

    // 期待
    // 元のデータに変化がない
    // 条件
    // 新しくタグがついていなかった
    public function test_ProcessingifOriginalHasNoTags_新規タグなし(){
        // tag_id = nullのデータ作成
        $this->bookMarkTagRepository->store(null,$this->bookMarkId);

        $this->bookMarkTagRepository->ProcessingifOriginalHasNoTags($this->bookMarkId,[]);

        // nullのデータが残っているか
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => null,

        ]);
    }

    // 期待
    // 新しく追加されたタグのデータがある
    // 元のデータに変化がない
    // 条件
    // 削除されたタグなし
    // 新しく追加されたタグあり
    public function test_ProcessingifOriginalHasAnyTags_削除されたタグなし_新しく追加されたタグあり()
    {
        // もともとついていたタグ
        $oldTag = Tag::factory()->create(['user_id' => $this->userId]);


        BookMarkTag::create([
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => $oldTag->id
        ]);

        // 新しくつけるタグ
        $newTag = Tag::factory()->create(['user_id' => $this->userId]);

        $this->bookMarkTagRepository->ProcessingifOriginalHasAnyTags($this->bookMarkId,[$oldTag->id],[$oldTag->id,$newTag->id]);

        // 古いデータが残っている
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => $oldTag->id,

        ]);

        // 新しいデータが登録されている
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => $newTag->id,

        ]);
    }

    // 期待
    // 元のデータに変化がない
    // 条件
    // 削除されたタグなし
    // 新しく追加されたタグなし
    public function test_ProcessingifOriginalHasAnyTags_削除されたタグなし_新しく追加されたタグなし()
    {
        // もともとついていたタグ
        $oldTag = Tag::factory()->create(['user_id' => $this->userId]);


        BookMarkTag::create([
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => $oldTag->id
        ]);

        $this->bookMarkTagRepository->ProcessingifOriginalHasAnyTags($this->bookMarkId,[$oldTag->id],[$oldTag->id]);

        // 古いデータが残っている
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => $oldTag->id,

        ]);
    }

    // 期待
    // もともとついていたタグのデータが削除されれる
    // 新しく追加されたタグのデータがある
    // 条件
    // 削除されたタグあり
    // 新しく追加されたタグあり
    public function test_ProcessingifOriginalHasAnyTags_削除されたタグあり_新しく追加されたタグあり()
    {
        // もともとついていたタグ
        $oldTag = Tag::factory()->create(['user_id' => $this->userId]);

        BookMarkTag::create([
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => $oldTag->id
        ]);

        // 新しくつけるタグ
        $newTag = Tag::factory()->create(['user_id' => $this->userId]);

        $this->bookMarkTagRepository->ProcessingifOriginalHasAnyTags($this->bookMarkId,[$oldTag->id],[$newTag->id]);

        // 古いデータが消えているか
        $this->assertDatabaseMissing('book_mark_tags',[
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => $oldTag->id,

        ]);

        // 新しいデータが登録されている
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => $newTag->id,

        ]);
    }

    // 期待
    // もともとついていたタグのデータが削除されれる
    // 何もタグがついていないというデータが追加される
    // 条件
    // 削除されたタグあり
    // 新しく追加されたタグなし
    public function test_ProcessingifOriginalHasAnyTags_削除されたタグあり_新しく追加されたタグなし()
    {
        // もともとついていたタグ
        $oldTag = Tag::factory()->create(['user_id' => $this->userId]);

        BookMarkTag::create([
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => $oldTag->id
        ]);

        $this->bookMarkTagRepository->ProcessingifOriginalHasAnyTags($this->bookMarkId,[$oldTag->id],[]);

        // 古いデータが消えているか
        $this->assertDatabaseMissing('book_mark_tags',[
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => $oldTag->id,

        ]);

        // 何もタグがついていないというデータが追加される
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookMarkId,
            'tag_id'     => null,

        ]);
    }
}

