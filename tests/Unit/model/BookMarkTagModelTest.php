<?php

namespace Tests\Unit\model;

use Tests\TestCase;

use App\Models\BookMarkTag;
use App\Models\BookMark;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\DB;

//ファクトリーで使う
use Illuminate\Database\Eloquent\Factories\Sequence;

// データベース関係で使う
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

use Carbon\Carbon;

class BookMarkTagModelTest extends TestCase
{
    // テストしたらリセットする
    use RefreshDatabase;

    private $bookmarkTagModel;
    private $userId;
    private $bookmarkId;

    public $uniqeInt1 = [1,2,3,4,5];
    public $uniqeInt2 = [6,7,8,9,10];

    public function setup():void
    {
        parent::setUp();
        $this->bookmarkTagModel = new BookMarkTag();

        // ユーザーを用意
        $user = User::create([
            'name'     => "tester",
            'email'    => "hideya670@gmail.com",
            'password' => "gemini0522",
        ]);

        $this->userId = $user->id;

        // ブックマークを用意
        $bookmark = BookMark::create([
            'user_id' => $this->userId,
            'title'   => 'testTitle',
            'url'    => 'https://github.com/s19013/my-wiki/',
        ]);

        $this->bookmarkId = $bookmark->id;

        // User::factory()->count(1)->create();

        // Tag::factory()->count(5)->create(['user_id' => $this->userId]);
        // Tag::factory()->count(5)->create(['user_id' => $this->userId + 1]);

        // BookMark::factory()->count(1)->create(['user_id' => $this->userId]);
        // BookMark::factory()->count(1)->create(['user_id' => $this->userId + 1]);

        // shuffle($this->uniqeInt1);
        // shuffle($this->uniqeInt2);
        // BookMarkTag::factory()->count(5)->state(new Sequence(
        //     ['tag_id' => $this->getUniqeInt($this->uniqeInt1)],
        //     ['tag_id' => $this->getUniqeInt($this->uniqeInt1)],
        //     ['tag_id' => $this->getUniqeInt($this->uniqeInt1)],
        //     ['tag_id' => $this->getUniqeInt($this->uniqeInt1)],
        //     ['tag_id' => $this->getUniqeInt($this->uniqeInt1)],
        // ))->create(['book_mark_id' =>1]);

        // BookMarkTag::factory()->count(5)->state(new Sequence(
        //     ['tag_id' => $this->getUniqeInt($this->uniqeInt2)],
        //     ['tag_id' => $this->getUniqeInt($this->uniqeInt2)],
        //     ['tag_id' => $this->getUniqeInt($this->uniqeInt2)],
        //     ['tag_id' => $this->getUniqeInt($this->uniqeInt2)],
        //     ['tag_id' => $this->getUniqeInt($this->uniqeInt2)],
        // ))->create(['book_mark_id' =>2]);
    }

    public function echoEtc()
    {
        echo "user \n";
        foreach( User::select('id','name')->get() as $data ){
            echo $data;
            echo "\n";
        }

        echo "\n bookmark \n";
        foreach( BookMark::select('id','user_id','title','url')->get() as $data ){
            echo $data;
            echo "\n";
        }

        echo "\n tag \n";
        foreach( Tag::select('id','user_id','name')->get() as $data ){
            echo $data;
            echo "\n";
        }

        echo "\n BookMarkTag \n";
        foreach( BookMarkTag::select('book_mark_id','tag_id')->get() as $data ){
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

    public function test_storeBookMarkTag_タグをつけた場合()
    {
        $tag = Tag::create([
            'user_id' => $this->userId,
            'name'    => 'bookmarkTagModelStoreTest',
        ]);

        $this->bookmarkTagModel->storeBookMarkTag($tag->id,$this->bookmarkId);

        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookmarkId,
            'tag_id' => $tag->id
        ]);
    }

    public function test_storeBookMarkTag_タグをつけなかった場合()
    {


        $this->bookmarkTagModel->storeBookMarkTag(null,$this->bookmarkId);

        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookmarkId,
            'tag_id' => null
        ]);
    }

    public function test_deleteBookMarkTag()
    {
        $tag = Tag::create([
            'user_id' => $this->userId,
            'name'    => 'bookmarkTagModelStoreTest',
        ]);



        $this->bookmarkTagModel->storeBookMarkTag($tag->id,$this->bookmarkId);

        $this->bookmarkTagModel->deleteBookMarkTag($tag->id,$this->bookmarkId);

        // データを取ってくる
        $bookmarkTag = BookMarkTag::select('*')
        ->where('tag_id','=',$tag->id)
        ->where('book_mark_id','=',$this->bookmarkId)
        ->first();

        // 記事についているタグのdeleted_atがnullではない
        $this->assertNotNull($bookmarkTag->deleted_at);
    }

    public function test_serveTagsRelatedToBookMark_登録時にタグを紐づけた場合()
    {
        $tag = Tag::create([
            'user_id' => $this->userId,
            'name'    => 'bookmarkTagModelStoreTest',
        ]);



        $this->bookmarkTagModel->storeBookMarkTag($tag->id,$this->bookmarkId);

        $bookmarkTags = $this->bookmarkTagModel->serveTagsRelatedToBookMark($this->bookmarkId,$this->userId);

        //名前とidが一緒かどうか
        $this->assertSame($bookmarkTags[0]->id,$tag->id);
        $this->assertSame($bookmarkTags[0]->name,$tag->name);
    }

    public function test_serveTagsRelatedToBookMark_登録時にタグを紐づけなかった場合()
    {


        $this->bookmarkTagModel->storeBookMarkTag(null,$this->bookmarkId);

        $bookmarkTags = $this->bookmarkTagModel->serveTagsRelatedToBookMark($this->bookmarkId,$this->userId);

        //idがnull
        $this->assertSame($bookmarkTags[0]->id,null);
    }

    public function test_updateBookMarkTag_記事についていたタグと新規のタグを入れ替える()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        //更新前
        $tags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);


        foreach($tags as $tag){ $this->bookmarkTagModel->storeBookMarkTag($tag->id,$this->bookmarkId); }

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
        $this->bookmarkTagModel->updateBookMarkTag($this->bookmarkId,[$tag1->id,$tag2->id]);

        // 更新前のデータ(ちゃんと消されたか確認する)
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookmarkId,
            'tag_id'     => $tags[0]->id,
            'deleted_at' => Carbon::now()
        ]);
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookmarkId,
            'tag_id'     => $tags[1]->id,
            'deleted_at' => Carbon::now()
        ]);

        // 更新後のデータ
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookmarkId,
            'tag_id'     => $tag1->id,
            'deleted_at' => null
        ]);
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookmarkId,
            'tag_id'     => $tag2->id,
            'deleted_at' => null
        ]);
    }

    public function test_updateBookMarkTag_タグがついていなかった記事にタグをつける()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());



        $this->bookmarkTagModel->storeBookMarkTag(null,$this->bookmarkId);

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
        $this->bookmarkTagModel->updateBookMarkTag($this->bookmarkId,[$tag1->id,$tag2->id]);

        // 更新前のデータ(ちゃんと消されたか確認する)
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookmarkId,
            'tag_id'     => null,
            'deleted_at' => Carbon::now()
        ]);

        // 更新後のデータ
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookmarkId,
            'tag_id'     => $tag1->id,
            'deleted_at' => null
        ]);
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookmarkId,
            'tag_id'     => $tag2->id,
            'deleted_at' => null
        ]);
    }

    public function test_updateBookMarkTag_記事についていたタグはそのままに新規のタグを追加()
    {
        //更新前
        $tags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);


        foreach($tags as $tag){
            $this->bookmarkTagModel->storeBookMarkTag($tag->id,$this->bookmarkId);
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
        $this->bookmarkTagModel->updateBookMarkTag($this->bookmarkId,[$tag1->id,$tag2->id,$tags[0]->id,$tags[1]->id]);

        //取得したタグが新しく作ったタグになっているか確認
        // もともとつけていた部分
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookmarkId,
            'tag_id'     => $tags[0]->id,
            'deleted_at' => null
        ]);
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookmarkId,
            'tag_id'     => $tags[1]->id,
            'deleted_at' => null
        ]);

        // 追加した部分
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookmarkId,
            'tag_id'     => $tag1->id,
            'deleted_at' => null
        ]);
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookmarkId,
            'tag_id'     => $tag2->id,
            'deleted_at' => null
        ]);
    }

    public function test_updateBookMarkTag_記事についていたタグの一部を外す()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        //更新前
        $tags = Tag::factory()->count(4)->create(['user_id' => $this->userId]);


        foreach($tags as $tag){
            $this->bookmarkTagModel->storeBookMarkTag($tag->id,$this->bookmarkId);
        }

        //----

        //更新
        $this->bookmarkTagModel->updateBookMarkTag($this->bookmarkId,[$tags[0]->id,$tags[1]->id]);

        //残したやつ
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookmarkId,
            'tag_id'     => $tags[0]->id,
            'deleted_at' => null
        ]);
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookmarkId,
            'tag_id'     => $tags[1]->id,
            'deleted_at' => null
        ]);
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookmarkId,
            'tag_id'     => $tags[2]->id,
            'deleted_at' => Carbon::now()
        ]);
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookmarkId,
            'tag_id'     => $tags[3]->id,
            'deleted_at' => Carbon::now()
        ]);
    }

    public function test_getOrignalTag()
    {
        $tags = Tag::factory()->count(4)->create(['user_id' => $this->userId]);

        foreach($tags as $tag){
            $this->bookmarkTagModel->storeBookMarkTag($tag->id,$this->bookmarkId);
        }

        $bookmarkTags = $this->bookmarkTagModel->getOrignalTag($this->bookmarkId);

        // print_r($bookmarkTags);

        // 登録したタグがあるかどうか
        foreach ($bookmarkTags as $bookmarkTag) {
            $this->assertContains($bookmarkTag,$bookmarkTags);
        }
    }

    public function test_procesOriginalBookMarkDoesNotHaveAnyTags_元の記事にタグがついている()
    {
        $tags = Tag::factory()->count(4)->create(['user_id' => $this->userId]);

        $returnValue = $this->bookmarkTagModel->storeBookMarkTag($tags[0]->id,$this->bookmarkId);
        $this->bookmarkTagModel->storeBookMarkTag($tags[1]->id,$this->bookmarkId);

        $this->bookmarkTagModel->procesOriginalBookMarkDoesNotHaveAnyTags(
            originalTagList:$this->bookmarkTagModel->getOrignalTag($this->bookmarkId),
            bookMarkId:$this->bookmarkId,
            updatedTagList:[$tags[0]->id,$tags[1]->id,$tags[2]->id,$tags[3]->id]
        );

        $this->assertNull($returnValue);

        // 登録されているタグに変化がない
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookmarkId,
            'tag_id'     => $tags[0]->id,
            'deleted_at' => null
        ]);
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookmarkId,
            'tag_id'     => $tags[1]->id,
            'deleted_at' => null
        ]);
        // procesOriginalBookMarkDoesNotHaveAnyTagsでは新規タグの登録はしないからここではテストしない

    }

    public function test_procesOriginalBookMarkDoesNotHaveAnyTags_元の記事にタグがついていない_新しくタグを追加()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        $tags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        $this->bookmarkTagModel->storeBookMarkTag(null,$this->bookmarkId);

        $returnValue = $this->bookmarkTagModel->procesOriginalBookMarkDoesNotHaveAnyTags(
            originalTagList:$this->bookmarkTagModel->getOrignalTag($this->bookmarkId),
            bookMarkId:$this->bookmarkId,
            updatedTagList:[$tags[0]->id,$tags[1]->id]
        );

        $this->assertNull($returnValue);

        //nullの部分が論理削除されている
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookmarkId,
            'tag_id'     => null,
            'deleted_at' => Carbon::now()
        ]);

        // procesOriginalBookMarkDoesNotHaveAnyTagsでは新規タグの登録はしないからここではテストしない
    }

    public function test_procesOriginalBookMarkDoesNotHaveAnyTags_元の記事にタグがついていない_タグも追加しない()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        $this->bookmarkTagModel->storeBookMarkTag(null,$this->bookmarkId);

        $returnValue = $this->bookmarkTagModel->procesOriginalBookMarkDoesNotHaveAnyTags(
            originalTagList:$this->bookmarkTagModel->getOrignalTag($this->bookmarkId),
            bookMarkId:$this->bookmarkId,
            updatedTagList:[]
        );

        $this->assertTrue($returnValue);

        //元データに変化なし
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookmarkId,
            'tag_id'     => null,
            'deleted_at' => null
        ]);
        // procesOriginalBookMarkDoesNotHaveAnyTagsでは新規タグの登録はしないからここではテストしない
    }

    public function test_procesOriginalBookMarkDeleteAllTags_追加タグなし_ついてたタグ全部けした()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        $tags = Tag::factory()->count(4)->create(['user_id' => $this->userId]);

        $this->bookmarkTagModel->storeBookMarkTag($tags[0]->id,$this->bookmarkId);
        $this->bookmarkTagModel->storeBookMarkTag($tags[1]->id,$this->bookmarkId);

        $returnValue = $this->bookmarkTagModel->procesOriginalBookMarkDeleteAllTags(
                originalTagList:$this->bookmarkTagModel->getOrignalTag($this->bookmarkId),
                bookMarkId:$this->bookmarkId,
                isAddedTagListEmpty:True,
                deletedTagList:[$tags[0]->id,$tags[1]->id]
            );

        $this->assertNull($returnValue);

        // もとあったデータの論理削除は別の関数でやるのでここではチェックしない

        // tag_id = nullのデータが追加されているか確認
        $this->assertDatabaseHas('book_mark_tags',[
            'book_mark_id' => $this->bookmarkId,
            'tag_id'     => null,
            'deleted_at' => null
        ]);
    }

    public function test_procesOriginalBookMarkDeleteAllTags_追加タグあり_ついてたタグ全部けした()
    {

        $tags = Tag::factory()->count(4)->create(['user_id' => $this->userId]);

        $this->bookmarkTagModel->storeBookMarkTag($tags[0]->id,$this->bookmarkId);
        $this->bookmarkTagModel->storeBookMarkTag($tags[1]->id,$this->bookmarkId);

        $returnValue = $this->bookmarkTagModel->procesOriginalBookMarkDeleteAllTags(
                originalTagList:$this->bookmarkTagModel->getOrignalTag($this->bookmarkId),
                bookMarkId:$this->bookmarkId,
                isAddedTagListEmpty:false,
                deletedTagList:[$tags[0]->id,$tags[1]->id]
            );

        $this->assertNull($returnValue);

        // もとあったデータの論理削除は別の関数でやるのでここではチェックしない
    }

    public function test_procesOriginalBookMarkDeleteAllTags_追加タグなし_タグついてない()
    {
        $this->bookmarkTagModel->storeBookMarkTag(null,$this->bookmarkId);

        $returnValue = $this->bookmarkTagModel->procesOriginalBookMarkDeleteAllTags(
                originalTagList:$this->bookmarkTagModel->getOrignalTag($this->bookmarkId),
                bookMarkId:$this->bookmarkId,
                isAddedTagListEmpty:true,
                deletedTagList:[]
            );

        $this->assertNull($returnValue);
    }

    public function test_procesOriginalBookMarkDeleteAllTags_追加タグあり_タグついてない()
    {
        $this->bookmarkTagModel->storeBookMarkTag(null,$this->bookmarkId);

        $returnValue = $this->bookmarkTagModel->procesOriginalBookMarkDeleteAllTags(
                originalTagList:$this->bookmarkTagModel->getOrignalTag($this->bookmarkId),
                bookMarkId:$this->bookmarkId,
                isAddedTagListEmpty:false,
                deletedTagList:[]
            );

        $this->assertNull($returnValue);
    }
}

