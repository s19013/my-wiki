<?php

namespace Database\Seeders\useInPlaywight;

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
        $applePie = Article::factory()->create([
            'title' => "how to make applePie",
            'user_id' => 1,
        ]);

        // array(1, 2, 3, 4)
        foreach (["apple","recipe","sweets"] as $tag){
            ArticleTag::factory()->create([
                "article_id" => $applePie->id,
                "tag_id"     => (Tag::where('name', $tag)->first())->id
            ]);
        }

        $appleTea = Article::factory()->create([
            'title' => "how to make appleTea",
            'user_id' => 1,
        ]);

        foreach (["apple","recipe","drink"] as $tag){
            ArticleTag::factory()->create([
                "article_id" => $appleTea->id,
                "tag_id"     => (Tag::where('name', $tag)->first())->id
            ]);
        }

        $berryPie = Article::factory()->create([
            'title' => "how to make berryPie",
            'user_id' => 1,
        ]);

        foreach (["berry","recipe","sweets"] as $tag){
            ArticleTag::factory()->create([
                "article_id" => $berryPie->id,
                "tag_id"     => (Tag::where('name', $tag)->first())->id
            ]);
        }

        $peachPie = Article::factory()->create([
            'title' => "how to make berryPie",
            'user_id' => 1,
        ]);

        foreach (["peach","recipe","sweets"] as $tag){
            ArticleTag::factory()->create([
                "article_id" => $peachPie->id,
                "tag_id"     => (Tag::where('name', $tag)->first())->id
            ]);
        }

        $articles = Article::factory()->count(10)->create(['user_id' => 1]);
        foreach ($articles as $article){
            ArticleTag::factory()->create([
                    "article_id" => $article->id,
                    "tag_id"     => Tag::factory()->create(['user_id' => 1])->id
            ]);
        }
    }
}
