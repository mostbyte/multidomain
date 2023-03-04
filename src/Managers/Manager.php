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
            ->updateLogConfig();

        return $this;
    }

    public function updateAppConfig(): static
    {
        config([
            'app.name' => strtoupper($this->getScheme()),
            'app.url' => $this->domainManager->getFullDomain(),
            'app.locale' => $this->domainManager->getLocale(),
        ]);

        return $this;
    }

    public function updateDatabaseConfig(): static
    {
        $driver = config('database.default');
        config(["database.connections.$driver.schema" => $this->getScheme()]);
        DB::purge($driver);

        return $this;
    }

    public function updateLogConfig(): static
    {
        $date = now()->toDateString();
        $scheme = $this->getScheme();
        config([
            'logging.channels.single.path' => storage_path("logs/{$scheme}/$date.log"),
            'logging.channels.daily.path' => storage_path("logs/{$scheme}/$date.log"),
            'logging.channels.daily.emergency' => storage_path("logs/{$scheme}/$date.log"),
        ]);

        return $this;
    }

    public function getScheme(): string
    {
        return $this->domainManager->getSubDomain() ?: 'public';
    }
}
