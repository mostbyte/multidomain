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
        $domain = $request->route('domain');
        $request->route()->forgetParameter('domain');
        mostbyteDomainManager()->setSubdomain($domain);
        mostbyteManager()->updateConfigs();

        return $next($request);
    }
}