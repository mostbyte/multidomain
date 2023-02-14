<?php

namespace Tests\Unit;

use Tests\TestCase;

class DomainTest extends TestCase
{
    public function test_database_schema_config()
    {
        $subDomain = explode('.', $this->httpRequest->getHost(), 2)[0];

        $driver = config('database.default');
        $this->assertTrue(config("database.connections.$driver.schema") === $subDomain);
    }
}