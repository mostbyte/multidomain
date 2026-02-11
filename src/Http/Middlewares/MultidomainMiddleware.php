<?php

namespace Mostbyte\Multidomain\Http\Middlewares;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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

        $regex = config('multidomain.schema_validation.regex', '/^[a-zA-Z0-9_\-]+$/');

        if (!$domain || !preg_match($regex, $domain)) {
            abort(404, 'Invalid domain format');
        }

        if ($request->route()->getName() != 'mostbyte.multidomain.type') {
            $cacheEnabled = config('multidomain.cache.enabled', true);
            $cachePrefix = config('multidomain.cache.prefix', 'multidomain_schema_exists');
            $cacheTtl = config('multidomain.cache.ttl', 3600);

            if ($cacheEnabled) {
                $schemaExists = Cache::remember(
                    key: "{$cachePrefix}:{$domain}",
                    ttl: $cacheTtl,
                    callback: fn() => $this->schemaExists($domain)
                );
            } else {
                $schemaExists = $this->schemaExists($domain);
            }

            if (!$schemaExists) {
                abort(404, 'Domain not found');
            }
        }

        mostbyteDomainManager()->setSubdomain($domain);
        mostbyteManager()->updateConfigs($domain);

        return $next($request);
    }

    /**
     * Check if schema exists in PostgreSQL database
     *
     * @param string $domain
     * @return bool
     */
    protected function schemaExists(string $domain): bool
    {
        try {
            $result = DB::selectOne(
                "SELECT EXISTS(
                    SELECT 1
                    FROM information_schema.schemata
                    WHERE schema_name = ?
                ) as exists",
                [$domain]
            );

            return (bool) $result->exists;
        } catch (Exception $e) {
            // Log error and return false to deny access
            logger()->error('Domain validation error: ' . $e->getMessage(), [
                'domain' => $domain,
                'exception' => $e
            ]);
            return false;
        }
    }

}
