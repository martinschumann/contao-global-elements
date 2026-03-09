<?php

declare(strict_types=1);

use Contao\EasyCodingStandard\Set\SetList;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

$header = <<<EOF
This file is part of contao-garage/contao-global-elements.

@author    Martin Schumann <martin.schumann@ontao-garage.de>
@license   LGPL-3.0-or-later
@copyright Contao Garage %s
EOF;
$header = sprintf($header, date('Y'));

return ECSConfig::configure()
    ->withSets([SetList::CONTAO])
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withConfiguredRule(HeaderCommentFixer::class, ['header' => "$header"])
;
