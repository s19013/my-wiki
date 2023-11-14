<?php

namespace Database\Seeders\useInPlaywight;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
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
        Tag::factory()->create([
            'name' =>"recipe",
            'user_id' => 1,
        ]);

        Tag::factory()->create([
            'name' =>"apple",
            'user_id' => 1,
        ]);

        Tag::factory()->create([
            'name' =>"berry",
            'user_id' => 1,
        ]);

        Tag::factory()->create([
            'name' =>"peach",
            'user_id' => 1,
        ]);

        Tag::factory()->create([
            'name' =>"sweets",
            'user_id' => 1,
        ]);

        Tag::factory()->create([
            'name' =>"drink",
            'user_id' => 1,
        ]);
    }
}
