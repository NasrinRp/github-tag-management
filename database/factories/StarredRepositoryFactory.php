<?php

namespace Database\Factories;

use App\Models\StarredRepository;
use Illuminate\Database\Eloquent\Factories\Factory;

class StarredRepositoryFactory extends Factory
{
    protected $model = StarredRepository::class;

    public function definition()
    {
        return [
            'username' => $this->faker->userName,
            'repository_id' => $this->faker->unique()->numberBetween(1, 10000),
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'url' => $this->faker->url,
            'language' => $this->faker->word,
        ];
    }
}
