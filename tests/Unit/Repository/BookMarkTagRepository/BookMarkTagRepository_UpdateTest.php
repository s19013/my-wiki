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

class BookMarkTagRepositoryTest_UpdateTest extends TestCase
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
}
