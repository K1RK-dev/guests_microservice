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
        $startTime = microtime(true);
        $memoryStart = memory_get_usage();

        $response = $next($request);

        $endTime = microtime(true);
        $memoryEnd = memory_get_usage();

        $response->headers->set('X-Debug-Time', round(($endTime - $startTime) * 1000, 3) . " ms spent");
        $response->headers->set('X-Debug-Memory', round(($memoryEnd - $memoryStart) / 1024, 3) . " kb/s used");

        return $response;
    }
}
