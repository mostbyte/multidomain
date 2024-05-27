<?php

namespace Mostbyte\Multidomain\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Mostbyte\Multidomain\Services\CommandsService;
use stdClass;
use Symfony\Component\Console\Command\Command as CommandAlias;
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
     *
     * @return int
     */
    public function handle(): int
    {
        if ($this->option('all')) {
            $schemas = array_filter(DB::select('SELECT schema_name FROM information_schema.schemata;'), function ($obj) {
                return !(in_array($obj->schema_name, ['public', 'information_schema']) ||
                    Str::of($obj->schema_name)->startsWith('pg_'));
            });
        } else {

            try {
                $schema = Validator::validate(['schema' => Str::lower($this->argument('schema'))], ['schema' => 'required|string|max:50|regex:/^[a-zA-Z\-]+$/'])['schema'];
            } catch (Throwable $exception) {
                $this->components->error($exception->getMessage());
                return CommandAlias::INVALID;
            }

            $obj = new class {
                public string $schema_name;
            };
            $obj->schema_name = $schema;

            $schemas = [$obj];
        }

        foreach ($schemas as $schema) {
            $result = $this->migrate($schema->schema_name);

            if ($result != CommandAlias::SUCCESS) {
                $this->components->error("Can't run migrations for schema {$schema->schema_name}");
                return CommandAlias::FAILURE;
            }
        }

        return CommandAlias::SUCCESS;
    }


    protected function migrate(string $schema): int
    {
        /** @var CommandsService $commandService */
        $commandService = app(CommandsService::class);

        try {
            $commandService->execute($schema);
        } catch (Throwable $exception) {
            $this->components->error($exception->getMessage());
            return CommandAlias::INVALID;
        }

        $this->components->task('Migrating tables', fn() => $this->call('migrate', array_filter([
                '--seed' => $this->option('seed'),
                '--realpath' => $this->option('realpath'),
                '--pretend' => $this->option('pretend'),
                '--step' => $this->option('seed'),
                '--force' => $this->option('force'),
            ])) == 0);

        if (PHP_OS_FAMILY !== "Windows") {
            shell_exec("chown -R www-data:www-data ./storage");
        }

        return CommandAlias::SUCCESS;
    }
}
