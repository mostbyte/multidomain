<?php

namespace Mostbyte\Multidomain\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\UrlParam;
use Mostbyte\Multidomain\Enums\SchemaMigrateEnum;
use Mostbyte\Multidomain\Http\Responses\SuccessCommandResponse;

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
        $exitCode = Artisan::call($type->command());

        return new SuccessCommandResponse(
            message: Artisan::output(),
            status: $exitCode
        );
    }
}
