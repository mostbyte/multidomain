<?php

namespace Mostbyte\Multidomain\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Throwable;

class MostbyteMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mostbyte:migrate {--schema=}';

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
        $schema = $this->option('schema');

        try {
            $schema = Validator::validate(['schema' => $schema], [
                'schema' => 'required|string|max:50',
            ], [
                'schema.required' => 'The schema is required',
            ])['schema'];
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());
            return CommandAlias::INVALID;
        }

        $driver = config('database.default');
        config(["database.connections.$driver.schema" => $schema]);

        $checkQuery = "SELECT schema_name FROM information_schema.schemata WHERE schema_name = '$schema';";
        $result = DB::select($checkQuery);

        if (!empty($result)) {
            $this->error("$schema schema are already exists!");
            return CommandAlias::INVALID;
        }

        $query = "CREATE SCHEMA $schema";
        DB::select($query);

        Artisan::call('migrate');

        $this->comment('Migrated successfully!');
        return CommandAlias::SUCCESS;
    }
}
