<?php

namespace Mostbyte\Multidomain\Http\Controllers;

use Illuminate\Routing\Controller;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\UrlParam;
use Mostbyte\Multidomain\Enums\SchemaMigrateEnum;
use Mostbyte\Multidomain\Http\Responses\SuccessCommandResponse;
use Symfony\Component\Process\Process;

/**
 * Контроллер для запуска миграций схем
 */
#[Group('Система')]
#[Authenticated]
class SchemaMigrateController extends Controller
{
    #[UrlParam(
        name: 'type',
        type: 'string',
        description: 'Типы команд:<br/>
        <b>schema</b> - Создаёт новую схему<br/>
        <b>rollback</b> - Удаляет схему<br/>
        <b>migrate</b> - Запускает миграции.',
        required: true,
        example: 'migrate'
    )]
    public function __invoke(SchemaMigrateEnum $type): SuccessCommandResponse
    {
        $parts = explode(' ', $type->command());

        $process = new Process(
            array_merge(['php', 'artisan'], $parts),
            base_path(),
            null,
            null,
            300
        );

        $process->run();

        return new SuccessCommandResponse(
            message: $process->getOutput() ?: $process->getErrorOutput(),
            status: $process->isSuccessful() ? 0 : 1
        );
    }
}
