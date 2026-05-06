<?php

namespace Database\Factories;

use App\Models\ReviewRound;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ReviewRound> */
class ReviewRoundFactory extends Factory
{
    public function definition(): array
    {
        return [
            'round'  => 1,
            'status' => 'awaiting_reviewers',
        ];
    }

    public function reviewsCompleted(): static
    {
        return $this->state(fn () => ['status' => 'reviews_completed']);
    }

    public function revisionsRequested(): static
    {
        return $this->state(fn () => ['status' => 'revisions_requested']);
    }

    public function accepted(): static
    {
        return $this->state(fn () => ['status' => 'accepted']);
    }
}
