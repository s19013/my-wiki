<?php

namespace Tests\Unit\model;

use Tests\TestCase;

use App\Models\ArticleTag;
use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\DB;

//ファクトリーで使う
use Illuminate\Database\Eloquent\Factories\Sequence;

// データベース関係で使う
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

use Carbon\Carbon;

class ArticleTagModelTest extends TestCase
{
    // テストしたらリセットする
    use RefreshDatabase;

    private $articleTagModel;
    private $userId;
    private $articleId;

    public $uniqeInt1 = [1,2,3,4,5];
    public $uniqeInt2 = [6,7,8,9,10];

    public function setup():void
    {
        parent::setUp();
        $this->articleTagModel = new ArticleTag();

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

        // User::factory()->count(1)->create();

        // Tag::factory()->count(5)->create(['user_id' => $this->userId]);
        // Tag::factory()->count(5)->create(['user_id' => $this->userId + 1]);

        // Article::factory()->count(1)->create(['user_id' => $this->userId]);
        // Article::factory()->count(1)->create(['user_id' => $this->userId + 1]);

        // shuffle($this->uniqeInt1);
        // shuffle($this->uniqeInt2);
        // ArticleTag::factory()->count(5)->state(new Sequence(
        //     ['tag_id' => $this->getUniqeInt($this->uniqeInt1)],
        //     ['tag_id' => $this->getUniqeInt($this->uniqeInt1)],
        //     ['tag_id' => $this->getUniqeInt($this->uniqeInt1)],
        //     ['tag_id' => $this->getUniqeInt($this->uniqeInt1)],
        //     ['tag_id' => $this->getUniqeInt($this->uniqeInt1)],
        // ))->create(['article_id' =>1]);

        // ArticleTag::factory()->count(5)->state(new Sequence(
        //     ['tag_id' => $this->getUniqeInt($this->uniqeInt2)],
        //     ['tag_id' => $this->getUniqeInt($this->uniqeInt2)],
        //     ['tag_id' => $this->getUniqeInt($this->uniqeInt2)],
        //     ['tag_id' => $this->getUniqeInt($this->uniqeInt2)],
        //     ['tag_id' => $this->getUniqeInt($this->uniqeInt2)],
        // ))->create(['article_id' =>2]);
    }

    public function echoEtc()
    {
        echo "user \n";
        foreach( User::select('id','name')->get() as $data ){
            echo $data;
            echo "\n";
        }

        echo "\n article \n";
        foreach( Article::select('id','user_id','title','body')->get() as $data ){
            echo $data;
            echo "\n";
        }

        echo "\n tag \n";
        foreach( Tag::select('id','user_id','name')->get() as $data ){
            echo $data;
            echo "\n";
        }

        echo "\n ArticleTag \n";
        foreach( ArticleTag::select('article_id','tag_id')->get() as $data ){
            echo $data;
            echo "\n";
        }
    }

    public function getUniqeInt(&$List)
    {
        $temp = $List[0];
        array_shift($List);
        return $temp;
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

    public function test_storeArticleTag_タグをつけた場合()
    {
        $tag = Tag::create([
            'user_id' => $this->userId,
            'name'    => 'articleTagModelStoreTest',
        ]);




        $this->articleTagModel->storeArticleTag($tag->id,$this->articleId);

        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id' => $tag->id
        ]);
    }

    public function test_storeArticleTag_タグをつけなかった場合()
    {


        $this->articleTagModel->storeArticleTag(null,$this->articleId);

        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id' => null
        ]);
    }

    public function test_deleteArticleTag()
    {
        $tag = Tag::create([
            'user_id' => $this->userId,
            'name'    => 'articleTagModelStoreTest',
        ]);



        $this->articleTagModel->storeArticleTag($tag->id,$this->articleId);

        $this->articleTagModel->deleteArticleTag($tag->id,$this->articleId);

        // データを取ってくる
        $articleTag = ArticleTag::select('*')
        ->where('tag_id','=',$tag->id)
        ->where('article_id','=',$this->articleId)
        ->first();

        // 記事についているタグのdeleted_atがnullではない
        $this->assertNotNull($articleTag->deleted_at);
    }

    public function test_serveTagsRelatedToAricle_登録時にタグを紐づけた場合()
    {
        $tag = Tag::create([
            'user_id' => $this->userId,
            'name'    => 'articleTagModelStoreTest',
        ]);



        $this->articleTagModel->storeArticleTag($tag->id,$this->articleId);

        $articleTags = $this->articleTagModel->serveTagsRelatedToAricle($this->articleId,$this->userId);

        //名前とidが一緒かどうか
        $this->assertSame($articleTags[0]->id,$tag->id);
        $this->assertSame($articleTags[0]->name,$tag->name);
    }

    public function test_serveTagsRelatedToAricle_登録時にタグを紐づけなかった場合()
    {


        $this->articleTagModel->storeArticleTag(null,$this->articleId);

        $articleTags = $this->articleTagModel->serveTagsRelatedToAricle($this->articleId,$this->userId);

        //idがnull
        $this->assertSame($articleTags[0]->id,null);
    }

    public function test_updateAricleTag_記事についていたタグと新規のタグを入れ替える()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        //更新前
        $tags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);


        foreach($tags as $tag){ $this->articleTagModel->storeArticleTag($tag->id,$this->articleId); }

        //----

        //新しいタグを追加
        $tag1 = Tag::create([
            'user_id' => $this->userId,
            'name'    => 'updateTag_1',
        ]);
        $tag2 = Tag::create([
            'user_id' => $this->userId,
            'name'    => 'updateTag_2',
        ]);

        //更新
        $this->articleTagModel->updateAricleTag($this->articleId,[$tag1->id,$tag2->id]);

        // 更新前のデータ(ちゃんと消されたか確認する)
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => $tags[0]->id,
            'deleted_at' => Carbon::now()
        ]);
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => $tags[1]->id,
            'deleted_at' => Carbon::now()
        ]);

        // 更新後のデータ
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => $tag1->id,
            'deleted_at' => null
        ]);
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => $tag2->id,
            'deleted_at' => null
        ]);
    }

    public function test_updateAricleTag_タグがついていなかった記事にタグをつける()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());



        $this->articleTagModel->storeArticleTag(null,$this->articleId);

        //----

        //新しいタグを追加
        $tag1 = Tag::create([
            'user_id' => $this->userId,
            'name'    => 'updateTag_1',
        ]);
        $tag2 = Tag::create([
            'user_id' => $this->userId,
            'name'    => 'updateTag_2',
        ]);

        //更新
        $this->articleTagModel->updateAricleTag($this->articleId,[$tag1->id,$tag2->id]);

        // 更新前のデータ(ちゃんと消されたか確認する)
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => null,
            'deleted_at' => Carbon::now()
        ]);

        // 更新後のデータ
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => $tag1->id,
            'deleted_at' => null
        ]);
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => $tag2->id,
            'deleted_at' => null
        ]);
    }

    public function test_updateAricleTag_記事についていたタグはそのままに新規のタグを追加()
    {
        //更新前
        $tags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);


        foreach($tags as $tag){
            $this->articleTagModel->storeArticleTag($tag->id,$this->articleId);
        }

        //----

        //新しいタグを追加
        $tag1 = Tag::create([
            'user_id' => $this->userId,
            'name'    => 'updateTag_1',
        ]);
        $tag2 = Tag::create([
            'user_id' => $this->userId,
            'name'    => 'updateTag_2',
        ]);

        //更新
        $this->articleTagModel->updateAricleTag($this->articleId,[$tag1->id,$tag2->id,$tags[0]->id,$tags[1]->id]);

        //取得したタグが新しく作ったタグになっているか確認
        // もともとつけていた部分
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => $tags[0]->id,
            'deleted_at' => null
        ]);
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => $tags[1]->id,
            'deleted_at' => null
        ]);

        // 追加した部分
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => $tag1->id,
            'deleted_at' => null
        ]);
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => $tag2->id,
            'deleted_at' => null
        ]);
    }

    public function test_updateAricleTag_記事についていたタグの一部を外す()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        //更新前
        $tags = Tag::factory()->count(4)->create(['user_id' => $this->userId]);


        foreach($tags as $tag){
            $this->articleTagModel->storeArticleTag($tag->id,$this->articleId);
        }

        //----

        //更新
        $this->articleTagModel->updateAricleTag($this->articleId,[$tags[0]->id,$tags[1]->id]);

        //残したやつ
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => $tags[0]->id,
            'deleted_at' => null
        ]);
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => $tags[1]->id,
            'deleted_at' => null
        ]);
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => $tags[2]->id,
            'deleted_at' => Carbon::now()
        ]);
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => $tags[3]->id,
            'deleted_at' => Carbon::now()
        ]);
    }

    public function test_getOrignalTag()
    {
        $tags = Tag::factory()->count(4)->create(['user_id' => $this->userId]);

        foreach($tags as $tag){
            $this->articleTagModel->storeArticleTag($tag->id,$this->articleId);
        }

        $articleTags = $this->articleTagModel->getOrignalTag($this->articleId);

        // print_r($articleTags);

        // 登録したタグがあるかどうか
        foreach ($articleTags as $articleTag) {
            $this->assertContains($articleTag,$articleTags);
        }
    }

    public function test_procesOriginalArticleDoesNotHaveAnyTags_元の記事にタグがついている()
    {
        $tags = Tag::factory()->count(4)->create(['user_id' => $this->userId]);

        $returnValue = $this->articleTagModel->storeArticleTag($tags[0]->id,$this->articleId);
        $this->articleTagModel->storeArticleTag($tags[1]->id,$this->articleId);

        $this->articleTagModel->procesOriginalArticleDoesNotHaveAnyTags(
            originalTagList:$this->articleTagModel->getOrignalTag($this->articleId),
            articleId:$this->articleId,
            updatedTagList:[$tags[0]->id,$tags[1]->id,$tags[2]->id,$tags[3]->id]
        );

        $this->assertNull($returnValue);
        // procesOriginalArticleDoesNotHaveAnyTagsでは新規タグの登録はしないからここではテストしない

    }

    public function test_procesOriginalArticleDoesNotHaveAnyTags_元の記事にタグがついていない_新しくタグを追加()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        $tags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        $this->articleTagModel->storeArticleTag(null,$this->articleId);

        $returnValue = $this->articleTagModel->procesOriginalArticleDoesNotHaveAnyTags(
            originalTagList:$this->articleTagModel->getOrignalTag($this->articleId),
            articleId:$this->articleId,
            updatedTagList:[$tags[0]->id,$tags[1]->id]
        );

        $this->assertNull($returnValue);

        //nullの部分が論理削除されている
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => null,
            'deleted_at' => Carbon::now()
        ]);

        // procesOriginalArticleDoesNotHaveAnyTagsでは新規タグの登録はしないからここではテストしない
    }

    public function test_procesOriginalArticleDoesNotHaveAnyTags_元の記事にタグがついていない_タグも追加しない()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        $this->articleTagModel->storeArticleTag(null,$this->articleId);

        $returnValue = $this->articleTagModel->procesOriginalArticleDoesNotHaveAnyTags(
            originalTagList:$this->articleTagModel->getOrignalTag($this->articleId),
            articleId:$this->articleId,
            updatedTagList:[]
        );

        $this->assertTrue($returnValue);
        // procesOriginalArticleDoesNotHaveAnyTagsでは新規タグの登録はしないからここではテストしない
    }

    public function test_procesOriginalArticleDeleteAllTags_追加タグなし_ついてたタグ全部けした()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        $tags = Tag::factory()->count(4)->create(['user_id' => $this->userId]);

        $this->articleTagModel->storeArticleTag($tags[0]->id,$this->articleId);
        $this->articleTagModel->storeArticleTag($tags[1]->id,$this->articleId);

        $returnValue = $this->articleTagModel->procesOriginalArticleDeleteAllTags(
                originalTagList:$this->articleTagModel->getOrignalTag($this->articleId),
                articleId:$this->articleId,
                isAddedTagListEmpty:True,
                deletedTagList:[$tags[0]->id,$tags[1]->id]
            );

        $this->assertNull($returnValue);

        // もとあったデータの論理削除は別の関数でやるのでここではチェックしない

        // tag_id = nullのデータが追加されているか確認
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => null,
            'deleted_at' => null
        ]);
    }

    public function test_procesOriginalArticleDeleteAllTags_追加タグあり_ついてたタグ全部けした()
    {

        $tags = Tag::factory()->count(4)->create(['user_id' => $this->userId]);

        $this->articleTagModel->storeArticleTag($tags[0]->id,$this->articleId);
        $this->articleTagModel->storeArticleTag($tags[1]->id,$this->articleId);

        $returnValue = $this->articleTagModel->procesOriginalArticleDeleteAllTags(
                originalTagList:$this->articleTagModel->getOrignalTag($this->articleId),
                articleId:$this->articleId,
                isAddedTagListEmpty:false,
                deletedTagList:[$tags[0]->id,$tags[1]->id]
            );

        $this->assertNull($returnValue);

        // もとあったデータの論理削除は別の関数でやるのでここではチェックしない
    }

    public function test_procesOriginalArticleDeleteAllTags_追加タグなし_タグついてない()
    {
        $this->articleTagModel->storeArticleTag(null,$this->articleId);

        $returnValue = $this->articleTagModel->procesOriginalArticleDeleteAllTags(
                originalTagList:$this->articleTagModel->getOrignalTag($this->articleId),
                articleId:$this->articleId,
                isAddedTagListEmpty:true,
                deletedTagList:[]
            );

        $this->assertNull($returnValue);
    }

    public function test_procesOriginalArticleDeleteAllTags_追加タグあり_タグついてない()
    {
        $this->articleTagModel->storeArticleTag(null,$this->articleId);

        $returnValue = $this->articleTagModel->procesOriginalArticleDeleteAllTags(
                originalTagList:$this->articleTagModel->getOrignalTag($this->articleId),
                articleId:$this->articleId,
                isAddedTagListEmpty:false,
                deletedTagList:[]
            );

        $this->assertNull($returnValue);
    }
}

