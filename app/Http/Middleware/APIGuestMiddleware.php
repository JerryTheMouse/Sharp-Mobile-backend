<?php

namespace App\Http\Middleware;

use Closure;

class APIGuestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $user = \JWTAuth::parseToken()->authenticate();
            if ($user)
                return \Response::json(['error'=>"Can't access these routes while logged-in"], 400);

        } catch (\Exception $e) {
            return $next($request);

        }

        return $next($request);
    }
}

