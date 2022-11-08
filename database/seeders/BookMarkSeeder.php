<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\BookMark;
use App\Models\BookMarkTag;
use App\Models\Tag;


class BookMarkSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $mainArticles = BookMark::factory()->count(10)->create(['user_id' => 13]);
        foreach ($mainArticles as $article){
            BookMarkTag::factory()->create([
                    "book_mark_id" => $article->id,
                    "tag_id"     => Tag::factory()->create(['user_id' => 13])->id
            ]);
        }
    }
}
