<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Contao\Rector\Set\SetList;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withSets([SetList::CONTAO])
    ->withSkip([
        NullToStrictStringFuncCallArgRector::class,
    ])
    ->withRootFiles()
    ->withParallel()
    ->withCache(sys_get_temp_dir().'/rector/contao-global-elements')
;
