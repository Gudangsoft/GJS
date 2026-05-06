<?php

namespace Database\Factories;

use App\Models\ArticleGalley;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ArticleGalley> */
class ArticleGalleyFactory extends Factory
{
    private static array $formats = [
        ['label' => 'PDF',  'locale' => 'id'],
        ['label' => 'PDF',  'locale' => 'en'],
        ['label' => 'HTML', 'locale' => 'id'],
        ['label' => 'EPUB', 'locale' => 'id'],
    ];

    private static int $index = 0;

    public function definition(): array
    {
        $format = self::$formats[self::$index % count(self::$formats)];
        self::$index++;

        return [
            'label'             => $format['label'],
            'locale'            => $format['locale'],
            'submission_file_id'=> null,
            'remote_url'        => null,
            'sequence'          => self::$index,
            'is_approved'       => true,
            'views'             => fake()->numberBetween(5, 600),
        ];
    }

    public function pdf(): static
    {
        return $this->state(fn () => ['label' => 'PDF']);
    }
}
