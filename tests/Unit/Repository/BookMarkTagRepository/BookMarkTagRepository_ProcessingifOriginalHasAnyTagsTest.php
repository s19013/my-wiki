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

class BookMarkTagRepository_ProcessingifOriginalHasAnyTagsTest extends TestCase
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
