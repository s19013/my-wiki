<?php

namespace Tests\Unit\Repository\ArticleRepository;

use Tests\TestCase;

use App\Models\ArticleTag;
use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\DB;

use App\Repository\ArticleTagRepository;

//ファクトリーで使う
use Illuminate\Database\Eloquent\Factories\Sequence;

// データベース関係で使う
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

use Carbon\Carbon;

class ArticleTagRepository_ProcessingifOriginalHasAnyTagsTest extends TestCase
{
    // テストしたらリセットする
    use RefreshDatabase;

    private $articleTagRepository;
    private $userId;
    private $articleId;

    public function setup():void
    {
        parent::setUp();
        $this->articleTagRepository = new ArticleTagRepository();


        // ユーザーを用意
        $user = User::create([
            'name'     => "tester",
            'email'    => "hideya670@gmail.com",
            'password' => "gemini0522",
        ]);

        $this->userId = $user->id;

        // 記事を用意
        $article = Article::create([
            'user_id' => $this->userId,
            'title'   => 'testTitle',
            'body'    => 'testBody',
        ]);

        $this->articleId = $article->id;
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


        ArticleTag::create([
            'article_id' => $this->articleId,
            'tag_id'     => $oldTag->id
        ]);

        // 新しくつけるタグ
        $newTag = Tag::factory()->create(['user_id' => $this->userId]);

        $this->articleTagRepository->ProcessingifOriginalHasAnyTags($this->articleId,[$oldTag->id],[$oldTag->id,$newTag->id]);

        // 古いデータが残っている
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => $oldTag->id,

        ]);

        // 新しいデータが登録されている
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
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


        ArticleTag::create([
            'article_id' => $this->articleId,
            'tag_id'     => $oldTag->id
        ]);

        $this->articleTagRepository->ProcessingifOriginalHasAnyTags($this->articleId,[$oldTag->id],[$oldTag->id]);

        // 古いデータが残っている
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
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

        ArticleTag::create([
            'article_id' => $this->articleId,
            'tag_id'     => $oldTag->id
        ]);

        // 新しくつけるタグ
        $newTag = Tag::factory()->create(['user_id' => $this->userId]);

        $this->articleTagRepository->ProcessingifOriginalHasAnyTags($this->articleId,[$oldTag->id],[$newTag->id]);

        // 古いデータが消えているか
        $this->assertDatabaseMissing('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => $oldTag->id,

        ]);

        // 新しいデータが登録されている
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
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

        ArticleTag::create([
            'article_id' => $this->articleId,
            'tag_id'     => $oldTag->id
        ]);

        $this->articleTagRepository->ProcessingifOriginalHasAnyTags($this->articleId,[$oldTag->id],[]);

        // 古いデータが消えているか
        $this->assertDatabaseMissing('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => $oldTag->id,

        ]);

        // 何もタグがついていないというデータが追加される
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => null,

        ]);
    }
}
