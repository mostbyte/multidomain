<?php

namespace Mostbyte\Multidomain\Managers;

use Illuminate\Support\Facades\DB;

class Manager
{
    public function __construct(protected DomainManager $domainManager)
    {
    }

    public function updateConfigs(string $schema = null, string $disk = 'public'): static
    {
        $schema = $schema ?? $this->getSchema();

        $this->updateAppConfig($schema)
            ->updateDatabaseConfig($schema)
            ->updateLogConfig($schema)
            ->updateFilesystemConfig($disk)
            ->updateTelescopeConfig($schema);

        return $this;
    }

    public function updateTelescopeConfig($schema = null): static
    {
        config(['telescope.path' => "$schema/telescope"]);

        return $this;
    }

    public function updateAppConfig(string $schema = null): static
    {
        config([
            'app.name' => strtoupper($schema ?? $this->getSchema()),
            'app.url' => $this->domainManager->getFullDomain(),
            'app.locale' => $this->domainManager->getLocale(),
        ]);

        return $this;
    }

    public function updateDatabaseConfig(string $schema = null): static
    {
        $driver = config('database.default');
        config(["database.connections.$driver.schema" => $schema ?? $this->getSchema()]);
        DB::purge($driver);

        return $this;
    }

    public function updateLogConfig(string $schema = null): static
    {
        $date = now()->toDateString();
        $scheme = $schema ?? $this->getSchema();
        config([
            'logging.channels.emergency.path' => storage_path("logs/{$scheme}/$date.log"),
            'logging.channels.single.path' => storage_path("logs/{$scheme}/$date.log"),
            'logging.channels.daily.path' => storage_path("logs/{$scheme}/$date.log"),
            'logging.channels.daily.emergency' => storage_path("logs/{$scheme}/$date.log"),
        ]);

        return $this;
    }

    public function updateFilesystemConfig($disk = 'public'): static
    {
        $url = config("filesystems.disks.$disk.url") . "/" . $this->getSchema();
        $root = config("filesystems.disks.$disk.root", '') . DIRECTORY_SEPARATOR . $this->getSchema();
        config([
            "filesystems.disks.$disk.root" => $root,
            "filesystems.disks.$disk.url" => $url,
        ]);

        return $this;
    }

    public function getSchema(): string
    {
        return $this->domainManager->getSubDomain() ?: 'public';
    }
}
