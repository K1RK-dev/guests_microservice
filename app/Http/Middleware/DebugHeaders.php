<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DebugHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $response->headers->set('X-Debug-Time', microtime(true));
        $response->headers->set('X-Debug-Memory', memory_get_usage());

        return $response;
    }
}
