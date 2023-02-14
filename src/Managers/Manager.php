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
    public function updateDatabaseConfig(): static
    {

        $driver = config('database.default');
        config(["database.connections.$driver.schema" => $this->domainManager->getSubDomain()]);
        DB::purge($driver);

        return $this;
    }

    public function updateLogConfig(): static
    {
        $subDomain = $this->domainManager->getSubDomain();
        $date = now()->toDateString();
        config([
            'logging.channels.single.path' => storage_path("logs/{$subDomain}/$date.log"),
            'logging.channels.daily.path' => storage_path("logs/{$subDomain}/$date.log"),
            'logging.channels.daily.emergency' => storage_path("logs/{$subDomain}/$date.log"),
        ]);

        return $this;

    }

    public function updateAppConfig(): static
    {
        config([
            'app.name' => strtoupper($this->domainManager->getSubDomain()),
            'app.url' => $this->domainManager->getFullDomain(),
            'app.locale' => $this->domainManager->getLocale(),
        ]);

        return $this;
    }
}
