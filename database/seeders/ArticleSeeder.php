<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Article;
use App\Models\ArticleTag;
use App\Models\Tag;


class ArticleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $mainArticles = Article::factory()->count(10)->create(['user_id' => 13]);
        foreach ($mainArticles as $article){
            ArticleTag::factory()->create([
                    "article_id" => $article->id,
                    "tag_id"     => Tag::factory()->create(['user_id' => 13])->id
            ]);
        }
    }
}
