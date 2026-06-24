<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request)
            {
                return redirect()->intended(
                    $request->user()->hasRole('super_admin') ? '/admin' : '/dashboard'
                );
            }
        });
    }

    public function boot(): void
    {
        Fortify::createUsersUsing(\App\Actions\Fortify\CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(\App\Actions\Fortify\UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(\App\Actions\Fortify\UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(\App\Actions\Fortify\ResetUserPassword::class);

        // Auth views
        Fortify::loginView(fn () => view('auth.login'));
        Fortify::registerView(fn () => view('auth.register'));
        Fortify::requestPasswordResetLinkView(fn () => view('auth.forgot-password'));
        Fortify::resetPasswordView(fn ($request) => view('auth.reset-password', ['request' => $request]));
        Fortify::twoFactorChallengeView(fn () => view('auth.two-factor-challenge'));
        Fortify::verifyEmailView(fn () => view('auth.verify-email'));

        // Login: max 5 attempts/min per user+IP combo (brute force protection)
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = mb_strtolower($request->input(Fortify::username())).'|'.$request->ip();
            return [
                Limit::perMinute(5)->by($throttleKey),
                Limit::perMinute(20)->by($request->ip()),    // catch credential stuffing from 1 IP
            ];
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        // Register: max 3 accounts per 10 minutes per IP
        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinutes(10, 3)->by($request->ip());
        });

        // Password reset: max 3 per 10 minutes per IP
        RateLimiter::for('password-reset-link', function (Request $request) {
            return Limit::perMinutes(10, 3)->by($request->ip());
        });

        // Generic form saves (submissions, announcements, etc.)
        RateLimiter::for('form-submit', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });
    }
}
