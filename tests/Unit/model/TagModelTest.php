<?php

namespace Tests\Unit\model;

use Tests\TestCase;

use App\Models\User;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

// データベース関係で使う
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class TagModelTest extends TestCase
{
    // テストしたらリセットする
    use RefreshDatabase;

    private $tagModel;
    private $userId;

    public function setup():void
    {
        parent::setUp();
        $this->tagModel = new Tag();

        // ユーザーを用意
        $user = User::create([
            'name'     => "tester",
            'email'    => "hideya670@gmail.com",
            'password' => "gemini0522",
        ]);

        $this->userId = $user->id;
    }

    // 期待
    // 引数2の文字列がデータベースに保存される
    public function test_store_一意のタグ()
    {
        $this->tagModel->store($this->userId,"testTag");

        $this->assertDatabaseHas('tags',[
            'name' => 'testTag',
            'user_id'  => $this->userId
        ]);

    }

    // 期待
    // dbの中から 引数2の文字列をnameカラムに含むタグのデータをとってくる
    public function test_search()
    {
        $hitTag = Tag::factory()->create( ["user_id" => $this->userId] );
        Tag::factory()->count(5)->create( ["user_id" => $this->userId] );

        $receivedTags = $this->tagModel->search($this->userId,"searchTestTag");

        //名前とidが一緒かどうか
        $IdList = [];
        foreach ($receivedTags as $tag){array_push($IdList,$tag->id);}

        $nameList = [];
        foreach ($receivedTags as $tag){ array_push($nameList,$tag->name);}

        $this->assertSame($tag[0]->name,"searchTestTag");
    }

    // 期待
    // isAllreadyExistsがTrueを返す
    // 条件
    // 指定したユーザーが引数に渡された文字列"url"をすでに登録している
    public function test_isAllreadyExists_登録済み()
    {
        $this->tagModel->store($this->userId,"isAllreadyExistsTestTag");

        $this->assertTrue($this->tagModel->isAllreadyExists($this->userId,"isAllreadyExistsTestTag"));
    }

    // 期待
    // isAllreadyExistsがFalseを返す
    // 条件
    // 指定したユーザーが引数に渡された文字列"url"をまだ登録していない
    public function test_isAllreadyExists_未登録()
    {
        $this->assertFalse($this->tagModel->isAllreadyExists($this->userId,"isAllreadyExistsTestTag"));
    }
}
