<?php

namespace Database\Seeders\useInPlaywright;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\UserSeeder;
use Database\Seeders\useInPlaywright\ArticleSeeder;
use Database\Seeders\useInPlaywright\TagSeeder;


class PlaywrightSeeder extends Seeder
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

// artisanコマンド作るんじゃなくてbashで良いかも?2つ連続で実行するだけだし
// -> でもディレクトリ実行する必要があるな｡｡｡
// php artisan migrate:fresh
// php artisan db:seed --class=Database\\Seeders\\useInPlaywright\\PlaywrightSeeder
