<?php

namespace Upsoftware\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->header('Locale') ??
            $request->header('Lang') ??
            $request->get('Locale') ??
            $request->get('Lang') ??
            app()->getLocale();

        app()->setLocale($locale);
        return $next($request);
    }
}
