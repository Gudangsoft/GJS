<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $available = json_decode(
            Setting::get('language.available', '["id","en"]'),
            true
        ) ?? ['id', 'en'];

        $locale = null;

        // 1. Authenticated user's saved preference
        if (Auth::check() && Auth::user()->locale) {
            $locale = Auth::user()->locale;
        }

        // 2. Session value (from language switcher)
        if (!$locale) {
            $locale = session('locale');
        }

        // 3. Site admin default setting
        if (!$locale) {
            $locale = Setting::get('language.default', config('app.locale', 'id'));
        }

        // Ensure locale is in the allowed list
        if (!in_array($locale, $available)) {
            $locale = $available[0] ?? config('app.locale', 'id');
        }

        App::setLocale($locale);
        \Carbon\Carbon::setLocale($locale);

        // RTL support: set layout direction for Arabic
        view()->share('localeDir', $locale === 'ar' ? 'rtl' : 'ltr');
        view()->share('currentLocale', $locale);
        view()->share('availableLocales', $available);

        return $next($request);
    }
}
