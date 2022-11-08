<?php

namespace Tests\Unit\Repository;

use Tests\TestCase;

use App\Models\User;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

// データベース関係で使う
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

use App\Repository\TagRepository;

use Carbon\Carbon;

class TagRepositoryTest extends TestCase
{
    // テストしたらリセットする
    use RefreshDatabase;

    private $TagRepository;
    private $userId;

    public function setup():void
    {
        parent::setUp();
        $this->tagRepository = new TagRepository();

        // ユーザーを用意
        $user = User::create([
            'name'     => "tester",
            'email'    => "hideya670@gmail.com",
            'password' => "gemini0522",
        ]);

        $this->userId = $user->id;
    }

    // 期待
    // 関数がTrueを返す
    // 引数2の文字列がデータベースに保存される
    public function test_store_一意のタグ()
    {
        $this->assertTrue($this->tagRepository->store($this->userId,"testTag"));

        $this->assertDatabaseHas('tags',[
            'name'       => 'testTag',
            'user_id'    => $this->userId,
            'deleted_at' => null
        ]);

    }

    // 期待
    // 関数がfalseを返す
    public function test_store_同じタグを登録する()
    {
        Tag::create([
            'name'    => 'testTag',
            'user_id' => $this->userId
        ]);

        $this->assertFalse($this->tagRepository->store($this->userId,"testTag"));
    }

    // 期待
    // dbの中から 引数2の文字列をnameカラムに含むタグのデータをとってくる
    public function test_search()
    {
        // 検索にかかるタグ
        Tag::factory()->create( [
            "name"    => "TestTag",
            "user_id" => $this->userId
        ] );
        Tag::factory()->count(5)->create( ["user_id" => $this->userId] );

        $receivedTags = $this->tagRepository->search($this->userId,"TestTag");

        //名前とidが一緒かどうか
        $IdList = [];
        foreach ($receivedTags as $tag){array_push($IdList,$tag->id);}

        $nameList = [];
        foreach ($receivedTags as $tag){ array_push($nameList,$tag->name);}

        $this->assertContains("TestTag",$nameList);
    }


    // 期待
    // 指定したidのタグ名が更新されている
    // 関数がTrueを返す
    // 条件
    // 変更後のタグ名がtagテーブルで一意だった場合
    public function test_updateTag_変更後のタグ名がtagテーブルで一意だった()
    {
        $tag = Tag::create([
            'name'    => 'beforeUpdate',
            'user_id' => $this->userId
        ]);

        $this->assertTrue($this->tagRepository->update($this->userId,$tag->id,'afterUpdate'));

        $this->assertDatabaseHas('tags',[
            'name'       => 'afterUpdate',
            'user_id'    => $this->userId,
            'deleted_at' => null
        ]);

    }

    // 期待
    // 関数がfalseを返す
    // 条件
    // 変更後のタグ名がtagテーブルに既に登録されていた場合
    public function test_updateTag_変更後のタグ名がtagテーブルに既に存在していた()
    {
        $allreadyExist = Tag::create([
            'name'    => 'allready',
            'user_id' => $this->userId
        ]);

        $tag = Tag::create([
            'name'    => 'beforeUpdate',
            'user_id' => $this->userId
        ]);

        $this->assertFalse($this->tagRepository->update($this->userId,$tag->id,'allready'));
    }

    // 期待
    // 指定したidのタグが論理削除される
    public function test_delete()
    {
        Carbon::setTestNow(Carbon::now());

        $tag = Tag::factory()->create([
            'user_id' => $this->userId
        ]);

        $this->tagRepository->delete($tag->id);

        $this->assertDatabaseHas('tags',[
            'id'    => $tag->id,
            'deleted_at' =>Carbon::now()
        ]);
    }

    // 期待
    // isAllreadyExistsがTrueを返す
    // 条件
    // 指定したユーザーが引数に渡された文字列"url"をすでに登録している
    public function test_isAllreadyExists_登録済み()
    {
        $this->tagRepository->store($this->userId,"isAllreadyExistsTestTag");

        $this->assertTrue($this->tagRepository->isAllreadyExists($this->userId,"isAllreadyExistsTestTag"));
    }

    // 期待
    // isAllreadyExistsがFalseを返す
    // 条件
    // 指定したユーザーが引数に渡された文字列"url"をまだ登録していない
    public function test_isAllreadyExists_未登録()
    {
        $this->assertFalse($this->tagRepository->isAllreadyExists($this->userId,"isAllreadyExistsTestTag"));
    }
}