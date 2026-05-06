<?php

namespace Database\Factories;

use App\Models\SubmissionContributor;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SubmissionContributor> */
class SubmissionContributorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'salutation'        => fake()->randomElement(['Dr.', 'Prof.', 'Mr.', 'Mrs.', 'Ms.', null]),
            'first_name'        => fake('id_ID')->firstName(),
            'last_name'         => fake('id_ID')->lastName(),
            'email'             => fake()->unique()->safeEmail(),
            'orcid'             => null,
            'affiliation'       => fake()->randomElement([
                'Universitas Indonesia', 'Institut Teknologi Bandung',
                'Universitas Gadjah Mada', 'Universitas Airlangga',
                'Universitas Diponegoro', 'Universitas Brawijaya',
                'Institut Pertanian Bogor', 'Universitas Padjadjaran',
            ]),
            'country'           => 'ID',
            'bio'               => fake()->optional(0.4)->paragraph(),
            'url'               => null,
            'user_group_id'     => null,
            'primary_contact'   => false,
            'include_in_browse' => true,
            'sequence'          => 1,
        ];
    }

    public function primaryContact(): static
    {
        return $this->state(fn () => ['primary_contact' => true]);
    }
}
