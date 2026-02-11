<?php

namespace Mostbyte\Multidomain;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Mostbyte\Multidomain\Managers\ConsoleManager;
use Mostbyte\Multidomain\Managers\DomainManager;
use Mostbyte\Multidomain\Managers\Manager;
use Mostbyte\Multidomain\Services\CommandsService;

class Multidomain
{
    public function __construct(
        protected Manager $manager,
        protected DomainManager $domainManager,
    ) {}

    /**
     * Switch the application context to the given tenant schema.
     */
    public function setTenant(string $schema): static
    {
        $this->manager->updateConfigs($schema);
        return $this;
    }

    /**
     * Get the currently active schema name.
     */
    public function currentSchema(): string
    {
        return $this->manager->getSchema();
    }

    /**
     * Get the DomainManager instance.
     */
    public function domainManager(): DomainManager
    {
        return $this->domainManager;
    }

    /**
     * Get the Manager instance.
     */
    public function manager(): Manager
    {
        return $this->manager;
    }

    /**
     * List all tenant schemas (excluding system schemas).
     *
     * @return array<string>
     */
    public function schemas(): array
    {
        $excluded = config('multidomain.excluded_schemas', ['public', 'information_schema']);
        $excludedPrefixes = config('multidomain.excluded_schema_prefixes', ['pg_']);

        $rows = DB::select('SELECT schema_name FROM information_schema.schemata');

        return array_values(array_map(
            fn ($row) => $row->schema_name,
            array_filter($rows, function ($row) use ($excluded, $excludedPrefixes) {
                if (in_array($row->schema_name, $excluded)) {
                    return false;
                }
                foreach ($excludedPrefixes as $prefix) {
                    if (Str::startsWith($row->schema_name, $prefix)) {
                        return false;
                    }
                }
                return true;
            })
        ));
    }

    /**
     * Check if a tenant schema exists.
     */
    public function schemaExists(string $schema): bool
    {
        return DB::table('information_schema.schemata')
            ->where('schema_name', '=', $schema)
            ->exists();
    }
}
