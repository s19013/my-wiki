<?php

namespace Tests\Unit\Repository;

use Tests\TestCase;
use App\Models\Article;
use App\Models\ArticleTag;
use App\Models\Tag;
use App\Models\User;

use App\Repository\ArticleRepository;

// データベース関係で使う
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;


use Carbon\Carbon;

class ArticleRepository_SearchTest extends TestCase
{
    // テストしたらリセットする
    use RefreshDatabase;

    private $articleModel;
    private $articleRepository;
    private $userId;

    public function setup():void
    {
        parent::setUp();
        $this->articleRepository = new ArticleRepository();

        // ユーザーを用意
        $user = User::create([
            'name'     => "tester",
            'email'    => "hideya670@gmail.com",
            'password' => "gemini0522",
        ]);

        $this->userId = $user->id;
    }

    // 期待
    // 何ページ分あるか数える
    // 一度に取得する件数は10件を超えない
    // 条件
    // すべてのデータの合計数が10以上
    public function test_Search_すべてのデータの合計数が10以上()
    {
        //
        Carbon::setTestNow(Carbon::now());

        //テストユーザーの記事
        Article::factory()->count(25)->create(['user_id' => $this->userId]);

        $response = $this->articleRepository->search(
            userId:$this->userId,
            keyword:'',
            page:1,
            tagList:null,
            searchTarget:'title'
        );

        //帰ってきたarticleListの数を数える(10個以上あっても一度に10個までしか返さない)
        $this->assertCount(10,$response['data']);

        // 何ページ分あるか確認
        // 今回は全部で20件ある,1ページ10件までなので,10件 + 10件 + 5件の3ページに分かれる
        $this->assertEquals(3, $response['last_page']);
    }

    // 期待
    // 何ページ分あるか数える
    // 一度に取得する件数は10件を超えない
    // 条件
    // すべてのデータの合計数が10以下
    public function test_Search_すべてのデータの合計数が10以下()
    {
        //
        Carbon::setTestNow(Carbon::now());

        //テストユーザーの記事
        Article::factory()->count(5)->create(['user_id' => $this->userId]);

        $response = $this->articleRepository->search(
            userId:$this->userId,
            keyword:'',
            page:1,
            tagList:null,
            searchTarget:'title'
        );

        //帰ってきたarticleListの数を数える
        $this->assertCount(5,$response['data']);

        // 何ページ分あるか確認
        $this->assertEquals(1, $response['last_page']);
    }

    // 期待
    // 11件目から20件目のデータを取得する
    //
    public function test_Search_11件目から20件目のデータを取得する()
    {
        //
        Carbon::setTestNow(Carbon::now());

        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        for ($i=0; $i <=30 ; $i++) {
            Article::create([
                'title'   => "title ${i}",
                'body'    => "body ${i}",
                'user_id' => $this->userId
            ]);
        }

        Article::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        $response = $this->articleRepository->search(
            userId:$this->userId,
            keyword:'',
            page:2,
            tagList:null,
            searchTarget:'title'
        );

        $articleList = $response['data'];

        //帰ってきたarticleListの数を数える(10個以上あっても一度に10個までしか返さない)
        for ($i=10; $i <20 ; $i++) {
            $this->assertSame($articleList[$i - 10]->title,"title ${i}");
        }
    }

    // 期待
    // * 指定したユーザーの記事だけを取ってくる
    // * 削除した記事は取ってこない
    // 条件
    // * タグ指定なし
    // * タイトル検索
    // * キーワードなし
    public function test_Search_タグ指定なし_タイトル検索_キーワードなし()
    {
        //
        Carbon::setTestNow(Carbon::now());

        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーの記事
        $articles = Article::factory()->count(20)->create(['user_id' => $this->userId]);

        //わざと記事を消す
        $this->articleRepository->delete($articles[0]->id);
        $this->articleRepository->delete($articles[1]->id);

        // ダミーの記事
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        $response = $this->articleRepository->search(
            userId:$this->userId,
            keyword:'',
            page:1,
            tagList:null,
            searchTarget:'title'
        );

        $articleList = $response['data'];

        $idList = [];
        foreach ($articleList as $data){ array_push($idList,$data->id); }

        // 全部指定したユーザーの記事か
        foreach ($articleList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除した記事は含んでないか
        $this->assertNotContains($articles[0]['id'],$idList);
        $this->assertNotContains($articles[1]['id'],$idList);
    }

    // 期待
    //  * titelカラムに"apple"と"make"の文字列が入っているデータをとってくる
    //  * 指定したユーザーの記事だけを取ってくる
    //  * 削除した記事は取ってこない
    // 条件
    // * タグ指定なし
    // * タイトル検索
    // * キーワードあり
    public function test_Search_タグ指定なし_タイトル検索_キーワードあり()
    {
        //
        Carbon::setTestNow(Carbon::now());


        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーの記事
        $articles = Article::factory()->count(10)->create(['user_id' => $this->userId]);

        //検索で引っかかるような記事作成
        $hitArticle1 = Article::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make applePie'
        ]);

        $hitArticle2 = Article::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make appleTea'
        ]);

        // わざと消す記事
        $deleteArticle = Article::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make appleJam'
        ]);

        //わざと記事を消す
        $this->articleRepository->delete($deleteArticle->id);

        // ダミーの記事
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        $response = $this->articleRepository->search(
            userId:$this->userId,
            keyword:"apple make",
            page:1,
            tagList:null,
            searchTarget:'title'
        );

        $articleList = $response['data'];

        $idList = [];
        foreach ($articleList as $data){ array_push($idList,$data->id); }


        // 全部指定したユーザーの記事か
        foreach ($articleList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除した記事は含んでないか
        $this->assertNotContains($deleteArticle['id'],$idList);

        // ヒットするはずの記事を取ってきているか
        $this->assertContains($hitArticle1['id'], $idList);
        $this->assertContains($hitArticle2['id'], $idList);

    }

    // 期待
    //  * 指定したタグがついている記事をとってくる
    //  * 指定したユーザーの記事を取ってくる
    //  * 削除した記事は取ってこない
    // 条件
    // * タグ指定:'recipe'
    // * タイトル検索
    // * キーワードなし
    public function test_Search_タグ指定あり_タイトル検索_キーワードなし_()
    {
        //
        Carbon::setTestNow(Carbon::now());

        // 検索でヒットするはずのタグを作る
        $hitTag = Tag::factory()->create([
            'name'    => 'recipe',
            'user_id' => $this->userId
        ]);

        //検索で引っかかるような記事作成
        $hitArticle1 = Article::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make applePie'
        ]);

        ArticleTag::create([
            'article_id' => $hitArticle1->id,
            'tag_id'     => $hitTag->id,
        ]);

        // ダミー
        $hitArticle2 = Article::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make berryPie'
        ]);

        $dammyTag1 = Tag::factory()->create([
            'name'    => 'berry',
            'user_id' => $this->userId
        ]);

        ArticleTag::create([
            'article_id' => $hitArticle2->id,
            'tag_id'     => $dammyTag1->id,
        ]);

        ArticleTag::create([
            'article_id' => $hitArticle2->id,
            'tag_id'     => $hitTag->id,
        ]);

        // わざと消す記事
        $deleteArticle = Article::factory()->create([
            'user_id' => $this->userId,
            'title'   => 'how to make peachPie'
        ]);

        $dammyTag2 = Tag::factory()->create([
            'name'    => 'peach',
            'user_id' => $this->userId
        ]);

        ArticleTag::create([
            'article_id' => $deleteArticle->id,
            'tag_id'     => $dammyTag2->id,
        ]);

        ArticleTag::create([
            'article_id' => $deleteArticle->id,
            'tag_id'     => $hitTag->id,
        ]);

        //わざと記事を消す
        $this->articleRepository->delete($deleteArticle->id);

        $response = $this->articleRepository->search(
            userId:$this->userId,
            keyword:"",
            page:1,
            tagList:[$hitTag->id],
            searchTarget:'title'
        );

        $articleList = $response['data'];

        $idList = [];
        foreach ($articleList as $data){ array_push($idList,$data->id); }

        // 全部指定したユーザーの記事か
        foreach ($articleList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除した記事は含んでないか
        $this->assertNotContains($deleteArticle['id'],$idList);

        // ヒットするはずの記事を取ってきているか
        $this->assertContains($hitArticle1['id'], $idList);
    }

    // 期待
    //  * titelカラムに"make"の文字列が入っているデータをとってくる
    //  * 指定したタグがついている記事をとってくる
    //  * 指定したユーザーの記事だけを取ってくる
    //  * 削除した記事は取ってこない
    // 条件
    // * タグ指定:'recipe'
    // * タイトル検索
    // * キーワードなし
    public function test_Search_タグ指定あり_タイトル検索_キーワードあり()
    {
        //
        Carbon::setTestNow(Carbon::now());


        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーの記事
        $articles = Article::factory()->count(10)->create(['user_id' => $this->userId]);


        //検索で引っかかるような記事作成
        $hitArticle = Article::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make applePie'
        ]);

        $hitTag = Tag::factory()->create([
            'name'    => 'apple',
            'user_id' => $this->userId
        ]);

        // 他のダミーにも付けるタグ
        $tag = Tag::factory()->create([
            'name'    => 'recipe',
            'user_id' => $this->userId
        ]);

        ArticleTag::create([
            'article_id' => $hitArticle->id,
            'tag_id'     => $hitTag->id,
        ]);

        ArticleTag::create([
            'article_id' => $hitArticle->id,
            'tag_id'     => $tag->id,
        ]);

        // ダミー
        $dammyArticle = Article::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make berryPie'
        ]);

        $dammyTag1 = Tag::factory()->create([
            'name'    => 'berry',
            'user_id' => $this->userId
        ]);

        ArticleTag::create([
            'article_id' => $dammyArticle->id,
            'tag_id'     => $dammyTag1->id,
        ]);

        ArticleTag::create([
            'article_id' => $dammyArticle->id,
            'tag_id'     => $tag->id,
        ]);

        // わざと消す記事
        $deleteArticle = Article::factory()->create([
            'user_id' => $this->userId,
            'title' => 'how to make peachPie'
        ]);

        $dammyTag2 = Tag::factory()->create([
            'name'    => 'berry',
            'user_id' => $this->userId
        ]);

        ArticleTag::create([
            'article_id' => $deleteArticle->id,
            'tag_id'     => $dammyTag2->id,
        ]);

        ArticleTag::create([
            'article_id' => $deleteArticle->id,
            'tag_id'     => $tag->id,
        ]);

        //他のダミー記事
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        Tag::factory()->count(2)->create(['user_id' => $anotherUsers[0]->id]);
        Tag::factory()->count(2)->create(['user_id' => $anotherUsers[0]->id]);


        //わざと記事を消す
        $this->articleRepository->delete($deleteArticle->id);

        $response = $this->articleRepository->search(
            userId:$this->userId,
            keyword:"make",
            page:1,
            tagList:[$hitTag->id],
            searchTarget:'title'
        );

        $articleList = $response['data'];

        $idList = [];
        foreach ($articleList as $data){ array_push($idList,$data->id); }

        // 全部指定したユーザーの記事か
        foreach ($articleList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除した記事は含んでないか
        $this->assertNotContains($deleteArticle->id,$idList);

        // ヒットするはずの記事を取ってきているか
        $this->assertContains($hitArticle->id, $idList);
    }

    // 期待
    // * 指定したユーザーの記事だけを取ってくる
    // * 削除した記事は取ってこない
    // 条件
    // * タグ指定なし
    // * 本文検索
    // * キーワードなし
    public function test_Search_タグ指定なし_本文検索_キーワードなし()
    {
        //
        Carbon::setTestNow(Carbon::now());

        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーの記事
        $articles = Article::factory()->count(20)->create(['user_id' => $this->userId]);

        //わざと記事を消す
        $this->articleRepository->delete($articles[0]->id);
        $this->articleRepository->delete($articles[1]->id);

        // ダミーの記事
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        $response = $this->articleRepository->search(
            userId:$this->userId,
            keyword:'',
            page:1,
            tagList:null,
            searchTarget:'body'
        );

        $articleList = $response['data'];

        $idList = [];
        foreach ($articleList as $data){ array_push($idList,$data->id); }

        // 全部指定したユーザーの記事か
        foreach ($articleList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除した記事は含んでないか
        $this->assertNotContains($articles[0]['id'],$idList);
        $this->assertNotContains($articles[1]['id'],$idList);
    }

    // 期待
    //  * titelカラムに"apple"と"make"の文字列が入っているデータをとってくる
    //  * 指定したユーザーの記事だけを取ってくる
    //  * 削除した記事は取ってこない
    // 条件
    // * タグ指定なし
    // * 本文検索
    // * キーワードあり
    public function test_Search_タグ指定なし_本文検索_キーワードあり()
    {
        //
        Carbon::setTestNow(Carbon::now());


        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーの記事
        $articles = Article::factory()->count(10)->create(['user_id' => $this->userId]);

        //検索で引っかかるような記事作成
        $hitArticle1 = Article::factory()->create([
            'user_id' => $this->userId,
            'body' => 'how to make applePie'
        ]);

        $hitArticle2 = Article::factory()->create([
            'user_id' => $this->userId,
            'body' => 'how to make appleTea'
        ]);

        // わざと消す記事
        $deleteArticle = Article::factory()->create([
            'user_id' => $this->userId,
            'body' => 'how to make appleJam'
        ]);

        //わざと記事を消す
        $this->articleRepository->delete($deleteArticle->id);

        // ダミーの記事
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        $response = $this->articleRepository->search(
            userId:$this->userId,
            keyword:"apple make",
            page:1,
            tagList:null,
            searchTarget:'body'
        );

        $articleList = $response['data'];

        $idList = [];
        foreach ($articleList as $data){ array_push($idList,$data->id); }


        // 全部指定したユーザーの記事か
        foreach ($articleList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除した記事は含んでないか
        $this->assertNotContains($deleteArticle['id'],$idList);

        // ヒットするはずの記事を取ってきているか
        $this->assertContains($hitArticle1['id'], $idList);
        $this->assertContains($hitArticle2['id'], $idList);

    }

    // 期待
    //  * 指定したタグがついている記事をとってくる
    //  * 指定したユーザーの記事を取ってくる
    //  * 削除した記事は取ってこない
    // 条件
    // * タグ指定:'recipe'
    // * 本文検索
    // * キーワードなし
    public function test_Search_タグ指定あり_本文検索_キーワードなし_()
    {
        //
        Carbon::setTestNow(Carbon::now());

        // 検索でヒットするはずのタグを作る
        $hitTag = Tag::factory()->create([
            'name'    => 'recipe',
            'user_id' => $this->userId
        ]);

        //検索で引っかかるような記事作成
        $hitArticle1 = Article::factory()->create([
            'user_id' => $this->userId,
            'body' => 'how to make applePie'
        ]);

        ArticleTag::create([
            'article_id' => $hitArticle1->id,
            'tag_id'     => $hitTag->id,
        ]);

        // ダミー
        $hitArticle2 = Article::factory()->create([
            'user_id' => $this->userId,
            'body' => 'how to make berryPie'
        ]);

        $dammyTag1 = Tag::factory()->create([
            'name'    => 'berry',
            'user_id' => $this->userId
        ]);

        ArticleTag::create([
            'article_id' => $hitArticle2->id,
            'tag_id'     => $dammyTag1->id,
        ]);

        ArticleTag::create([
            'article_id' => $hitArticle2->id,
            'tag_id'     => $hitTag->id,
        ]);

        // わざと消す記事
        $deleteArticle = Article::factory()->create([
            'user_id' => $this->userId,
            'body'   => 'how to make peachPie'
        ]);

        $dammyTag2 = Tag::factory()->create([
            'name'    => 'peach',
            'user_id' => $this->userId
        ]);

        ArticleTag::create([
            'article_id' => $deleteArticle->id,
            'tag_id'     => $dammyTag2->id,
        ]);

        ArticleTag::create([
            'article_id' => $deleteArticle->id,
            'tag_id'     => $hitTag->id,
        ]);

        //わざと記事を消す
        $this->articleRepository->delete($deleteArticle->id);

        $response = $this->articleRepository->search(
            userId:$this->userId,
            keyword:"",
            page:1,
            tagList:[$hitTag->id],
            searchTarget:'body'
        );

        $articleList = $response['data'];

        $idList = [];
        foreach ($articleList as $data){ array_push($idList,$data->id); }

        // 全部指定したユーザーの記事か
        foreach ($articleList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除した記事は含んでないか
        $this->assertNotContains($deleteArticle->id,$idList);

        // ヒットするはずの記事を取ってきているか
        $this->assertContains($hitArticle1->id, $idList);
    }

    // 期待
    //  * titelカラムに"make"の文字列が入っているデータをとってくる
    //  * 指定したタグがついている記事をとってくる
    //  * 指定したユーザーの記事だけを取ってくる
    //  * 削除した記事は取ってこない
    // 条件
    // * タグ指定:'recipe'
    // * 本文検索
    // * キーワードなし
    public function test_Search_タグ指定あり_本文検索_キーワードあり()
    {
        //
        Carbon::setTestNow(Carbon::now());


        //ダミーユーザー追加
        $anotherUsers = User::factory()->count(2)->create();

        //テストユーザーの記事
        $articles = Article::factory()->count(10)->create(['user_id' => $this->userId]);


        //検索で引っかかるような記事作成
        $hitArticle = Article::factory()->create([
            'user_id' => $this->userId,
            'body' => 'how to make applePie'
        ]);

        $hitTag = Tag::factory()->create([
            'name'    => 'apple',
            'user_id' => $this->userId
        ]);

        // 他のダミーにも付けるタグ
        $tag = Tag::factory()->create([
            'name'    => 'recipe',
            'user_id' => $this->userId
        ]);

        ArticleTag::create([
            'article_id' => $hitArticle->id,
            'tag_id'     => $hitTag->id,
        ]);

        ArticleTag::create([
            'article_id' => $hitArticle->id,
            'tag_id'     => $tag->id,
        ]);

        // ダミー
        $dammyArticle = Article::factory()->create([
            'user_id' => $this->userId,
            'body' => 'how to make berryPie'
        ]);

        $dammyTag1 = Tag::factory()->create([
            'name'    => 'berry',
            'user_id' => $this->userId
        ]);

        ArticleTag::create([
            'article_id' => $dammyArticle->id,
            'tag_id'     => $dammyTag1->id,
        ]);

        ArticleTag::create([
            'article_id' => $dammyArticle->id,
            'tag_id'     => $tag->id,
        ]);

        // わざと消す記事
        $deleteArticle = Article::factory()->create([
            'user_id' => $this->userId,
            'body' => 'how to make peachPie'
        ]);

        $dammyTag2 = Tag::factory()->create([
            'name'    => 'berry',
            'user_id' => $this->userId
        ]);

        ArticleTag::create([
            'article_id' => $deleteArticle->id,
            'tag_id'     => $dammyTag2->id,
        ]);

        ArticleTag::create([
            'article_id' => $deleteArticle->id,
            'tag_id'     => $tag->id,
        ]);

        //他のダミー記事
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[0]->id]);
        Article::factory()->count(5)->create(['user_id' => $anotherUsers[1]->id]);

        Tag::factory()->count(2)->create(['user_id' => $anotherUsers[0]->id]);
        Tag::factory()->count(2)->create(['user_id' => $anotherUsers[0]->id]);


        //わざと記事を消す
        $this->articleRepository->delete($deleteArticle->id);

        $response = $this->articleRepository->search(
            userId:$this->userId,
            keyword:"make",
            page:1,
            tagList:[$hitTag->id],
            searchTarget:'body'
        );

        $articleList = $response['data'];

        $idList = [];
        foreach ($articleList as $data){ array_push($idList,$data->id); }

        // 全部指定したユーザーの記事か
        foreach ($articleList as $data){ $this->assertEquals($data->user_id,$this->userId); }

        // 削除した記事は含んでないか
        $this->assertNotContains($deleteArticle->id,$idList);

        // ヒットするはずの記事を取ってきているか
        $this->assertContains($hitArticle->id , $idList);
    }
}
