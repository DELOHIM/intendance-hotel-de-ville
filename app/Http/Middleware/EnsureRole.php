<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Usage : ->middleware('role:admin')  (admin = admin + super_admin)
 *         ->middleware('role:super_admin')
 *         ->middleware('role:admin,maintenance')
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $autorise = false;
        foreach ($roles as $role) {
            if ($role === 'admin' && $user->isAdmin()) {
                $autorise = true;
            } elseif ($user->role === $role) {
                $autorise = true;
            }
        }

        abort_unless($autorise, 403, "Vous n'avez pas accès à cette page.");

        return $next($request);
    }
}
