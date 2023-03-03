<?php

namespace Mostbyte\Multidomain\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Throwable;

class MostbyteMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mostbyte:migrate {schema} {--seed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will create new schema and run migration for it';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $schema = $this->argument('schema');
        $toSeed = $this->option('seed');

        try {

            $schema = Validator::validate(['schema' => $schema], ['schema' => 'string|max:50'])['schema'];

        } catch (Throwable $exception) {

            $this->components->error($exception->getMessage());
            return CommandAlias::INVALID;
        }

        $exists = DB::table('information_schema.schemata')
            ->where('schema_name', '=', $schema)
            ->exists();

        if ($exists) {
            $this->components->error("Schema \"$schema\" already exists!");
            return CommandAlias::INVALID;
        }

        DB::statement("CREATE SCHEMA $schema");

        $driver = config('database.default');
        config(["database.connections.$driver.schema" => $schema]);
        DB::purge($driver);

        if ($toSeed) {
            $this->components->info('Migration and seeding started');
        }

        Artisan::call($toSeed ? 'migrate --seed' : 'migrate');

        $this->components->info('Migrated successfully!');
        return CommandAlias::SUCCESS;
    }
}
