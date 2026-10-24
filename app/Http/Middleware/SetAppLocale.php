<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetAppLocale
{
    /**
     * Supported locales list
     */
    public const SUPPORTED_LOCALES = ['id', 'en'];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasSession() && in_array($request->session()->get('locale'), self::SUPPORTED_LOCALES, true)) {
            app()->setLocale($request->session()->get('locale'));
        } elseif ($request->user() && in_array($request->user()->preferred_locale, self::SUPPORTED_LOCALES, true)) {
            app()->setLocale($request->user()->preferred_locale);
            if ($request->hasSession()) {
                $request->session()->put('locale', $request->user()->preferred_locale);
            }
        } else {
            app()->setLocale(config('app.locale', 'id'));
        }

        return $next($request);
    }
}
