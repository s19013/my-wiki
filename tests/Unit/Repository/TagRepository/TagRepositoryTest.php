<?php

namespace Tests\Unit\Repository\TagRepository;

use Tests\TestCase;

use App\Models\User;
use App\Models\Tag;
use App\Models\Article;
use App\Models\BookMark;
use App\Models\ArticleTag;
use App\Models\BookMarkTag;
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

        // テストで使う時間を固定
        Carbon::setTestNow(Carbon::now());
    }

    // 期待
    // 引数2の文字列がデータベースに保存される
    public function test_store()
    {
        $this->tagRepository->store($this->userId,"testTag");
        $this->assertDatabaseHas('tags',[
            'name'       => 'testTag',
            'user_id'    => $this->userId,
            'deleted_at' => null
        ]);

    }

    // 期待
    // データベースに保存される
    public function test_store_消したタグをもう一度登録()
    {
        $tag = Tag::factory()->create( ["user_id" => $this->userId] );
        $this->tagRepository->delete($tag->id);


        $this->tagRepository->store($this->userId,$tag->name);
        $this->assertDatabaseHas('tags',[
            'name'       => $tag->name,
            'user_id'    => $this->userId,
            'deleted_at' => null
        ]);

    }

    // 期待
    // 削除されていないすべてのデータをとってくる
    public function test_search_削除されていないすべてのタグをとって来る()
    {
        Tag::factory()->count(5)->create( ["user_id" => $this->userId] );

        $deletedTag = Tag::factory()->create( [
            "user_id"    => $this->userId,
            "deleted_at" => Carbon::now()
        ] );

        $receivedTags = $this->tagRepository->search($this->userId,"");

        //名前とidが一緒かどうか
        $IdList = [];
        foreach ($receivedTags as $tag){array_push($IdList,$tag->id);}

        $nameList = [];
        foreach ($receivedTags as $tag){ array_push($nameList,$tag->name);}

        // 数を数える
        $this->assertCount(5, $IdList);

        // 削除したタグが含まれてないか
        $this->assertNotContains($deletedTag->id, $IdList);
    }

    // 期待
    // 他のユーザーが作ったタグをとって来ない
    public function test_search_他のユーザーが作ったタグをとって来ない()
    {
        Tag::factory()->count(5)->create(["user_id" => User::factory()->create()->id]);

        $receivedTags = $this->tagRepository->search($this->userId,"");

        // 数を数える
        $this->assertEmpty($receivedTags->toArray());
    }

    // 期待
    // dbの中から 引数2の文字列をnameカラムに含むタグのデータをとってくる
    public function test_search_キーワード指定()
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


    //
    //
    public function test_search_キーワードなし()
    {
        Tag::factory()->count(5)->create( ["user_id" => $this->userId] );

        $receivedTags = $this->tagRepository->search($this->userId,"");

        // 数が一緒か
        $this->assertSame(5,count($receivedTags->toArray()));
    }

    // 期待
    // nullが帰って来る
    // 条件
    // 編集しようとしているidと取ってきたidがことなる
    public function test_serveTagId_編集しようとしているidと取ってきたidがことなる()
    {
        $tag = Tag::factory()->create( ["user_id" => $this->userId] );

        $receivedTag = $this->tagRepository->serveTagId($this->userId,$tag->name);

        $this->assertNull($receivedTag);
    }

    // 期待
    // nullが帰って来る
    // 条件
    // データ登録してない
    public function test_serveTagId_データ登録してない()
    {
        $tag = Tag::factory()->create( ["user_id" => $this->userId] );

        $receivedId = $this->tagRepository->serveTagId($this->userId,"test");

        $this->assertNull($receivedId);
    }

    // 期待
    // 何かしらの数字が帰ってくる
    // 条件
    // すでに登録してあって､
    public function test_serveTagId_データ登録済み()
    {
        $tag = Tag::factory()->create( ["user_id" => $this->userId] );

        $receivedId = $this->tagRepository->serveTagId($tag->name,$this->userId);

        $this->assertSame($receivedId,$tag->id);
    }

    // 期待
    // 指定したidのタグの名前をとって来れるか
    public function test_findFromId_指定したidのタグの名前をとって来れるか()
    {
        $tag = Tag::factory()->create( ["user_id" => $this->userId] );

        $receivedTag = $this->tagRepository->findFromId($this->userId,$tag->id);

        $this->assertSame($tag->id,$receivedTag->id);
        $this->assertSame($tag->name,$receivedTag->name);
    }

    // 期待
    // 削除したタグはとってこない
    public function test_findFromId_削除したタグはとってこない()
    {
        $tag = Tag::factory()->create( [
            "user_id" => $this->userId,
            "deleted_at" => Carbon::now()
        ] );

        $receivedTag = $this->tagRepository->findFromId($this->userId,$tag->id);

        // 全部nullのハズ
        $this->assertNull($receivedTag->id);
        $this->assertNull($receivedTag->name);
    }

    // 期待
    // 他のユーザーが作ったタグはとってこない
    public function test_findFromId_他のユーザーが作ったタグはとってこない()
    {
        $tag = Tag::factory()->create(["user_id" => User::factory()->create()->id]);

        $receivedTag = $this->tagRepository->search($this->userId,$tag->id);

        // 空の配列が帰ってくるはず
        $this->assertEmpty($receivedTag);
    }



    // 期待
    // データが更新される
    public function test_update()
    {
        $tag = Tag::create([
            'name'    => 'beforeUpdate',
            'user_id' => $this->userId
        ]);

        $this->tagRepository->update($this->userId,$tag->id,'afterUpdate');

        $this->assertDatabaseHas('tags',[
            'name'       => 'afterUpdate',
            'user_id'    => $this->userId,
            'deleted_at' => null
        ]);

    }

    // 期待
    // データが更新されたデータをもう一度更新する
    public function test_update_再度アップデート()
    {
        $tag = Tag::factory()->count(2)->create( [
            "user_id" => $this->userId,
        ]);

        $this->tagRepository->delete($tag[0]->id);

        $this->tagRepository->update($this->userId,$tag[1]->id,$tag[1]->name);

        $this->assertDatabaseHas('tags',[
            'name'       => $tag[1]->name,
            'user_id'    => $this->userId,
            'deleted_at' => null
        ]);

    }

    // 条件
    // タグを消去
    // 別のタグを消去したタグと同じ名前にする
    // 期待
    // エラーが出ない
    // タグ名がアプデされている
    public function test_update_消したタグ名にアップデート()
    {
        $tag = Tag::create([
            'name'    => 'beforeUpdate',
            'user_id' => $this->userId
        ]);

        $this->tagRepository->update($this->userId,$tag->id,'afterUpdate');

        $this->assertDatabaseHas('tags',[
            'name'       => 'afterUpdate',
            'user_id'    => $this->userId,
            'deleted_at' => null
        ]);

        $this->tagRepository->update($this->userId,$tag->id,'onemoreUpdate');

        $this->assertDatabaseHas('tags',[
            'name'       => 'onemoreUpdate',
            'user_id'    => $this->userId,
            'deleted_at' => null
        ]);

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

    // 期待
    // タグの名前,タグのid,紐づけられている数を取得
    public function test_searchInEdit_カウントテスト()
    {
        $tags = Tag::factory()->count(2)->create( ["user_id" => $this->userId] );
        $article = Article::factory()->create( ["user_id" => $this->userId] );
        $bookMark = BookMark::factory()->create( ["user_id" => $this->userId] );

        // 1つの記事に全部のタグを登録して見る
        // 1つのブックマークに全部のタグを登録して見る
        foreach ($tags as $tag) {
            ArticleTag::factory()->create([
                "article_id" => $article->id,
                'tag_id'     => $tag->id
            ]);
            BookMarkTag::factory()->create([
                "book_mark_id" => $bookMark->id,
                'tag_id'     => $tag->id
            ]);
        }

        $receivedTags = ($this->tagRepository->searchInEdit($this->userId,"",1))['data'];

        // すべて2つずつ紐づけさせたので
        // すべてのcountが2になっている
        foreach ($receivedTags as $tag) {
            $this->assertSame(2,$tag->count);
        }
    }

    // 期待
    // タグの名前,タグのid,紐づけられている数を取得
    // 指定したユーザーの情報だけ取ってくる
    public function test_searchInEdit_指定したユーザー(Type $var = null)
    {
        $tags = Tag::factory()->count(2)->create( ["user_id" => $this->userId] );
        $article = Article::factory()->create( ["user_id" => $this->userId] );
        $bookMark = BookMark::factory()->create( ["user_id" => $this->userId] );

        $dammyUser = User::factory()->create();
        $dammyTags = Tag::factory()->count(2)->create( ["user_id" => $dammyUser->id] );
        $dammyArticle = Article::factory()->create( ["user_id" => $dammyUser->id] );
        $dammyBookMark = BookMark::factory()->create( ["user_id" => $dammyUser->id] );

        foreach ($tags as $tag) {
            ArticleTag::factory()->create([
                "article_id" => $article->id,
                'tag_id'     => $tag->id
            ]);
            BookMarkTag::factory()->create([
                "book_mark_id" => $bookMark->id,
                'tag_id'     => $tag->id
            ]);
        }

        foreach ($dammyTags as $tag) {
            ArticleTag::factory()->create([
                "article_id" => $dammyArticle->id,
                'tag_id'     => $tag->id
            ]);
            BookMarkTag::factory()->create([
                "book_mark_id" => $dammyBookMark->id,
                'tag_id'       => $tag->id
            ]);
        }

        $receivedTags = ($this->tagRepository->searchInEdit($this->userId,"",1))['data'];

        // すべて指定したユーザーのidか
        foreach ($receivedTags->toArray() as $tag) {
            $this->assertSame($this->userId,$tag['user_id']);
        }
    }

    // 期待
    // 削除したタグの情報は取ってこない
    public function test_searchInEdit_削除済みテスト()
    {
        $tags = Tag::factory()->count(2)->create( ["user_id" => $this->userId] );
        $article = Article::factory()->create( ["user_id" => $this->userId] );
        $bookMark = BookMark::factory()->create( ["user_id" => $this->userId] );

        // 1つの記事に全部のタグを登録して見る
        // 1つのブックマークに全部のタグを登録して見る
        foreach ($tags as $tag) {
            ArticleTag::factory()->create([
                "article_id" => $article->id,
                'tag_id'     => $tag->id
            ]);
            BookMarkTag::factory()->create([
                "book_mark_id" => $bookMark->id,
                'tag_id'     => $tag->id
            ]);
        }

        // 1つ消す
        Tag::where('id','=',$tags[0]->id)->delete();

        $receivedTags = ($this->tagRepository->searchInEdit($this->userId,"",1))['data'];

        // 使うタグが1つ減ったので､1つのタグしか帰って来ないはず
        $this->assertSame(count($receivedTags->toArray()),1);
    }

    public function test_searchInEdit_キーワード指定()
    {
        // 検索にかかるタグ
        $hitTag = Tag::factory()->create( [
            "name"    => "TestTag",
            "user_id" => $this->userId
        ] );
        $tags = Tag::factory()->count(5)->create( ["user_id" => $this->userId] );
        $article = Article::factory()->create( ["user_id" => $this->userId] );
        $bookMark = BookMark::factory()->create( ["user_id" => $this->userId] );

        // ヒットするタグは1つだけ登録する
        ArticleTag::factory()->create([
            "article_id" => $article->id,
            'tag_id'     => $hitTag->id
        ]);

        // 他のタグを登録
        foreach ($tags as $tag) {
            ArticleTag::factory()->create([
                "article_id" => $article->id,
                'tag_id'     => $tag->id
            ]);
            BookMarkTag::factory()->create([
                "book_mark_id" => $bookMark->id,
                'tag_id'     => $tag->id
            ]);
        }

        $receivedTags = ($this->tagRepository->searchInEdit($this->userId,"TestTag",1))['data'];

        //名前とidが一緒かどうか
        $IdList = [];
        foreach ($receivedTags as $tag){array_push($IdList,$tag->id);}

        $nameList = [];
        foreach ($receivedTags as $tag){ array_push($nameList,$tag->name);}

        // 検索したタグが含まれているか｡
        $this->assertContains("TestTag",$nameList);
    }
}

