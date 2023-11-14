<?php

namespace Database\Seeders\useInPlaywright;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\UserSeeder;
use Database\Seeders\useInPlaywright\ArticleSeeder;
use Database\Seeders\useInPlaywright\TagSeeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        (new userSeeder())->run();
        (new TagSeeder())->run();
        (new ArticleSeeder())->run();
    }
}
