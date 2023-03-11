<?php

namespace Mostbyte\Multidomain\Managers;

use Illuminate\Support\Facades\DB;

class Manager
{
    public function __construct(protected DomainManager $domainManager)
    {
    }

    public function updateConfigs(): static
    {
        $this->updateAppConfig()
            ->updateDatabaseConfig()
            ->updateLogConfig()
            ->updateFilesystemConfig();

        return $this;
    }

    public function updateAppConfig(): static
    {
        config([
            'app.name' => strtoupper($this->getSchema()),
            'app.url' => $this->domainManager->getFullDomain(),
            'app.locale' => $this->domainManager->getLocale(),
        ]);

        return $this;
    }

    public function updateDatabaseConfig(): static
    {
        $driver = config('database.default');
        config(["database.connections.$driver.schema" => $this->getSchema()]);
        DB::purge($driver);

        return $this;
    }

    public function updateLogConfig(): static
    {
        $date = now()->toDateString();
        $scheme = $this->getSchema();
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
