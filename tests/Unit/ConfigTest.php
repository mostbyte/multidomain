<?php

it('publishes default config', function () {
    $config = config('multidomain');

    expect($config)->toBeArray();
    expect($config['default_schema'])->toBe('public');
    expect($config['filesystem_disk'])->toBe('public');
    expect($config['cache']['enabled'])->toBeTrue();
    expect($config['cache']['ttl'])->toBe(3600);
    expect($config['cache']['prefix'])->toBe('multidomain_schema_exists');
    expect($config['schema_validation']['regex'])->toBe('/^[a-zA-Z0-9_\-]+$/');
    expect($config['schema_validation']['max_length'])->toBe(50);
    expect($config['excluded_schemas'])->toContain('public', 'information_schema');
    expect($config['excluded_schema_prefixes'])->toContain('pg_');
    expect($config['driver'])->toBeNull();
});
