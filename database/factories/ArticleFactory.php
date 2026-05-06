<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Article> */
class ArticleFactory extends Factory
{
    private static int $sequence = 0;

    public function definition(): array
    {
        self::$sequence++;

        return [
            'doi'           => null,
            'doi_status'    => null,
            'pages'         => fake()->numerify('##') . '–' . fake()->numerify('##'),
            'sequence'      => self::$sequence * 10.0,
            'date_published'=> fake()->dateTimeBetween('-18 months', '-1 month'),
            'access_status' => 'open',
            'views'         => fake()->numberBetween(50, 2500),
            'downloads'     => fake()->numberBetween(10, 800),
        ];
    }

    public function withDoi(): static
    {
        return $this->state(fn () => [
            'doi'        => '10.12345/gjs.' . fake()->numerify('####') . '.' . fake()->numerify('####'),
            'doi_status' => 'registered',
        ]);
    }
}
