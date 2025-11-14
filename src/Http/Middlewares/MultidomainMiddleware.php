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

        // Validate domain format (allow lowercase letters, numbers, hyphens, and underscores)
        if (!$domain || !preg_match('/^[a-zA-Z\-]+$/', $domain)) {
            abort(404, 'Invalid domain format');
        }

        // Check if schema exists in database (with caching)
        $schemaExists = Cache::remember(
            key: "domain_schema_exists:$domain",
            ttl: 3600, // 1 hour
            callback: fn() => $this->schemaExists($domain)
        );

        if (!$schemaExists) {
            abort(404, 'Domain not found');
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
