<?php

namespace Mostbyte\Multidomain\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;

class VerifyApiKeyMiddleware
{
    /**
     * The header carrying the shared secret.
     */
    public const HEADER = 'X-API-KEY';

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $key = (string) config('multidomain.api_key');

        // Fail closed: an unconfigured key must never leave the routes open,
        // they can drop tenant schemas.
        if ($key === '') {
            logger()->warning('Multidomain API key is not configured, rejecting request', [
                'path' => $request->path(),
            ]);

            abort(403, 'Multidomain API key is not configured');
        }

        $provided = $request->header(self::HEADER);

        if ($provided === null) {
            abort(401, 'Missing API key');
        }

        if (! hash_equals($key, (string) $provided)) {
            abort(403, 'Invalid API key');
        }

        return $next($request);
    }
}
