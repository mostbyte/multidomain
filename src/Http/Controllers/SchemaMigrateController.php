<?php

namespace Mostbyte\Multidomain\Http\Controllers;

use Artisan;
use Illuminate\Routing\Controller;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
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
        description: "Типы команд:<br/>
        <b>schema</b> - Создаёт новую схему<br/>
        <b>migrate</b> - Запускает миграции.",
        required: true,
        example: 'migrate'
    )]
    #[ResponseFromApiResource(SuccessCommandResponse::class, description: 'Успешный запуск команды миграции')]
    public function __invoke(SchemaMigrateEnum $type): SuccessCommandResponse
    {
        $command = $type->command();

        $code = Artisan::call($command);

        return new SuccessCommandResponse(
            message: Artisan::output(),
            status: $code
        );
    }
}
