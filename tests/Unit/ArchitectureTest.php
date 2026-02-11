<?php

arch('commands extend Command')
    ->expect('Mostbyte\Multidomain\Commands')
    ->toExtend('Illuminate\Console\Command');

arch('controllers extend Controller')
    ->expect('Mostbyte\Multidomain\Http\Controllers')
    ->toExtend('Illuminate\Routing\Controller');

arch('facades extend Facade')
    ->expect('Mostbyte\Multidomain\Facades')
    ->toExtend('Illuminate\Support\Facades\Facade');

arch('enums are enums')
    ->expect('Mostbyte\Multidomain\Enums')
    ->toBeEnums();

arch('no debugging statements')
    ->expect('Mostbyte\Multidomain')
    ->not->toUse(['dd', 'dump', 'ray', 'var_dump']);
