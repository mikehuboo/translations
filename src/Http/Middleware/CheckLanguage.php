<?php

declare(strict_types=1);

namespace Huboo\I18nLoader\Http\Middleware;

use Closure;

class CheckLanguage
{
    const DEFAULT_LANG = 'en';

    /**
     * Set the language as needed.
     *
     * @param $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $config = app('translation.cache')->getConfig();

        $defaultLanguage = $config['default_locale'] ?? config('language.default');
        $locales = $config['available_locales'] ?? [config('language.default')];

        $requestedLanguage = $request->server('HTTP_ACCEPT_LANGUAGE', $defaultLanguage);

        $lang = substr(strtolower($requestedLanguage), 0, 2);

        if (in_array($lang, $locales)) {
            app()->setLocale($lang);
        } elseif (in_array($defaultLanguage, $locales)) {
            app()->setLocale($defaultLanguage);
        } else {
            app()->setLocale(self::DEFAULT_LANG);
        }

        return $next($request);
    }
}
