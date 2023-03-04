<?php

namespace MD\Tests;

use Illuminate\Http\Request;
use Mostbyte\Multidomain\Managers\DomainManager;
use Mostbyte\Multidomain\Managers\Manager;
use Mostbyte\Multidomain\MultidomainServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected Request $httpRequest;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpRequest = Request::create('https://messo.warehouse.mostbyte.uz/sads');

        /** @var DomainManager $domainManager */
        $domainManager = app(DomainManager::class, [
            'request' => $this->httpRequest
        ]);

        /** @var Manager $manager */
        $manager = app(Manager::class, compact('domainManager'));

        $manager->updateConfigs();
    }

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array<int, class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected function getPackageProviders($app)
    {
        return [
            MultidomainServiceProvider::class,
        ];
    }
}