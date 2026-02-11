<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Database Driver
    |--------------------------------------------------------------------------
    |
    | The database driver used for schema operations. This package requires
    | PostgreSQL since it uses PostgreSQL schemas for tenant isolation.
    | If null, the default database connection driver will be used.
    |
    */

    'driver' => null,

    /*
    |--------------------------------------------------------------------------
    | Default Schema
    |--------------------------------------------------------------------------
    |
    | The default schema name used when no tenant schema is active.
    |
    */

    'default_schema' => 'public',

    /*
    |--------------------------------------------------------------------------
    | Excluded Schemas
    |--------------------------------------------------------------------------
    |
    | Schemas that should be excluded from tenant operations such as
    | listing all schemas or running migrations with the --all flag.
    |
    */

    'excluded_schemas' => [
        'public',
        'information_schema',
    ],

    /*
    |--------------------------------------------------------------------------
    | Excluded Schema Prefixes
    |--------------------------------------------------------------------------
    |
    | Schema name prefixes to exclude from tenant operations. Any schema
    | whose name starts with one of these prefixes will be skipped.
    |
    */

    'excluded_schema_prefixes' => [
        'pg_',
    ],

    /*
    |--------------------------------------------------------------------------
    | Schema Validation
    |--------------------------------------------------------------------------
    |
    | Rules for validating schema names. The regex pattern determines which
    | characters are allowed, and max_length sets the upper bound.
    |
    */

    'schema_validation' => [
        'regex' => '/^[a-zA-Z0-9_\-]+$/',
        'max_length' => 50,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | Configuration for caching schema existence checks. Set 'enabled' to
    | false to disable caching entirely. The 'ttl' is in seconds.
    | The 'prefix' is used as a cache key prefix.
    |
    */

    'cache' => [
        'enabled' => true,
        'ttl' => 3600,
        'prefix' => 'multidomain_schema_exists',
    ],

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | The filesystem disk used for tenant-specific storage.
    |
    */

    'filesystem_disk' => 'public',

];
