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
        return sprintf("mostbyte:%s %s", $this->value, mostbyteManager()->getSchema());
    }

    public function is(array|self $type): bool
    {
        return in_array($this, Arr::wrap($type));
    }
}
