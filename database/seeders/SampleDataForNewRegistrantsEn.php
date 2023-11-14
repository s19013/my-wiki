<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Article;
use App\Models\ArticleTag;
use App\Models\BookMark;
use App\Models\BookMarkTag;
use App\Models\Tag;


class SampleDataForNewRegistrantsEn extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run($userId)
    {
        $tag = Tag::create([
            'user_id' => $userId,
            'name'    => "sampleTag"
        ]);

        $article = Article::create([
            'user_id'  => $userId,
            'title'    => "sample",
            'body'     => "Thank you for registering. You can add as many tags as you like.",
        ]);

        ArticleTag::factory()->create([
            "article_id" => $article->id,
            "tag_id"     => (Tag::where('name', "sampleTag")->first())->id
        ]);

        // 本来ならqaへのリンクとか入れときたい
        // $bookMark = BookMark::create([
        //     'user_id'  => $userId,
        //     'title'    => "",
        //     'url'      => "",
        // ]);

        // BookmarkTag::factory()->create([
        //     "book_mark_id" => $bookMark->id,
        //     "tag_id"     => (Tag::where('name', "サンプルタグ")->first())->id
        // ]);
    }
}
