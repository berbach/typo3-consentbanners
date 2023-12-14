<?php
return [
    'ctrl' => [
        'title' => 'Consent Banner Module',
        'label' => 'name',
        'sortby' => 'sorting',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'adminOnly' => true,
        'versioningWS' => false,
        'hideAtCopy' => true,
        'searchFields' => '',
        'typeicon_classes' => [
            'default' => 'module-cookie'
        ],
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'copyAfterDuplFields' => 'sys_language_uid',
        'useColumnsForDefaultValues' => 'sys_language_uid',
        'translationSource' => 'l10n_source'
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    --palette--;;header,
                    --palette--;;content,
                --div--;LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:tab.javascript,
                    --palette--;;javascript,
                --div--;LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.placeholder,
                    --palette--;;placeholder,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                    --palette--;;language,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    --palette--;;hidden,    
                '
        ]
    ],
    'palettes' => [
        'header' => [
            'showitem' => '
                name,
            ',
        ],
        'content' => [
            'showitem' => '
                description,
                --linebreak--,
                module_target
            ',
        ],
        'placeholder' => [
            'showitem' => '
                placeholder_headline,
                --linebreak--,
                placeholder,
            ',
        ],
        'language' => [
            'showitem' => '
                sys_language_uid;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:sys_language_uid_formlabel,
                l10n_parent;LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent
            ',
        ],
        'hidden' => [
            'showitem' => '
                hidden;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:field.default.hidden
            ',
        ],
        'javascript' => [
            'showitem' => '
                    accepted_script,
                --linebreak--,
                    rejected_script
            ',
        ],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_consentbanners_domain_model_module',
                'foreign_table_where' => 'AND {#tx_consentbanners_domain_model_module}.{#pid}=###CURRENT_PID### AND {#tx_consentbanners_domain_model_module}.{#sys_language_uid} IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],

        'l10n_source' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],

        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true
                    ]
                ],
            ],
        ],

        'name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.name',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim,required'
            ],
        ],

        'description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.description',
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
                'cols' => 50,
                'rows' => 10
            ]
        ],

        'module_target' => [
            'exclude' => false,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.target',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'items' => [['Content Element']],
                'itemsProcFunc' => Bb\Consentbanners\Utility\TCASelectItemUtility::class . '->getAllContentElements',
            ]
        ],

        'placeholder_headline' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.placeholder_headline',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim'
            ],
        ],

        'placeholder' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.placeholder',
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
                'rows' => 5
            ]
        ],

        'accepted_script' => [
            'exclude' => false,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.accepted_script',
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
                'cols' => 80,
                'rows' => 20
            ]
        ],

        'rejected_script' => [
            'exclude' => false,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.rejected_script',
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
                'cols' => 80,
                'rows' => 20
            ]
        ],
    ],
];
