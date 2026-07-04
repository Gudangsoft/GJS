<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Setting;
use App\Models\Submission;
use App\Observers\ArticleObserver;
use App\Observers\SubmissionObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
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

        // Share brand variables with all public-facing layouts
        View::composer(
            ['layouts.app', 'layouts.auth', 'layouts.manager', 'errors.layout'],
            function ($view) {
                static $bd = null;
                if ($bd === null) {
                    try {
                        $bd = Setting::getGroup('brand');
                    } catch (\Throwable) {
                        $bd = [];
                    }
                }

                $defaultName   = config('app.name');
                $defaultAbbrev = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $defaultName), 0, 3)) ?: 'APP';

                $view->with([
                    'brandName'          => $bd['site_name']              ?? $defaultName,
                    'brandAbbrev'        => $bd['abbrev']                 ?? $defaultAbbrev,
                    'brandTagline'       => $bd['tagline']                ?? '',
                    'brandLogo'          => isset($bd['logo']) && $bd['logo'] ? asset('storage/' . $bd['logo']) : null,
                    'brandFavicon'       => isset($bd['favicon']) && $bd['favicon'] ? asset('storage/' . $bd['favicon']) : null,
                    'brandCopyright'     => $bd['copyright']              ?? '',
                    'brandFooterTagline' => $bd['footer_tagline']         ?? ($bd['description'] ?? ''),
                    'brandFooterIdx'     => ($bd['footer_show_indexing']  ?? '1') === '1',
                    'brandFooterSoc'     => ($bd['footer_show_social']    ?? '0') === '1',
                    'brandFooterColTitle'    => $bd['footer_col_title']         ?? '',
                    'brandFooterLinks'       => json_decode($bd['footer_links'] ?? '[]', true) ?: [],
                    'brandBuiltWith'         => $bd['footer_built_with']         ?? 'Laravel & Filament',
                    'brandBuiltWithUrl'      => $bd['footer_built_with_url']     ?? '',
                    'brandShowBuiltWith'     => ($bd['footer_show_built_with']   ?? '1') === '1',
                    'brandSocials'       => [
                        'facebook'  => $bd['social_facebook']  ?? '',
                        'twitter'   => $bd['social_twitter']   ?? '',
                        'instagram' => $bd['social_instagram'] ?? '',
                        'linkedin'  => $bd['social_linkedin']  ?? '',
                        'youtube'   => $bd['social_youtube']   ?? '',
                        'whatsapp'  => $bd['social_whatsapp']  ?? '',
                    ],
                ]);
            }
        );
    }
}
