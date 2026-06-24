<?php

namespace Database\Factories;

use App\Models\Issue;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Issue> */
class IssueFactory extends Factory
{
    public function definition(): array
    {
        return [
            'volume'             => 1,
            'number'             => 1,
            'year'               => now()->year,
            'title'              => null,
            'description'        => null,
            'cover_image'        => null,
            'cover_image_alt_text'=> null,
            'published'          => false,
            'current'            => false,
            'show_volume'        => true,
            'show_number'        => true,
            'show_year'          => true,
            'show_title'         => false,
            'access_status'      => 'open',
            'doi'                => null,
            'date_published'     => null,
            'date_notified'      => null,
        ];
    }

    public function published(): static
    {
        return $this->state(function () {
            $date = fake()->dateTimeBetween('-2 years', '-1 month');
            return [
                'published'      => true,
                'date_published' => $date,
                'date_notified'  => $date,
            ];
        });
    }

    public function current(): static
    {
        return $this->state(function () {
            $date = fake()->dateTimeBetween('-3 months', 'now');
            return [
                'current'        => true,
                'published'      => true,
                'date_published' => $date,
                'date_notified'  => $date,
            ];
        });
    }
}
