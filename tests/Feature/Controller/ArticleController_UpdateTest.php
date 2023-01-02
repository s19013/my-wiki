<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Http\Request;
use App\Http\Controllers\ArticleController;

use App\Models\Article;
use App\Models\ArticleTag;
use App\Models\Tag;
use App\Models\User;

// データベース関係で使う
use Illuminate\Foundation\Testing\WithoutMiddleware;

use Carbon\Carbon;
use Auth;
use Session;
/**
 * 今は$request->session()->regenerateToken();で騒ぎ立てられるのをどうにかできないので
 * $request->session()->regenerateToken();はすべてコメントアウトしてテストする
 */
class ArticleController_UpdateTest extends TestCase
{
    // テストしたらリセットする
    use RefreshDatabase;
    // ミドルウェアの無効化
    // use WithoutMiddleware;

    private $user;

    public function setup():void
    {
        parent::setUp();
        // ユーザーを用意
        $this->user = User::factory()->create();
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());
    }

    // 期待
    // * タイトル､bodyが更新されている
    // * もとの記事についていたタグのidがすべて論理削除されている
    // * 新しく紐づけたタグのidがarticle_tagsテーブルに保存される
    // 条件
    // * もとの記事についていたタグをすべて外す
    // * 記事に別のタグを紐づける
    public function test_articleUpdate_タグ総入れ替え()
    {
        // 記事などを作成
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);
        $newTags = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);

        foreach ($tags as $tag){
            ArticleTag::create([
                "article_id" => $article->id,
                "tag_id"     => $tag->id
            ]);
        }


        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/article/update/',[
            'articleId'     => $article->id,
            'articleTitle'  => "更新titleタグ総入れ替え",
            'articleBody'    => "更新bodyタグ総入れ替え" ,
            'tagList' => [$newTags[0]->id,$newTags[1]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // 記事
        $this->assertDatabaseHas('articles',[
            'user_id'=> $this->user->id,
            'title'  => "更新titleタグ総入れ替え",
            'body'   => "更新bodyタグ総入れ替え",
            'deleted_at' => null,
        ]);

        //タグ
        //新しく追加
        foreach ($newTags as $newTag){
            $this->assertDatabaseHas('article_tags',[
                'article_id' => $article->id,
                'tag_id'     => $newTag->id,
            ]);
        }

        //削除したタグ
        foreach ($tags as $tag){
            $this->assertDatabaseMissing('article_tags',[
                'article_id' => $article->id,
                'tag_id'     => $tag->id,
            ]);
        }
    }

    // 期待
    // * タイトル､bodyが更新されている
    // * もとの記事についていたタグのidになにも変化がない
    // * 新しく紐づけたタグのidがarticle_tagsテーブルに保存される
    // 条件
    // * 記事に別のタグを追加で紐づける
    public function test_articleUpdate_元のタグをそのままに新しく追加()
    {
        // 記事などを作成
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);
        $newTags = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);

        foreach ($tags as $tag){
            ArticleTag::create([
                "article_id" => $article->id,
                "tag_id"     => $tag->id
            ]);
        }

        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/article/update/',[
            'articleId'     => $article->id,
            'articleTitle'  => "更新title元のタグをそのままに新しく追加",
            'articleBody'   => "更新body元のタグをそのままに新しく追加" ,
            'tagList' => [$tags[0]->id,$tags[1]->id,$newTags[0]->id,$newTags[1]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // 記事
        $this->assertDatabaseHas('articles',[
            'user_id'=> $this->user->id,
            'title'  => "更新title元のタグをそのままに新しく追加",
            'body'   => "更新body元のタグをそのままに新しく追加",
            'deleted_at' => null,
        ]);

        //タグ
        // 新しくつけたタグ
        foreach ($newTags as $newTag){
            $this->assertDatabaseHas('article_tags',[
                'article_id' => $article->id,
                'tag_id'     => $newTag->id,
            ]);
        }

        // もともとつけていたタグ
        foreach ($tags as $tag){
            $this->assertDatabaseHas('article_tags',[
                'article_id' => $article->id,
                'tag_id'     => $tag->id,
            ]);
        }
    }

    // 期待
    // * タイトル､bodyが更新されている
    // * もとの記事についていたタグのidになにも変化がない
    // * 新しく紐づけたタグのidがarticle_tagsテーブルに保存される
    // 条件
    // * つけているタグの一部を消す
    public function test_articleUpdate_タグの一部を消す()
    {
        // carbonの時間固定


        // 記事などを作成
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(4)->create(['user_id' => $this->user->id]);

        foreach ($tags as $tag){
            ArticleTag::create([
                "article_id" => $article->id,
                "tag_id"     => $tag->id
            ]);
        }

        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/article/update/',[
            'articleId'     => $article->id,
            'articleTitle'  => "更新titleタグ総入れ替え",
            'articleBody'   => "更新bodyタグ総入れ替え" ,
            'tagList' => [$tags[2]->id,$tags[3]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // 記事
        $this->assertDatabaseHas('articles',[
            'user_id'=> $this->user->id,
            'title'  => "更新titleタグ総入れ替え",
            'body'   => "更新bodyタグ総入れ替え",
            'deleted_at' => null,
        ]);

        //タグ
        //残したタグ
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $tags[2]->id,
        ]);

        $this->assertDatabaseHas('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $tags[3]->id,
        ]);

        //削除したタグ
        $this->assertDatabaseMissing('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $tags[0]->id,
        ]);

        $this->assertDatabaseMissing('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $tags[1]->id,
        ]);
    }

    // 期待
    // * タイトル､bodyが更新されている
    // * もとの記事のtag_id = null のデータを論理削除
    // * 新しく紐づけたタグのidがarticle_tagsテーブルに保存される
    // 条件
    // * タグがついてなかった記事にタグを付ける
    public function test_articleUpdate_タグがついてなかった記事にタグを付ける()
    {
        // carbonの時間固定


        // 記事などを作成
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);

        ArticleTag::create([
            "article_id" => $article->id,
            "tag_id"     => null
        ]);

        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/article/update/',[
            'articleId'     => $article->id,
            'articleTitle'  => "更新titleタグがついてなかった記事にタグを付ける",
            'articleBody'   => "更新bodyタグがついてなかった記事にタグを付ける" ,
            'tagList' => [$tags[0]->id,$tags[1]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // 記事
        $this->assertDatabaseHas('articles',[
            'user_id'=> $this->user->id,
            'title'  => "更新titleタグがついてなかった記事にタグを付ける",
            'body'   => "更新bodyタグがついてなかった記事にタグを付ける",
            'deleted_at' => null,
        ]);

        //タグ
        //追加したタグ
        foreach ($tags as $tag){
            $this->assertDatabaseHas('article_tags',[
                'article_id' => $article->id,
                'tag_id'     => $tag->id,
            ]);
        }

        //削除したタグ
        $this->assertDatabaseMissing('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => null,
        ]);
    }

    // 期待
    // * タイトル､bodyが更新されている
    // * 新しくtag_id = null のデータを保存
    // * もともとついていたタグのdeleted_atに日付を入れる
    // 条件
    // * タグがついていた記事のタグをすべて消す
    public function test_articleUpdate_タグがついていた記事のタグをすべて消す()
    {
        // carbonの時間固定


        // 記事などを作成
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);

        foreach ($tags as $tag){
            ArticleTag::create([
                "article_id" => $article->id,
                "tag_id"       => $tag->id
            ]);
        }

        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/article/update/',[
            'articleId'    => $article->id,
            'articleTitle' => "タグがついていた記事のタグをすべて消す",
            'articleBody'   => "タグがついていた記事のタグをすべて消す" ,
            'tagList' => [],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // 記事
        $this->assertDatabaseHas('articles',[
            'user_id'=> $this->user->id,
            'title'  => "タグがついていた記事のタグをすべて消す",
            'body'    => "タグがついていた記事のタグをすべて消す",
        ]);

        //タグ
        //追加したタグ
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => null,
        ]);

        //削除したタグ
        foreach ($tags as $tag){
            $this->assertDatabaseMissing('article_tags',[
                'article_id' => $article->id,
                'tag_id'     => $tag->id,
            ]);
        }
    }


    // 期待
    // * タイトル､bodyが更新されている
    // * tag_idがnullのまま
    // 条件
    // * タグがついてなかった記事にをタグなしで更新､再度タグなしで更新
    public function test_articleUpdate_タグなし登録_タグなし更新_タグなし更新()
    {
        // carbonの時間固定

        // 記事などを作成
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        ArticleTag::create([
            "article_id" => $article->id,
            "tag_id"     => null
        ]);

        // 更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/article/update/',[
            'articleId'     => $article->id,
            'articleTitle'  => "更新",
            'articleBody'    => "更新" ,
            'tagList' => [],
        ]);

        // ステータス
        $response->assertStatus(200);

        // 再度更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/article/update/',[
            'articleId'     => $article->id,
            'articleTitle'  => "再度更新",
            'articleBody'    => "再度更新" ,
            'tagList' => [],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // 記事
        $this->assertDatabaseHas('articles',[
            'user_id'=> $this->user->id,
            'title'  => "再度更新",
            'body'    => "再度更新",
        ]);

        //タグ
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => null,
        ]);
    }

    // 期待
    // * タイトル､bodyが更新されている
    // * articleTagが更新されている
    // 条件
    // * タグがついてなかった記事にをタグなしで更新､タグつけてで更新
    public function test_articleUpdate_タグなし登録_タグなし更新_タグあり更新()
    {
        // carbonの時間固定

        // 記事などを作成
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);

        ArticleTag::create([
            "article_id" => $article->id,
            "tag_id"     => null
        ]);

        // 更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/article/update/',[
            'articleId'     => $article->id,
            'articleTitle'  => "更新",
            'articleBody'    => "更新" ,
            'tagList' => [],
        ]);

        // ステータス
        $response->assertStatus(200);

        // 再度更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/article/update/',[
            'articleId'     => $article->id,
            'articleTitle'  => "再度更新",
            'articleBody'    => "再度更新" ,
            'tagList' => [$tags[0]->id,$tags[1]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // 記事
        $this->assertDatabaseHas('articles',[
            'user_id'=> $this->user->id,
            'title'  => "再度更新",
            'body'    => "再度更新",
            'deleted_at' => null,
        ]);

        //タグ
        //追加したタグ
        foreach ($tags as $tag){
            $this->assertDatabaseHas('article_tags',[
                'article_id' => $article->id,
                'tag_id'     => $tag->id,
            ]);
        }

        //削除したタグ
        $this->assertDatabaseMissing('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => null,
        ]);
    }

    // 期待
    // * タイトル､bodyが更新されている
    // * articleTagが更新されている
    // 条件
    // * タグがついてなかった記事にをタグありで更新､タグなしで更新
    public function test_articleUpdate_タグなし登録_タグあり更新_タグなし更新()
    {
        // carbonの時間固定

        // 記事などを作成
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);

        ArticleTag::create([
            "article_id" => $article->id,
            "tag_id"     => null
        ]);

        // 更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/article/update/',[
            'articleId'     => $article->id,
            'articleTitle'  => "更新",
            'articleBody'    => "更新" ,
            'tagList' => [$tags[0]->id,$tags[1]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // 再度更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/article/update/',[
            'articleId'     => $article->id,
            'articleTitle'  => "再度更新",
            'articleBody'    => "再度更新" ,
            'tagList' => [],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // 記事
        $this->assertDatabaseHas('articles',[
            'user_id'=> $this->user->id,
            'title'  => "再度更新",
            'body'    => "再度更新",
            'deleted_at' => null,
        ]);

        //タグ
        //追加したタグ
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => null,
        ]);

        //削除したタグ
        foreach ($tags as $tag){
            $this->assertDatabaseMissing('article_tags',[
                'article_id' => $article->id,
                'tag_id'     => $tag->id,
            ]);
        }
    }

    // 期待
    // * タイトル､bodyが更新されている
    // * articleTagが更新されている
    // 条件
    // * タグがついてなかった記事にをタグありで更新､タグありで更新
    public function test_articleUpdate_タグなし登録_タグあり更新_タグあり更新()
    {
        // carbonの時間固定

        // 記事などを作成
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(4)->create(['user_id' => $this->user->id]);

        ArticleTag::create([
            "article_id" => $article->id,
            "tag_id"     => null
        ]);

        // 更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/article/update/',[
            'articleId'     => $article->id,
            'articleTitle'  => "更新",
            'articleBody'    => "更新" ,
            'tagList' => [$tags[0]->id,$tags[1]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // 再度更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/article/update/',[
            'articleId'     => $article->id,
            'articleTitle'  => "再度更新",
            'articleBody'    => "再度更新" ,
            'tagList' => [$tags[1]->id,$tags[2]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // 記事
        $this->assertDatabaseHas('articles',[
            'user_id'=> $this->user->id,
            'title'  => "再度更新",
            'body'    => "再度更新",
            'deleted_at' => null,
        ]);

        //タグ
        //追加したタグ
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $tags[1]->id,
        ]);

        $this->assertDatabaseHas('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $tags[2]->id,
        ]);

        //削除したタグ
        $this->assertDatabaseMissing('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $tags[0]->id,
        ]);

        $this->assertDatabaseMissing('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => null,
        ]);
    }

    // 期待
    // * タイトル､bodyが更新されている
    // * articleTagが更新されている
    // 条件
    // * タグがついてる記事にをタグなしで更新､再度タグなしで更新
    public function test_articleUpdate_タグあり登録_タグなし更新_タグなし更新()
    {
        // carbonの時間固定

        // 記事などを作成
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(4)->create(['user_id' => $this->user->id]);

        ArticleTag::create([
            "article_id" => $article->id,
            "tag_id"     => $tags[0]->id
        ]);

        // 更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/article/update/',[
            'articleId'     => $article->id,
            'articleTitle'  => "更新",
            'articleBody'    => "更新" ,
            'tagList' => [],
        ]);

        // ステータス
        $response->assertStatus(200);

        // 再度更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/article/update/',[
            'articleId'     => $article->id,
            'articleTitle'  => "再度更新",
            'articleBody'    => "再度更新" ,
            'tagList' => [],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // 記事
        $this->assertDatabaseHas('articles',[
            'user_id'=> $this->user->id,
            'title'  => "再度更新",
            'body'    => "再度更新",
            'deleted_at' => null,
        ]);

        //タグ
        //追加
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => null,
        ]);

        // 削除
        $this->assertDatabaseMissing('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $tags[0]->id,
        ]);
    }

    // 期待
    // * タイトル､bodyが更新されている
    // * articleTagが更新されている
    // 条件
    // * タグがついてた記事にをタグなしで更新､タグつけてで更新
    public function test_articleUpdate_タグあり登録_タグなし更新_タグあり更新()
    {
        // carbonの時間固定

        // 記事などを作成
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(3)->create(['user_id' => $this->user->id]);

        ArticleTag::create([
            "article_id" => $article->id,
            "tag_id"       => $tags[0]->id,
        ]);

        // 更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/article/update/',[
            'articleId'     => $article->id,
            'articleTitle'  => "更新",
            'articleBody'    => "更新" ,
            'tagList' => [],
        ]);

        // ステータス
        $response->assertStatus(200);

        // 再度更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/article/update/',[
            'articleId'     => $article->id,
            'articleTitle'  => "再度更新",
            'articleBody'    => "再度更新" ,
            'tagList' => [$tags[1]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // 記事
        $this->assertDatabaseHas('articles',[
            'user_id'=> $this->user->id,
            'title'  => "再度更新",
            'body'    => "再度更新",
            'deleted_at' => null,
        ]);

        //タグ
        //追加したタグ
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $tags[1]->id,
        ]);

        //削除したタグ
        $this->assertDatabaseMissing('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => null,
        ]);

        $this->assertDatabaseMissing('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $tags[0]->id,
        ]);
    }

    // 期待
    // * タイトル､bodyが更新されている
    // * articleTagが更新されている
    // 条件
    // * タグがついてた記事にをタグありで更新､タグなしで更新
    public function test_articleUpdate_タグあり登録_タグあり更新_タグなし更新()
    {
        // carbonの時間固定

        // 記事などを作成
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(3)->create(['user_id' => $this->user->id]);

        ArticleTag::create([
            "article_id" => $article->id,
            "tag_id"       => $tags[0]->id
        ]);

        // 更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/article/update/',[
            'articleId'     => $article->id,
            'articleTitle'  => "更新",
            'articleBody'    => "更新" ,
            'tagList' => [$tags[1]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // 再度更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/article/update/',[
            'articleId'     => $article->id,
            'articleTitle'  => "再度更新",
            'articleBody'    => "再度更新" ,
            'tagList' => [],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // 記事
        $this->assertDatabaseHas('articles',[
            'user_id'=> $this->user->id,
            'title'  => "再度更新",
            'body'    => "再度更新",
            'deleted_at' => null,
        ]);

        //タグ
        //追加したタグ
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => null,
        ]);

        //削除したタグ
        $this->assertDatabaseMissing('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $tags[0]->id,
        ]);

        $this->assertDatabaseMissing('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $tags[1]->id,
        ]);
    }

    // 期待
    // * タイトル､bodyが更新されている
    // * articleTagが更新されている
    // 条件
    // * タグがついてた記事にをタグありで更新､タグなしで更新
    public function test_articleUpdate_タグあり登録_タグあり更新_タグあり更新()
    {
        // carbonの時間固定

        // 記事などを作成
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        // タグ
        $tags    = Tag::factory()->count(4)->create(['user_id' => $this->user->id]);

        ArticleTag::create([
            "article_id" => $article->id,
            "tag_id"     => $tags[0]->id
        ]);

        // 更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/article/update/',[
            'articleId'     => $article->id,
            'articleTitle'  => "更新",
            'articleBody'    => "更新" ,
            'tagList' => [$tags[1]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // 再度更新
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->put('/api/article/update/',[
            'articleId'     => $article->id,
            'articleTitle'  => "再度更新",
            'articleBody'    => "再度更新" ,
            'tagList' => [$tags[2]->id],
        ]);

        // ステータス
        $response->assertStatus(200);

        // データベース
        // 記事
        $this->assertDatabaseHas('articles',[
            'user_id'=> $this->user->id,
            'title'  => "再度更新",
            'body'    => "再度更新",
            'deleted_at' => null,
        ]);

        //タグ
        //追加したタグ

        $this->assertDatabaseHas('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $tags[2]->id,
        ]);

        //削除したタグ
        $this->assertDatabaseMissing('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $tags[0]->id,
        ]);

        $this->assertDatabaseMissing('article_tags',[
            'article_id' => $article->id,
            'tag_id'     => $tags[1]->id,
        ]);
    }
}
