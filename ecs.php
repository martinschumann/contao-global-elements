<?php

declare(strict_types=1);

use Contao\EasyCodingStandard\Set\SetList;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use PhpCsFixer\Fixer\ReturnNotation\NoUselessReturnFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoWhitespaceBeforeCommaInArrayFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocAddMissingParamAnnotationFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocOrderFixer;
use PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer;
use PhpCsFixer\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;

$header = <<<EOF
This file is part of contao-garage/contao-global-elements.

@author    Martin Schumann <martin.schumann@ontao-garage.de>
@license   MIT
@copyright Contao Garage %s
EOF;
$header = sprintf($header, date('Y'));

return ECSConfig::configure()
    ->withSets([SetList::CONTAO])
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withConfiguredRule(HeaderCommentFixer::class, ['header' => "$header"])
    ->withConfiguredRule(BlankLineBeforeStatementFixer::class, ['statements' => [
        'do',
        'exit',
        'for',
        'foreach',
        'if',
        'return',
        'switch',
        'try',
        'while',
    ]])
    ->withConfiguredRule(MultilineWhitespaceBeforeSemicolonsFixer::class, ['strategy' => 'new_line_for_chained_calls'])
    ->withRules(
        [NotOperatorWithSuccessorSpaceFixer::class],
        [ConcatSpaceFixer::class],
        [NoUselessReturnFixerTest::class],
        [NoWhitespaceBeforeCommaInArrayFixerTest::class],
        [PhpdocAddMissingParamAnnotationFixer::class],
        [PhpdocOrderFixer::class],
    )
;
