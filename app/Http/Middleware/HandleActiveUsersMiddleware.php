<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class HandleActiveUsersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request):Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Route::currentRouteName() !== 'no-active' && \auth()->check() && auth()->user()->isMember() && !\auth()->user()->isActive()) {
            return redirect()->route('no-active');
        }

        return $next($request);
    }
}
