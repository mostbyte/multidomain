<?php

namespace Mostbyte\Multidomain\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Database\Events\DatabaseRefreshed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Mostbyte\Multidomain\Services\CommandsService;
use Symfony\Component\Console\Input\InputOption;
use Throwable;

class MostbyteFresh extends Command
{
    use ConfirmableTrait;

    protected $signature = 'mostbyte:fresh {schema}
        {--drop-views} {--drop-types} {--path} {--realpath} {--schema-path} {--seed} {--seeder=} {--step}
    ';

    public function handle(): int
    {
        if (!$this->confirmToProceed()) {
            return self::FAILURE;
        }

        /** @var CommandsService $commandService */
        $commandService = app(CommandsService::class);

        try {
            $schema = $commandService->execute($this->argument('schema'));
        } catch (Throwable $exception) {
            $this->components->error($exception->getMessage());
            return self::INVALID;
        }

        if (!Storage::deleteDirectory("public/$schema")){
            $this->components->error("Error when deleting \"$schema\" folder!");
            return self::INVALID;
        }

        $this->components->task('Dropping all tables', fn () => $this->callSilent('db:wipe', array_filter([
                '--drop-views' => $this->option('drop-views'),
                '--drop-types' => $this->option('drop-types'),
                '--force' => true,
            ])) == 0);

        $this->newLine();

        $this->call('migrate', array_filter([
            '--path' => $this->input->getOption('path'),
            '--realpath' => $this->input->getOption('realpath'),
            '--schema-path' => $this->input->getOption('schema-path'),
            '--force' => true,
            '--step' => $this->option('step'),
        ]));

        Event::dispatch(new DatabaseRefreshed);

        if ($this->needsSeeding()) {
            $this->runSeeder();
        }

        return self::SUCCESS;
    }

    /**
     * Determine if the developer has requested database seeding.
     *
     * @return bool
     */
    protected function needsSeeding(): bool
    {
        return $this->option('seed') || $this->option('seeder');
    }

    /**
     * Run the database seeder command.
     *
     * @return void
     */
    protected function runSeeder(): void
    {
        $this->call('db:seed', array_filter([
            '--class' => $this->option('seeder') ?: 'Database\\Seeders\\DatabaseSeeder',
            '--force' => true,
        ]));
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use'],
            ['drop-views', null, InputOption::VALUE_NONE, 'Drop all tables and views'],
            ['drop-types', null, InputOption::VALUE_NONE, 'Drop all tables and types (Postgres only)'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production'],
            ['path', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'The path(s) to the migrations files to be executed'],
            ['realpath', null, InputOption::VALUE_NONE, 'Indicate any provided migration file paths are pre-resolved absolute paths'],
            ['schema-path', null, InputOption::VALUE_OPTIONAL, 'The path to a schema dump file'],
            ['seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run'],
            ['seeder', null, InputOption::VALUE_OPTIONAL, 'The class name of the root seeder'],
            ['step', null, InputOption::VALUE_NONE, 'Force the migrations to be run so they can be rolled back individually'],
        ];
    }
}
