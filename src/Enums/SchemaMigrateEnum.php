<?php

namespace Mostbyte\Multidomain\Enums;

use Illuminate\Support\Arr;

enum SchemaMigrateEnum: string
{
    case MIGRATE = 'migrate';
    case SCHEMA = 'schema';
    case ROLLBACK = 'rollback';

    public function command(): string
    {
        $command = sprintf('mostbyte:%s %s', $this->value, mostbyteManager()->getSchema());

        if ($this != self::SCHEMA) {
            $command .= ' --force';
        }

        return $command;
    }

    public function is(array|self $type): bool
    {
        return in_array($this, Arr::wrap($type));
    }
}
