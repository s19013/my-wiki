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

    public function test_store_一意のタグ()
    {
        $this->tagModel->store($this->userId,"testTag");

        $this->assertDatabaseHas('tags',[
            'name' => 'testTag',
            'user_id'  => $this->userId
        ]);

    }

    public function test_search()
    {
        $this->tagModel->store($this->userId,"searchTestTag");

        $tag = $this->tagModel->search($this->userId,"searchTestTag");
        $this->assertSame($tag[0]->name,"searchTestTag");
    }

    public function test_isAllreadyExists_登録済み()
    {
        $this->tagModel->store($this->userId,"isAllreadyExistsTestTag");

        $this->assertTrue($this->tagModel->isAllreadyExists($this->userId,"isAllreadyExistsTestTag"));
    }

    public function test_isAllreadyExists_未登録()
    {
        $this->assertFalse($this->tagModel->isAllreadyExists($this->userId,"isAllreadyExistsTestTag"));
    }
}
