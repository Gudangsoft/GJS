<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class OrcidController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('orcid')
            ->scopes(['/authenticate'])
            ->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $orcidUser = Socialite::driver('orcid')->user();
        } catch (\Throwable) {
            return redirect()->route('login')
                ->with('error', 'Autentikasi ORCID gagal. Silakan coba lagi.');
        }

        $orcidId = $orcidUser->getId();

        // 1. Find by ORCID iD
        $user = User::where('orcid', $orcidId)->first();

        // 2. Find by email (link ORCID to existing account)
        if (!$user && $orcidUser->getEmail()) {
            $user = User::where('email', $orcidUser->getEmail())->first();
            if ($user) {
                $user->update(['orcid' => $orcidId]);
            }
        }

        // 3. Create a new account from ORCID data
        if (!$user) {
            $nameParts = explode(' ', trim($orcidUser->getName() ?? ''), 2);
            $user = User::create([
                'first_name'        => $nameParts[0] ?? 'ORCID',
                'last_name'         => $nameParts[1] ?? 'User',
                'email'             => $orcidUser->getEmail() ?? $orcidId . '@orcid.placeholder',
                'orcid'             => $orcidId,
                'password'          => bcrypt(Str::random(40)),
                'email_verified_at' => now(),
                'locale'            => 'id',
                'country'           => 'ID',
                'is_disabled'       => false,
            ]);
            $user->assignRole('author');
        }

        if ($user->is_disabled) {
            return redirect()->route('login')
                ->with('error', 'Akun Anda telah dinonaktifkan. Hubungi administrator.');
        }

        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard'));
    }
}
