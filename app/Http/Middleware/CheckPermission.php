<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->is_active) {
            auth()->logout();
            return redirect()->route('login')->withErrors(['login' => 'Akun Anda dinonaktifkan oleh Administrator.']);
        }

        if (! $user->hasPermission($permission)) {
            abort(403, 'Akses Ditolak: Akun Anda (' . ($user->primaryRole()?->label ?? 'User') . ') tidak memiliki izin [' . $permission . '] untuk tindakan ini.');
        }

        return $next($request);
    }
}
