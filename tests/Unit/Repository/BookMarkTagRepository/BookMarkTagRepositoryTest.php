<?php

namespace Tests\Unit\Repository\BookMarkTagRepository;

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
        $deletedTags =Tag::factory()->count(3)->create([
            'user_id' => $this->userId,
        ]);

        // 登録
        foreach ($tags as $tag) { $this->bookMarkTagRepository->store($tag->id,$this->bookMarkId); }
        foreach ($deletedTags as $tag) { $this->bookMarkTagRepository->store($tag->id,$this->bookMarkId); }

        //
        foreach ($deletedTags as $tag) { $this->bookMarkTagRepository->delete($tag->id,$this->bookMarkId); }

        // タグを取得
        $bookMarkTags = $this->bookMarkTagRepository->serveTagsRelatedToBookMark($this->bookMarkId,$this->userId);

        //名前とidが一緒かどうか

        $IdList = [];

        foreach ($bookMarkTags as $bookMarkTag){array_push($IdList,$bookMarkTag->id);}

        $nameList = [];
        foreach ($bookMarkTags as $bookMarkTag){ array_push($nameList,$bookMarkTag->name);}

        // 個数確認
        $this->assertSame(count($tags),count($IdList));
        $this->assertSame(count($tags),count($nameList));

        foreach ($tags as $tag){
            $this->assertContains($tag->id,$IdList);
            $this->assertContains($tag->name,$nameList);
        }

        foreach ($deletedTags as $tag){
            $this->assertNotContains($tag->id,$IdList);
            $this->assertNotContains($tag->name,$nameList);
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
}

