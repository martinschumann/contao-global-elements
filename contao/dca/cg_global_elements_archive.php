<?php

use Contao\DC_Table;

// Table cg_global_elements_archive
$GLOBALS['TL_DCA']['cg_global_elements_archive'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'ctable' => ['tl_content'],
        'switchToEdit' => true,
        'enableVersioning' => true,
        'onload_callback' => [
            // ['cg_global_elements_archive', 'checkPermission'],
        ],
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'title' => 'index',
            ],
        ],
    ],
    // List
    'list' => [
        'sorting' => [
            'mode' => 1,
            'fields' => ['title'],
            'headerFields' => ['title', 'description'],
            'panelLayout' => 'search,limit',
            'flag' => 1,
            'disableGrouping' => true,
        ],
        'label' => [
            'fields' => ['title', 'description'],
            'showColumns' => true,
        ],
    ],
    // Palettes
    'palettes' => [
        'default' => '{title_legend},title,description;',
    ],
    // Fields
    'fields' => [
        'id' => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp' => [
            'sql' => [
                'type' => 'integer',
                'unsigned' => true,
                'notnull' => true,
                'default' => 0,
            ],
        ],
        'title' => [
            'label' => &$GLOBALS['TL_LANG']['cg_global_elements_archive']['title'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => [
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class' => 'w50',
            ],
            'sql' => [
                'type' => 'string',
                'length' => 255,
                'notnull' => true,
                'default' => '',
            ],
        ],
        'description' => [
            'label' => &$GLOBALS['TL_LANG']['cg_global_elements_archive']['description'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'textarea',
            'eval' => [
                'mandatory' => false,
                'maxlength' => 1024,
                'tl_class' => 'clr w50',
            ],
            'sql' => [
                'type' => 'text',
                'length' => 65535,
                'notnull' => true,
                'default' => '',
            ],
        ],
    ],
];
