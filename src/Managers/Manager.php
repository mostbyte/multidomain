<?php

namespace Mostbyte\Multidomain\Managers;

use Illuminate\Support\Facades\DB;

class Manager
{
    protected ?string $defaultDiskRoot = null;

    protected ?string $defaultDiskUrl = null;

    public function __construct(protected DomainManager $domainManager) {}

    public function updateConfigs(?string $schema = null, ?string $disk = null): static
    {
        if ($schema) {
            $this->domainManager->setSubdomain($schema);
        }

        $schema = $this->domainManager->getSubDomain();
        $disk = $disk ?? config('multidomain.filesystem_disk', 'public');

        $this->updateAppConfig($schema)
            ->updateDatabaseConfig($schema)
            ->updateLogConfig($schema)
            ->updateFilesystemConfig($disk)
            ->updateTelescopeConfig($schema);

        return $this;
    }

    public function updateTelescopeConfig(?string $schema = null): static
    {
        config(['telescope.path' => "$schema/telescope"]);

        return $this;
    }

    public function updateAppConfig(?string $schema = null): static
    {
        config([
            'app.name' => strtoupper($schema ?? $this->getSchema()),
            'app.url' => $this->domainManager->getFullDomain(),
            'app.locale' => $this->domainManager->getLocale(),
        ]);

        return $this;
    }

    public function updateDatabaseConfig(?string $schema = null): static
    {
        $driver = config('multidomain.driver') ?? config('database.default');
        config(["database.connections.$driver.schema" => $schema ?? $this->getSchema()]);
        DB::purge($driver);

        return $this;
    }

    public function updateLogConfig(?string $schema = null): static
    {
        $date = now()->toDateString();
        $scheme = $schema ?? $this->getSchema();
        config([
            'logging.channels.emergency.path' => storage_path("logs/$scheme/$date.log"),
            'logging.channels.single.path' => storage_path("logs/$scheme/$date.log"),
            'logging.channels.daily.path' => storage_path("logs/$scheme/$date.log"),
            'logging.channels.daily.emergency' => storage_path("logs/$scheme/$date.log"),
        ]);

        return $this;
    }

    public function updateFilesystemConfig($disk = 'public'): static
    {
        if (! $this->defaultDiskRoot) {
            $this->defaultDiskRoot = config("filesystems.disks.$disk.root", '');
        }

        if (! $this->defaultDiskUrl) {
            $this->defaultDiskUrl = config("filesystems.disks.$disk.url", '');
        }

        $url = $this->defaultDiskUrl.'/'.$this->getSchema();
        $root = $this->defaultDiskRoot.DIRECTORY_SEPARATOR.$this->getSchema();

        config([
            "filesystems.disks.$disk.root" => $root,
            "filesystems.disks.$disk.url" => $url,
        ]);

        return $this;
    }

    public function getSchema(): string
    {
        return $this->domainManager->getSubDomain() ?: config('multidomain.default_schema', 'public');
    }
}
