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

class ArticleTagRepositoryTest extends TestCase
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
    // 引数2に指定した記事に､引数1に指定したタグのIdをデータベースに保管する
    // 条件
    // 引数1には数字が渡される
    public function test_store_タグをつけた場合()
    {
        $tag = Tag::create([
            'user_id' => $this->userId,
            'name'    => 'articleTagModelStoreTest',
        ]);

        $this->articleTagRepository->store($tag->id,$this->articleId);

        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id' => $tag->id
        ]);
    }

    // 期待
    // 引数2に指定した記事に､引数1に指定したnullをデータベースに保管する
    // 条件
    // 引数1にはnullが渡される
    public function test_store_タグをつけなかった場合()
    {
        $this->articleTagRepository->store(null,$this->articleId);

        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id' => null
        ]);
    }

    // 期待
    // 引数2に指定した記事から引数1に指定されたタグを物理削除する
    public function test_delete_紐づけたタグを物理削除する()
    {
        Carbon::setTestNow(Carbon::now());

        $tag = Tag::create([
            'user_id' => $this->userId,
            'name'    => 'articleTagModelStoreTest',
        ]);

        $this->articleTagRepository->store($tag->id,$this->articleId);

        $this->articleTagRepository->delete($tag->id,$this->articleId);

        $this->assertDatabaseMissing('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => $tag->id,
        ]);
    }

    // 期待
    // 引数1に指定した記事に紐付けられたタグの名前とタグのidを取得
    // 条件
    // 登録時に何かしらのタグを紐づけた
    public function test_serveTagsRelatedToArticle_登録時にタグを紐づけた場合()
    {
        $tags =Tag::factory()->count(5)->create([
            'user_id' => $this->userId,
        ]);

        foreach ($tags as $tag) { $this->articleTagRepository->store($tag->id,$this->articleId); }

        // タグを取得
        $articleTags = $this->articleTagRepository->serveTagsRelatedToArticle($this->articleId,$this->userId);

        //名前とidが一緒かどうか

        $IdList = [];

        foreach ($articleTags as $articleTag){
            array_push($IdList,$articleTag->id);
        }

        $nameList = [];
        foreach ($articleTags as $articleTag){ array_push($nameList,$articleTag->name);}
        foreach ($tags as $tag){
            $this->assertContains($tag->id,$IdList);
            $this->assertContains($tag->name,$nameList);
        }
    }

    // 期待
    // 空の配列がかえってくる
    // 条件
    // 登録時に何もタグをつけなかった
    public function test_serveTagsRelatedToArticle_登録時にタグを紐づけなかった場合()
    {
        $this->articleTagRepository->store(null,$this->articleId);

        // タグを取得
        $articleTags = $this->articleTagRepository->serveTagsRelatedToArticle($this->articleId,$this->userId);

        $this->assertEmpty($articleTags);
    }

    // 期待
    // 指定した記事についてあるすべてのタグのidを配列形式で取ってくる
    public function test_getOrignalTag()
    {
        $tags = Tag::factory()->count(4)->create(['user_id' => $this->userId]);

        // 登録
        foreach($tags as $tag){ $this->articleTagRepository->store($tag->id,$this->articleId); }

        // 取得
        $articleTags = $this->articleTagRepository->getOrignalTag($this->articleId);

        // print_r($articleTags);

        // 登録したタグがあるかどうか
        foreach ($articleTags as $articleTag) {
            $this->assertContains($articleTag,$articleTags);
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
        $this->articleTagRepository->store(null,$this->articleId);

        // 適当にタグを作る
        $tag = Tag::create([
            'user_id' => $this->userId,
            'name'    => 'test',
        ]);

        $updateTagList = [$tag->id];

        $this->articleTagRepository->ProcessingifOriginalHasNoTags($this->articleId,$updateTagList);

        // nullのデータが消えているか
        $this->assertDatabaseMissing('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => null,

        ]);

        // 新しいタグが登録されているか
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => $updateTagList[0],

        ]);
    }

    // 期待
    // 元のデータに変化がない
    // 条件
    // 新しくタグがついていなかった
    public function test_ProcessingifOriginalHasNoTags_新規タグなし(){
        // tag_id = nullのデータ作成
        $this->articleTagRepository->store(null,$this->articleId);

        $this->articleTagRepository->ProcessingifOriginalHasNoTags($this->articleId,[]);

        // nullのデータが残っているか
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => null,

        ]);
    }
}

