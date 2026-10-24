<?php

namespace App\Http\Middleware;

use App\Models\Location;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class EnsureLocationScope
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user) {
            $activeLocation = $user->getActiveLocation();
            $userLocations = $user->isSuperAdmin() || $user->isOwner()
                ? Location::active()->where('type', '!=', Location::TYPE_TRANSIT)->get()
                : $user->locations()->where('is_active', true)->get();

            // Share globally with all views
            View::share('activeLocation', $activeLocation);
            View::share('userLocations', $userLocations);
            View::share('currentUser', $user);
            View::share('activeRole', $user->primaryRole());
        }

        return $next($request);
    }
}
