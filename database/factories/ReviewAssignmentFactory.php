<?php

namespace Database\Factories;

use App\Models\ReviewAssignment;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ReviewAssignment> */
class ReviewAssignmentFactory extends Factory
{
    public function definition(): array
    {
        $dateAssigned     = fake()->dateTimeBetween('-4 months', '-6 weeks');
        $dateResponseDue  = (clone $dateAssigned)->modify('+2 weeks');
        $dateDue          = (clone $dateAssigned)->modify('+5 weeks');

        return [
            'status'               => 'awaiting_response',
            'review_method'        => 'double_blind',
            'round'                => 1,
            'date_assigned'        => $dateAssigned,
            'date_notified'        => $dateAssigned,
            'date_confirmed'       => null,
            'date_due'             => $dateDue,
            'date_response_due'    => $dateResponseDue,
            'date_reminded'        => null,
            'date_completed'       => null,
            'date_cancelled'       => null,
            'reminder_was_automatic'=> 0,
            'competing_interests'  => null,
            'unconsidered'         => false,
        ];
    }

    public function accepted(): static
    {
        return $this->state(function () {
            $dateAssigned = fake()->dateTimeBetween('-3 months', '-6 weeks');
            return [
                'status'         => 'accepted',
                'date_confirmed' => (clone $dateAssigned)->modify('+3 days'),
            ];
        });
    }

    public function completed(): static
    {
        return $this->state(function () {
            $dateAssigned   = fake()->dateTimeBetween('-4 months', '-8 weeks');
            $dateCompleted  = (clone $dateAssigned)->modify('+4 weeks');
            return [
                'status'         => 'completed',
                'date_confirmed' => (clone $dateAssigned)->modify('+2 days'),
                'date_completed' => $dateCompleted,
            ];
        });
    }

    public function declined(): static
    {
        return $this->state(fn () => ['status' => 'declined']);
    }
}
