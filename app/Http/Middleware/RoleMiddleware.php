<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Pakai: ->middleware('role:admin') atau ->middleware('role:admin,pengelola')
     * Admin selalu diizinkan jika 'admin' ada di daftar roles.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (!$user) abort(401);

        // jika tidak disupply role, izinkan
        if (empty($roles)) return $next($request);

        if (in_array($user->role, $roles, true)) {
            return $next($request);
        }

        // Boleh override: jika route minta 'pengelola' tapi user admin, tetap boleh
        if (in_array('pengelola', $roles, true) && $user->role === 'admin') {
            return $next($request);
        }

        abort(403);
    }
}
