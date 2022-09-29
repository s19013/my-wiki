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
    // 引数2に指定した記事に､引数1に指定したタグのIdをデータベースに保管する
    // 条件
    // 引数1には数字が渡される
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

    // 期待
    // 引数2に指定した記事に､引数1に指定したnullをデータベースに保管する
    // 条件
    // 引数1にはnullが渡される
    public function test_storeArticleTag_タグをつけなかった場合()
    {
        $this->articleTagModel->storeArticleTag(null,$this->articleId);

        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id' => null
        ]);
    }

    // 期待
    // 引数2に指定した記事から引数1に指定されたタグを論理削除する
    public function test_deleteArticleTag_紐づけたタグを論理削除する()
    {
        Carbon::setTestNow(Carbon::now());

        $tag = Tag::create([
            'user_id' => $this->userId,
            'name'    => 'articleTagModelStoreTest',
        ]);

        $this->articleTagModel->storeArticleTag($tag->id,$this->articleId);

        $this->articleTagModel->deleteArticleTag($tag->id,$this->articleId);

        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => $tag->id,
            'deleted_at' => Carbon::now(),
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

        foreach ($tags as $tag) { $this->articleTagModel->storeArticleTag($tag->id,$this->articleId); }

        // タグを取得
        $articleTags = $this->articleTagModel->serveTagsRelatedToArticle($this->articleId,$this->userId);

        //名前とidが一緒かどうか

        $IdList = [];

        foreach ($articleTags as $articleTag){array_push($IdList,$articleTag->id);}

        $nameList = [];
        foreach ($articleTags as $articleTag){ array_push($nameList,$articleTag->name);}
        foreach ($tags as $tag){
            $this->assertContains($tag->id,$IdList);
            $this->assertContains($tag->name,$nameList);
        }
    }

    // 期待
    // 引数1に指定した記事に紐付けられたタグの名前とnullを取得
    // 条件
    // 登録時に何もタグをつけなかった
    public function test_serveTagsRelatedToArticle_登録時にタグを紐づけなかった場合()
    {
        $this->articleTagModel->storeArticleTag(null,$this->articleId);

        $articleTags = $this->articleTagModel->serveTagsRelatedToArticle($this->articleId,$this->userId);

        //idがnull
        $this->assertSame($articleTags[0]->id,null);
    }

    // 期待
    // 指定した記事についていたタグをすべて外して､新しく指定したタグを記事に紐づける
    // 古いタグは論理削除されている
    public function test_updateArticleTag_記事についていたタグと新規のタグを入れ替える()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        //更新前
        $tags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        // 記事に紐づける
        foreach($tags as $tag){ $this->articleTagModel->storeArticleTag($tag->id,$this->articleId); }

        //----

        //新しいタグを追加
        $newTags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        //更新
        $this->articleTagModel->updateArticleTag($this->articleId,[$newTags[0]->id,$newTags[1]->id]);

        // 更新前のデータ(ちゃんと消されたか確認する)
        foreach($tags as $tag){
            $this->assertDatabaseHas('article_tags',[
                'article_id' => $this->articleId,
                'tag_id'     => $tag->id,
                'deleted_at' => Carbon::now()
            ]);
        }

        // 更新後のデータ
        foreach($newTags as $tag){
            $this->assertDatabaseHas('article_tags',[
                'article_id' => $this->articleId,
                'tag_id'     => $tag->id,
                'deleted_at' => null
            ]);
        }
    }

    // 期待
    // 指定したタグが何もついてなかった記事に､新しく指定したタグを記事に紐づける
    // nullは論理削除されている
    public function test_updateArticleTag_タグがついていなかった記事にタグをつける()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        $this->articleTagModel->storeArticleTag(null,$this->articleId);

        //----

        //新しいタグを追加
        $newTags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        //更新
        $this->articleTagModel->updateArticleTag($this->articleId,[$newTags[0]->id,$newTags[1]->id]);

        // 更新前のデータ(ちゃんと消されたか確認する)
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => null,
            'deleted_at' => Carbon::now()
        ]);

        // 更新後のデータ
        foreach($newTags as $tag){
            $this->assertDatabaseHas('article_tags',[
                'article_id' => $this->articleId,
                'tag_id'     => $tag->id,
                'deleted_at' => null
            ]);
        }
    }

    // 期待
    // 指定した記事についていたタグはそのままに新規のタグを追加
    public function test_updateArticleTag_記事についていたタグはそのままに新規のタグを追加()
    {
        //更新前
        $tags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        foreach($tags as $tag){ $this->articleTagModel->storeArticleTag($tag->id,$this->articleId); }

        //----

        //新しいタグを追加
        $newTags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        //更新
        $this->articleTagModel->updateArticleTag($this->articleId,[$newTags[0]->id,$newTags[1]->id,$tags[0]->id,$tags[1]->id]);

        //取得したタグが新しく作ったタグになっているか確認
        // もともとつけていた部分

        foreach ($tags as $tag){
            $this->assertDatabaseHas('article_tags',[
                'article_id' => $this->articleId,
                'tag_id'     => $tag->id,
                'deleted_at' => null
            ]);
        }

        // 追加した部分
        foreach ($newTags as $tag){
            $this->assertDatabaseHas('article_tags',[
                'article_id' => $this->articleId,
                'tag_id'     => $tag->id,
                'deleted_at' => null
            ]);
        }
    }

    // 期待
    // 指定した記事についていたタグを一部外して､新しく指定したタグを記事に紐づける
    // 古いタグは論理削除されている
    public function test_updateArticleTag_記事についていたタグの一部を外す()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        //更新前
        $tags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        foreach($tags as $tag){$this->articleTagModel->storeArticleTag($tag->id,$this->articleId);}

        //----

        $newTags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        //更新
        $this->articleTagModel->updateArticleTag($this->articleId,[$tags[1]->id,$newTags[0]->id,$newTags[1]->id]);

        // けしたやつ
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => $tags[0]->id,
            'deleted_at' => Carbon::now()
        ]);
        //残したやつ
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => $tags[1]->id,
            'deleted_at' => null
        ]);

        foreach($newTags as $tag){
            $this->assertDatabaseHas('article_tags',[
                'article_id' => $this->articleId,
                'tag_id'     => $tag->id,
                'deleted_at' => null
            ]);
        }
    }

    // 期待
    // 指定した記事についてあるすべてのタグのidを配列形式で取ってくる
    public function test_getOrignalTag()
    {
        $tags = Tag::factory()->count(4)->create(['user_id' => $this->userId]);

        // 登録
        foreach($tags as $tag){ $this->articleTagModel->storeArticleTag($tag->id,$this->articleId); }

        // 取得
        $articleTags = $this->articleTagModel->getOrignalTag($this->articleId);

        // print_r($articleTags);

        // 登録したタグがあるかどうか
        foreach ($articleTags as $articleTag) {
            $this->assertContains($articleTag,$articleTags);
        }
    }

    // 期待
    // procesOriginalArticleDoesNotHaveAnyTagsの帰り値がnullである
    // 元のデータに変化がない
    // 条件
    // 元の記事にタグがついている
    public function test_procesOriginalArticleDoesNotHaveAnyTags_元の記事にタグがついている()
    {
        $tags = Tag::factory()->count(4)->create(['user_id' => $this->userId]);

        foreach ($tags as $tag){ $this->articleTagModel->storeArticleTag($tag->id,$this->articleId); }

        $returnValue = $this->articleTagModel->procesOriginalArticleDoesNotHaveAnyTags(
            originalTagList:$this->articleTagModel->getOrignalTag($this->articleId),
            articleId:$this->articleId,
            updatedTagList:[$tags[0]->id,$tags[1]->id,$tags[2]->id,$tags[3]->id]
        );

        // 何もしないのでnullが返される
        $this->assertNull($returnValue);

        // 登録されているタグに変化がない
        foreach ($tags as $tag){
            $this->assertDatabaseHas('article_tags',[
                'article_id' => $this->articleId,
                'tag_id'     => $tag->id,
                'deleted_at' => null
            ]);
        }
        // procesOriginalArticleDoesNotHaveAnyTagsでは新規タグの登録はしないからここではテストしない

    }

    // 期待
    // procesOriginalArticleDoesNotHaveAnyTagsの帰り値がnullである
    // tag_id = nullのデータが論理削除されている
    // 条件
    // 元の記事にタグがついていない,新しくタグを追加しようとしている
    public function test_procesOriginalArticleDoesNotHaveAnyTags_元の記事にタグがついていない_新しくタグを追加()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        $this->articleTagModel->storeArticleTag(null,$this->articleId);

        $tags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        $returnValue = $this->articleTagModel->procesOriginalArticleDoesNotHaveAnyTags(
            originalTagList:$this->articleTagModel->getOrignalTag($this->articleId),
            articleId:$this->articleId,
            updatedTagList:[$tags[0]->id,$tags[1]->id]
        );

        $this->assertNull($returnValue);

        //nullのデータが論理削除されている
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => null,
            'deleted_at' => Carbon::now()
        ]);

        // procesOriginalArticleDoesNotHaveAnyTagsでは新規タグの登録はしないからここではテストしない
    }

    // 期待
    // procesOriginalArticleDoesNotHaveAnyTagsの帰り値がTrueである
    // tag_id = nullのデータが論理削除されている
    // 元のデータに変化がない
    // 条件
    // 元の記事にタグがついていない_タグも追加しない
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

        //元データに変化なし
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => null,
            'deleted_at' => null
        ]);
        // procesOriginalArticleDoesNotHaveAnyTagsでは新規タグの登録はしないからここではテストしない
    }

    // 期待
    // procesOriginalArticleDeleteAllTagsの帰り値がnullである
    // tag_id = nullのデータが新しく追加されている
    // 条件
    // 元の記事にタグをすべてけした,追加タグなし
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


    // 期待
    // procesOriginalArticleDeleteAllTagsの帰り値がnullである
    // 条件
    // 元の記事にタグをすべてけした,追加タグあり
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

        // もとあったデータの論理削除､新しいデータの追加は別の関数でやるのでここではチェックしない
    }

    // 期待
    // procesOriginalArticleDeleteAllTagsの帰り値がnullである
    // 元のデータに変化がない
    // 条件
    // 元の記事にタグがついてない,追加タグなし
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

        // 元のデータに変化がない
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => null,
            'deleted_at' => null
        ]);
    }

    // 期待
    // procesOriginalArticleDeleteAllTagsの帰り値がnullである
    // 元のデータに変化がない
    // 条件
    // 元の記事にタグがついてない,追加タグなし
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

        // 元のデータに変化がない
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => null,
            'deleted_at' => null
        ]);
        // 新しいデータの追加は別の関数でやるのでここではチェックしない
    }
}

