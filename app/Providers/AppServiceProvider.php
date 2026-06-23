<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Submission;
use App\Observers\ArticleObserver;
use App\Observers\SubmissionObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Orcid\OrcidExtendSocialite;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Strict mode — catch N+1, lazy loading issues in dev
        Model::shouldBeStrict(! app()->isProduction());

        // Force HTTPS in production
        if (app()->isProduction()) {
            URL::forceScheme('https');
        }

        // Global password policy: min 8 chars, letters + numbers + symbols
        Password::defaults(function () {
            return app()->isProduction()
                ? Password::min(8)->letters()->numbers()->symbols()->uncompromised()
                : Password::min(8);
        });

        // ORCID Socialite provider registration
        Event::listen(SocialiteWasCalled::class, OrcidExtendSocialite::class);

        // Model observers
        Submission::observe(SubmissionObserver::class);
        Article::observe(ArticleObserver::class);
    }
}
