<?php

namespace Mostbyte\Multidomain\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Throwable;

class MostbyteRollback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mostbyte:rollback {schema}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will rollback migrations and delete schema';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $schema = $this->argument('schema');

        try {
            $schema = Validator::validate(['schema' => $schema], ['schema' => 'string|max:50'])['schema'];
        } catch (Throwable $exception) {

            $this->components->error($exception->getMessage());
            return CommandAlias::INVALID;
        }

        $exists = DB::table('information_schema.schemata')
            ->where('schema_name', '=', $schema)
            ->exists();

        if (!$exists) {
            $this->components->error("Schema \"$schema\" not found!");
            return CommandAlias::INVALID;
        }

        $driver = config('database.default');
        config(["database.connections.$driver.schema" => $schema]);
        DB::purge($driver);
        Artisan::call('db:wipe --force');

        DB::statement("DROP SCHEMA $schema");

        if (!Storage::deleteDirectory("public/$schema")){
            $this->components->error("Error when deleting \"$schema\" folder!");
            return CommandAlias::INVALID;
        }

        $this->components->info('Rollback finished successfully!');
        return CommandAlias::SUCCESS;
    }
}
