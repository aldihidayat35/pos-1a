<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetAppLocale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Switch application locale via POST (form / AJAX)
     */
    public function switch(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', 'in:' . implode(',', SetAppLocale::SUPPORTED_LOCALES)],
        ]);

        $locale = $validated['locale'];

        // Persist to session
        $request->session()->put('locale', $locale);

        // Update user preference if authenticated
        if ($user = $request->user()) {
            $user->update(['preferred_locale' => $locale]);
        }

        app()->setLocale($locale);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'locale' => $locale,
                'message' => 'Bahasa berhasil diubah / Language changed successfully',
            ]);
        }

        return back()->with('success', 'Bahasa berhasil diubah');
    }

    /**
     * Switch application locale via GET route
     */
    public function set(Request $request, string $locale): RedirectResponse
    {
        if (!in_array($locale, SetAppLocale::SUPPORTED_LOCALES, true)) {
            $locale = 'id';
        }

        $request->session()->put('locale', $locale);

        if ($user = $request->user()) {
            $user->update(['preferred_locale' => $locale]);
        }

        app()->setLocale($locale);

        return back()->with('success', 'Bahasa berhasil diubah');
    }
}
