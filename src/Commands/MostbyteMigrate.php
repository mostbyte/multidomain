<?php

namespace Mostbyte\Multidomain\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Mostbyte\Multidomain\Services\CommandsService;
use Throwable;

class MostbyteMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mostbyte:migrate {schema?}
                {--force : Force the operation to run when in production}
                {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
                {--pretend : Dump the SQL queries that would be run}
                {--seed : Indicates if the seed task should be re-run}
                {--step : Force the migrations to be run so they can be rolled back individually}
                {--all : Run migrations for all schemas}';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('all')) {
            $excluded = config('multidomain.excluded_schemas', ['public', 'information_schema']);
            $excludedPrefixes = config('multidomain.excluded_schema_prefixes', ['pg_']);

            $schemas = array_filter(DB::select('SELECT schema_name FROM information_schema.schemata;'), function ($obj) use ($excluded, $excludedPrefixes) {
                if (in_array($obj->schema_name, $excluded)) {
                    return false;
                }
                foreach ($excludedPrefixes as $prefix) {
                    if (Str::startsWith($obj->schema_name, $prefix)) {
                        return false;
                    }
                }

                return true;
            });
        } else {
            $regex = config('multidomain.schema_validation.regex', '/^[a-zA-Z0-9_\-]+$/');
            $maxLength = config('multidomain.schema_validation.max_length', 50);

            try {
                $schema = Validator::validate(['schema' => Str::lower($this->argument('schema'))], ['schema' => "required|string|max:$maxLength|regex:$regex"])['schema'];
            } catch (Throwable $exception) {
                $this->components->error($exception->getMessage());

                return self::INVALID;
            }

            $obj = new class
            {
                public string $schema_name;
            };
            $obj->schema_name = $schema;

            $schemas = [$obj];
        }

        foreach ($schemas as $schema) {
            $result = $this->migrate($schema->schema_name);

            if ($result != self::SUCCESS) {
                $this->components->error("Can't run migrations for schema {$schema->schema_name}");

                return self::FAILURE;
            }
        }

        return self::SUCCESS;
    }

    protected function migrate(string $schema): int
    {
        /** @var CommandsService $commandService */
        $commandService = app(CommandsService::class);

        try {
            $commandService->execute($schema);
        } catch (Throwable $exception) {
            $this->components->error($exception->getMessage());

            return self::INVALID;
        }

        $this->components->task('Migrating tables', fn () => $this->call('migrate', array_filter([
            '--seed' => $this->option('seed'),
            '--realpath' => $this->option('realpath'),
            '--pretend' => $this->option('pretend'),
            '--step' => $this->option('seed'),
            '--force' => $this->option('force'),
        ])) == 0);

        return self::SUCCESS;
    }
}
