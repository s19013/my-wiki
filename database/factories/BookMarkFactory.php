<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\BookMark;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookMark>
 */
class BookMarkFactory extends Factory
{
    protected $model = BookMark::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title'=>fake()->realText(15),
            'url' =>fake()->realText(30),
        ];
    }
}
