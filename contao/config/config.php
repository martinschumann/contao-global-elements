<?php

/*
 * This file is part of contao-garage/contao-global-elements.
 *
 * @author    Martin Schumann <martin.schumann@ontao-garage.de>
 * @license   LGPL-3.0-or-later
 * @copyright Contao Garage 2026
 */

use Contao\ArrayUtil;
use Contao\System;
use Symfony\Component\HttpFoundation\Request;

// Back end modules
ArrayUtil::arrayInsert($GLOBALS['BE_MOD']['content'], (array_search('article', array_keys($GLOBALS['BE_MOD']['content'])) + 1), [
    'global_elements' => [
        'tables' => [
            'cg_global_elements_archive',
            'tl_content',
        ],
    ],
]);
