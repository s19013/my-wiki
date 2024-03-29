<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            "name" => 'testuser',
            "email" => "testuser@abc.com",
            'password' => Hash::make("testuser")
        ]);

        User::factory()->create([
            "name" => 'dammyuser',
            "email" => "dammy@abc.com",
            'password' => Hash::make("dammyuser")
        ]);
    }
}
