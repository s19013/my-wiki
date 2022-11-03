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


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $mainUser  = User::factory()->create([
            'email'    => 'hideya670@gmail.com',
            'password' => Hash::make("gemini0522"),
        ]);
        $otherUser = User::factory()->create();
        // $mainTags      = Tag::factory()->create();

        $mainArticles = Article::factory()->count(10)->create(['user_id' => $mainUser->id]);
        $otherArticles = Article::factory()->count(10)->create(['user_id' => $otherUser->id]);

        foreach ($mainArticles as $article){
            ArticleTag::factory()->create([
                "article_id" => $article->id,
                Tag::factory()->create()->id
            ]);
        }

        foreach ($otherArticles as $article){
            ArticleTag::factory()->create([
                "article_id" => $article->id,
                Tag::factory()->create()->id
            ]);
        }

        $mainBookMarks = BookMark::factory()->count(10)->create(['user_id' => $mainUser->id]);
        $otherBookMarks = BookMark::factory()->count(10)->create(['user_id' => $otherUser->id]);

        foreach ($mainBookMarks as $bookMark){
            BookMarkTag::factory()->create([
                "book_mark_id" => $bookMark->id,
                Tag::factory()->create()->id
            ]);
        }

        foreach ($otherBookMarks as $bookMark){
            BookMarkTag::factory()->create([
                "book_mark_id" => $bookMark->id,
                Tag::factory()->create()->id
            ]);
        }

    }
}
