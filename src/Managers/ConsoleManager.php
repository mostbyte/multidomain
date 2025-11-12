<?php

namespace Mostbyte\Multidomain\Managers;

class ConsoleManager
{
    protected string $schema = '';

    public function getSchema(): string
    {
        return $this->schema;
    }

    public function setSchema(string $schema): void
    {
        $this->schema = $schema;
    }
}