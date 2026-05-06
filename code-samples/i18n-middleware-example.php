<?php

/**
 * Bilingual (NO/EN) session-based locale middleware for Laravel 12.
 *
 * This pattern drives all i18n in tekavogtil. Locale is stored in the PHP
 * session and set on every request via this middleware. Blade views use the
 * __('site.key') helper to render translated strings.
 *
 * ─── Setup ───────────────────────────────────────────────────────────────
 *
 * 1. Register in bootstrap/app.php:
 *
 *    ->withMiddleware(function (Middleware $middleware) {
 *        $middleware->appendToGroup('web', \App\Http\Middleware\SetLocale::class);
 *    })
 *
 * 2. Add the locale switch route to routes/web.php:
 *
 *    Route::get('/lang/{locale}', [LocaleController::class, 'switch'])
 *         ->name('lang.switch');
 *
 * 3. Create language files:
 *    lang/no/site.php  — ['key' => 'Norsk tekst']
 *    lang/en/site.php  — ['key' => 'English text']
 *
 * 4. In Blade views, use:
 *    {{ __('site.key') }}
 *
 * 5. Language toggle button (in nav):
 *    <a href="{{ route('lang.switch', 'en') }}">EN</a>
 *    <a href="{{ route('lang.switch', 'no') }}">NO</a>
 *
 * ─────────────────────────────────────────────────────────────────────────
 */

// ══════════════════════════════════════════════════════════════
// FILE 1 of 2: SetLocale middleware
// App\Http\Middleware\SetLocale
// ══════════════════════════════════════════════════════════════

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * Reads the locale from the session on every request.
     * Falls back to the application default locale defined in config/app.php.
     * Validates against a whitelist to prevent locale injection.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Read from session, fall back to app default ('no' for tekavogtil)
        $locale = session('locale', config('app.locale', 'no'));

        // Whitelist — only accept supported locales
        if (! in_array($locale, ['no', 'en'])) {
            $locale = 'no';
        }

        // Set for this request
        app()->setLocale($locale);

        return $next($request);
    }
}


// ══════════════════════════════════════════════════════════════
// FILE 2 of 2: LocaleController
// App\Http\Controllers\LocaleController
// ══════════════════════════════════════════════════════════════

namespace App\Http\Controllers;

class LocaleController extends Controller
{
    /**
     * Switch the session locale and redirect back to the previous page.
     *
     * Route: GET /lang/{locale}
     * Name:  lang.switch
     *
     * @param string $locale  'no' or 'en'
     */
    public function switch(string $locale)
    {
        $allowed = ['no', 'en'];

        if (in_array($locale, $allowed)) {
            session(['locale' => $locale]);
        }

        // Redirect back to the page the user was on
        return redirect()->back();
    }
}


/*
 * ─── Sample language file: lang/no/site.php ──────────────────────────────
 *
 * return [
 *
 *     // Navigation
 *     'nav.services'   => 'Tjenester',
 *     'nav.pricing'    => 'Priser',
 *     'nav.start'      => 'Start prosjekt',
 *     'nav.about'      => 'Om oss',
 *     'nav.contact'    => 'Kontakt',
 *
 *     // Homepage hero
 *     'home.hero.headline'    => 'Vi bygger programvare for norske bedrifter.',
 *     'home.hero.subheadline' => 'Fast pris. Rask levering. Norsk ansvar.',
 *     'home.hero.cta'         => 'Start et prosjekt →',
 *
 *     // Footer
 *     'footer.tagline'  => 'Programvare · KI · Norge',
 *     'footer.email'    => 'hei@tekavogtil.no',
 *
 * ];
 *
 * ─── Sample language file: lang/en/site.php ──────────────────────────────
 *
 * return [
 *
 *     // Navigation
 *     'nav.services'   => 'Services',
 *     'nav.pricing'    => 'Pricing',
 *     'nav.start'      => 'Start a project',
 *     'nav.about'      => 'About',
 *     'nav.contact'    => 'Contact',
 *
 *     // Homepage hero
 *     'home.hero.headline'    => 'We build software for Norwegian businesses.',
 *     'home.hero.subheadline' => 'Fixed price. Fast delivery. Norwegian accountability.',
 *     'home.hero.cta'         => 'Start a project →',
 *
 *     // Footer
 *     'footer.tagline'  => 'Software · AI · Norway',
 *     'footer.email'    => 'hei@tekavogtil.no',
 *
 * ];
 */
