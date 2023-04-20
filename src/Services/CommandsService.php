<?php

namespace Mostbyte\Multidomain\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Mostbyte\Multidomain\Managers\ConsoleManager;
use Symfony\Component\Console\Command\Command as CommandAlias;

class CommandsService
{
    public function validateSchema(string $schema): string
    {
        return Validator::validate(['schema' => Str::lower($schema)], ['schema' => 'string|max:50|regex:/^[a-zA-Z\-]+$/'])['schema'];
    }

    /**
     * @throws Exception
     */
    public function execute(string $schema): string
    {
        $schema = $this->validateSchema($schema);
        $this->schemaNotFound($schema);
        $this->updateConfigs($schema);
        app(ConsoleManager::class)->setSchema($schema);
        return $schema;
    }

    /**
     * @throws Exception
     */
    public function schemaExists(string $schema): void
    {
        $exists = DB::table('information_schema.schemata')
            ->where('schema_name', '=', $schema)
            ->exists();
        if ($exists) throw new Exception("Schema \"$schema\" already exists!");
    }

    /**
     * @throws Exception
     */
    public function schemaNotFound(string $schema): void
    {
        $exists = DB::table('information_schema.schemata')
            ->where('schema_name', '=', $schema)
            ->exists();
        if (!$exists) throw new Exception("Schema \"$schema\" not found!");
    }

    public function updateConfigs(string $schema): void
    {
        $this->updateDbConfig($schema);
        $this->updateFilesystemConfig($schema);
    }

    protected function updateDbConfig(string $schema): void
    {
        $driver = config('database.default');
        config(["database.connections.$driver.schema" => $schema]);
        DB::purge($driver);
    }

    protected function updateFilesystemConfig(string $schema, string $disk = 'public'): void
    {
        $url = config('app.url') . "/storage/$schema/";
        $root = base_path() . "/storage/app/public/$schema";

        config([
            "filesystems.disks.$disk.root" => $root,
            "filesystems.disks.$disk.url" => $url,
        ]);
    }
}