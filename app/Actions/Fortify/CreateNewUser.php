<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input): User
    {
        // ── Honeypot check ────────────────────────────────────────────────────
        if (!empty($input['hp_website'])) {
            // Bot detected — fail silently with a generic email error
            throw ValidationException::withMessages([
                'email' => ['Pendaftaran tidak dapat diproses. Silakan coba lagi.'],
            ]);
        }

        // ── Rate limiting: max 5 registrations per IP per hour ────────────────
        $rateLimitKey = 'register_ip:' . request()->ip();
        $attempts     = (int) Cache::get($rateLimitKey, 0);

        if ($attempts >= 5) {
            throw ValidationException::withMessages([
                'email' => ['Terlalu banyak percobaan pendaftaran dari IP ini. Coba lagi dalam 1 jam.'],
            ]);
        }

        // ── Privacy consent required ──────────────────────────────────────────
        if (empty($input['privacy_consent'])) {
            throw ValidationException::withMessages([
                'privacy_consent' => ['Anda harus menyetujui pernyataan privasi untuk mendaftar.'],
            ]);
        }

        // ── Main validation ───────────────────────────────────────────────────
        Validator::make($input, [
            'first_name'  => ['required', 'string', 'max:100'],
            'last_name'   => ['required', 'string', 'max:100'],
            'email'       => ['required', 'string', 'email:rfc,dns', 'max:255', Rule::unique(User::class)],
            'affiliation' => ['nullable', 'string', 'max:255'],
            'country'     => ['nullable', 'string', 'size:2'],
            'orcid'       => [
                'nullable', 'string',
                'regex:/^\d{4}-\d{4}-\d{4}-\d{3}[\dX]$/',
                Rule::unique(User::class),
            ],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'first_name.required'  => 'Nama depan wajib diisi.',
            'last_name.required'   => 'Nama belakang wajib diisi.',
            'email.required'       => 'Alamat email wajib diisi.',
            'email.email'          => 'Format email tidak valid.',
            'email.unique'         => 'Email ini sudah terdaftar. Silakan masuk atau gunakan email lain.',
            'orcid.regex'          => 'Format ORCID iD tidak valid. Gunakan format: 0000-0000-0000-0000.',
            'orcid.unique'         => 'ORCID iD ini sudah terhubung dengan akun lain.',
            'password.required'    => 'Kata sandi wajib diisi.',
            'password.confirmed'   => 'Konfirmasi kata sandi tidak cocok.',
            'password.min'         => 'Kata sandi minimal 8 karakter.',
        ])->validate();

        // ── Increment rate limit counter ──────────────────────────────────────
        Cache::put($rateLimitKey, $attempts + 1, now()->addHour());

        // ── Create user ───────────────────────────────────────────────────────
        $user = User::create([
            'first_name'  => strip_tags(trim($input['first_name'])),
            'last_name'   => strip_tags(trim($input['last_name'])),
            'email'       => mb_strtolower(trim($input['email'])),
            'password'    => Hash::make($input['password']),
            'affiliation' => isset($input['affiliation']) ? strip_tags(trim($input['affiliation'])) : null,
            'country'     => $input['country'] ?? null,
            'orcid'       => isset($input['orcid']) && $input['orcid'] !== '' ? trim($input['orcid']) : null,
            'locale'      => 'id',
        ]);

        // ── Assign roles ──────────────────────────────────────────────────────
        $user->assignRole('author');

        if (!empty($input['register_as_reviewer'])) {
            $user->assignRole('reviewer');
        }

        return $user;
    }
}
