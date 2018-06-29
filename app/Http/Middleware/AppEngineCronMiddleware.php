<?php

namespace App\Http\Middleware;

use Closure;

class AppEngineCronMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->hasHeader('X-Appengine-Cron')) {
            return response()->json(trans('auth.unauthorized'), 401);
        }
        
        return $next($request);
    }
}
