<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    protected static ?string $password;

    private static array $salutations = ['Dr.', 'Prof.', 'Mr.', 'Mrs.', 'Ms.', null, null];

    public function definition(): array
    {
        return [
            'salutation'       => fake()->randomElement(self::$salutations),
            'first_name'       => fake('id_ID')->firstName(),
            'last_name'        => fake('id_ID')->lastName(),
            'email'            => fake()->unique()->safeEmail(),
            'email_verified_at'=> now(),
            'password'         => static::$password ??= Hash::make('password'),
            'orcid'            => null,
            'affiliation'      => fake()->randomElement([
                'Universitas Indonesia', 'Institut Teknologi Bandung',
                'Universitas Gadjah Mada', 'Universitas Airlangga',
                'Universitas Diponegoro', 'Universitas Brawijaya',
                'Institut Pertanian Bogor', 'Universitas Padjadjaran',
                'Universitas Hasanuddin', 'Universitas Sebelas Maret',
            ]),
            'country'          => 'ID',
            'bio'              => fake()->optional(0.6)->paragraph(),
            'phone'            => fake()->optional(0.5)->phoneNumber(),
            'locale'           => 'id',
            'is_disabled'      => false,
            'remember_token'   => Str::random(10),
        ];
    }

    public function withOrcid(): static
    {
        return $this->state(fn () => [
            'orcid' => implode('-', [
                str_pad(fake()->numerify('####'), 4, '0', STR_PAD_LEFT),
                str_pad(fake()->numerify('####'), 4, '0', STR_PAD_LEFT),
                str_pad(fake()->numerify('####'), 4, '0', STR_PAD_LEFT),
                str_pad(fake()->numerify('####'), 4, '0', STR_PAD_LEFT),
            ]),
        ]);
    }

    public function disabled(): static
    {
        return $this->state(fn () => ['is_disabled' => true]);
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }
}
