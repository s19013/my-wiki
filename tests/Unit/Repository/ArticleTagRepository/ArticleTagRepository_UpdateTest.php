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

class ArticleTagRepository_UpdateTest extends TestCase
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
    // 指定した記事についていたタグをすべて外して､新しく指定したタグを記事に紐づける
    // 古いタグは論理削除されている
    public function test_update_記事についていたタグと新規のタグを入れ替える()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        //更新前
        $tags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        // 記事に紐づける
        foreach($tags as $tag){ $this->articleTagRepository->store($tag->id,$this->articleId); }

        //----

        //新しいタグを追加
        $newTags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        //更新
        $this->articleTagRepository->update($this->articleId,[$newTags[0]->id,$newTags[1]->id]);

        // 更新前のデータ(ちゃんと消されたか確認する)
        foreach($tags as $tag){
            $this->assertDatabaseMissing('article_tags',[
                'article_id' => $this->articleId,
                'tag_id'     => $tag->id,
            ]);
        }

        // 更新後のデータ
        foreach($newTags as $tag){
            $this->assertDatabaseHas('article_tags',[
                'article_id' => $this->articleId,
                'tag_id'     => $tag->id,
            ]);
        }
    }

    // 期待
    // 指定したタグが何もついてなかった記事に､新しく指定したタグを記事に紐づける
    // nullは論理削除されている
    public function test_update_タグがついていなかった記事にタグをつける()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        $this->articleTagRepository->store(null,$this->articleId);

        //----

        //新しいタグを追加
        $newTags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        //更新
        $this->articleTagRepository->update($this->articleId,[$newTags[0]->id,$newTags[1]->id]);

        // 更新前のデータ(ちゃんと消されたか確認する)
        $this->assertDatabaseMissing('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => null,
        ]);

        // 更新後のデータ
        foreach($newTags as $tag){
            $this->assertDatabaseHas('article_tags',[
                'article_id' => $this->articleId,
                'tag_id'     => $tag->id,
            ]);
        }
    }

    // 期待
    // 指定した記事についていたタグはそのままに新規のタグを追加
    public function test_update_記事についていたタグはそのままに新規のタグを追加()
    {
        //更新前
        $tags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        foreach($tags as $tag){ $this->articleTagRepository->store($tag->id,$this->articleId); }

        //----

        //新しいタグを追加
        $newTags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        //更新
        $this->articleTagRepository->update($this->articleId,[$newTags[0]->id,$newTags[1]->id,$tags[0]->id,$tags[1]->id]);

        //取得したタグが新しく作ったタグになっているか確認
        // もともとつけていた部分

        foreach ($tags as $tag){
            $this->assertDatabaseHas('article_tags',[
                'article_id' => $this->articleId,
                'tag_id'     => $tag->id,
            ]);
        }

        // 追加した部分
        foreach ($newTags as $tag){
            $this->assertDatabaseHas('article_tags',[
                'article_id' => $this->articleId,
                'tag_id'     => $tag->id,

            ]);
        }
    }

    // 期待
    // 指定した記事についていたタグを一部外して､新しく指定したタグを記事に紐づける
    // 古いタグは論理削除されている
    public function test_update_記事についていたタグの一部を外す()
    {
        // carbonの時間固定
        Carbon::setTestNow(Carbon::now());

        //更新前
        $tags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        foreach($tags as $tag){$this->articleTagRepository->store($tag->id,$this->articleId);}

        //----

        $newTags = Tag::factory()->count(2)->create(['user_id' => $this->userId]);

        //更新
        $this->articleTagRepository->update($this->articleId,[$tags[1]->id,$newTags[0]->id,$newTags[1]->id]);

        // けしたやつ
        $this->assertDatabaseMissing('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => $tags[0]->id,
        ]);
        //残したやつ
        $this->assertDatabaseHas('article_tags',[
            'article_id' => $this->articleId,
            'tag_id'     => $tags[1]->id,

        ]);

        foreach($newTags as $tag){
            $this->assertDatabaseHas('article_tags',[
                'article_id' => $this->articleId,
                'tag_id'     => $tag->id,

            ]);
        }
    }
}
