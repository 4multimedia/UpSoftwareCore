<?php

namespace Upsoftware\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictedIpMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $config_allowed_ips = config('upsoftware.allowed_id');
        if (!is_array($config_allowed_ips)) {
            $config_allowed_ips = [];
        }
        $allowed_ips = array_merge([
            '127.0.0.1',
        ], $config_allowed_ips);
        
        $client_ip = $request->ip();
        if (!in_array($client_ip, $allowed_ips)) {
            return response()->json(['message' => 'Unauthorized.', 'ip' => $client_ip], 403);
        }

        return $next($request);
    }
}
