<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user()?->loadMissing('role');

        if (! $user) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        if ($user->hasRole(UserRole::Admin->value) || $user->hasRole(...$roles)) {
            return $next($request);
        }

        abort(Response::HTTP_FORBIDDEN, 'Cette action n’est pas autorisée pour votre rôle.');
    }
}
