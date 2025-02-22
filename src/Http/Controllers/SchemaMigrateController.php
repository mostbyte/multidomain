<?php

namespace Mostbyte\Multidomain\Http\Controllers;

use Illuminate\Routing\Controller;
use Mostbyte\Multidomain\Enums\SchemaMigrateEnum;
use Mostbyte\Multidomain\Http\Responses\SuccessCommandResponse;
use Illuminate\Support\Facades\Artisan;

/**
 * @group Система
 * @authenticated
 */
class SchemaMigrateController extends Controller
{
    /**
     * Запускает миграции
     *
     * @urlParam type string required Типы команд:<br/>
     * <b>schema</b> - Создаёт новую схему<br/>
     * <b>migrate</b> - Запускает миграции.<br/>
     * Example: migrate
     * @param SchemaMigrateEnum $type
     * @return SuccessCommandResponse
     */
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