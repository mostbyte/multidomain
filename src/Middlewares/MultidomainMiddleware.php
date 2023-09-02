<?php

namespace Mostbyte\Multidomain\Middlewares;

use Closure;
use Illuminate\Http\Request;

class MultidomainMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $request->route()->forgetParameter('domain');

        return $next($request);
    }
}
