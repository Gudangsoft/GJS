<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'first_name'  => ['required', 'string', 'max:255'],
            'last_name'   => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'affiliation' => ['nullable', 'string', 'max:255'],
            'orcid'       => ['nullable', 'string', 'max:19'],
        ])->validate();

        if ($input['email'] !== $user->email && $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'first_name'  => strip_tags($input['first_name']),
                'last_name'   => strip_tags($input['last_name']),
                'email'       => $input['email'],
                'affiliation' => isset($input['affiliation']) ? strip_tags($input['affiliation']) : null,
                'orcid'       => $input['orcid'] ?? null,
            ])->save();
        }
    }

    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'first_name'        => strip_tags($input['first_name']),
            'last_name'         => strip_tags($input['last_name']),
            'email'             => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
