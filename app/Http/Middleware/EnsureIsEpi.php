<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsEpi
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isEpi() && ! $request->user()?->isAdmin()) {
            abort(403, 'Acesso restrito a utilizadores EPI.');
        }

        return $next($request);
    }
}
