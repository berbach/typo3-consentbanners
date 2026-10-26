<?php

return [
    'ctrl' => [
        'title' => 'Consent Banner Module',
        'label' => 'name',
        'sortby' => 'sorting',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'adminOnly' => true,
        'versioningWS' => false,
        'hideAtCopy' => true,
        'typeicon_classes' => [
            'default' => 'module-cookie'
        ],
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'transOrigPointerField' => 'l10n_parent',
        'languageField' => 'sys_language_uid',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'copyAfterDuplFields' => 'sys_language_uid',
        'useColumnsForDefaultValues' => 'sys_language_uid',
        'translationSource' => 'l10n_source',
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --div--;core.form.tabs:general,
                    --palette--;;header,
                    --palette--;;content,
                --div--;LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:tab.javascript,
                    --palette--;;javascript,
                --div--;LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.placeholder,
                    --palette--;;placeholder,
                --div--;core.form.tabs:language,
                    --palette--;;language,
                --div--;core.form.tabs:access,
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
                sys_language_uid,
                l10n_parent
            ',
        ],
        'hidden' => [
            'showitem' => '
                hidden
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
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    ['label' => '', 'value' => 0],
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
                        'label' => '',
                        'invertStateDisplay' => true,
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
                'eval' => 'trim',
                'required' => true,
                'searchable' => false
            ],
        ],

        'description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.description',
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
                'cols' => 50,
                'rows' => 10,
                'searchable' => false
            ]
        ],

        'module_target' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.target',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'items' => [],
                'itemsProcFunc' => Bb\Consentbanners\Utility\TCASelectItemUtility::class . '->getAllContentElements',
            ]
        ],

        'placeholder_headline' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.placeholder_headline',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim',
                'searchable' => false
            ],
        ],

        'placeholder' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.placeholder',
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
                'rows' => 5,
                'searchable' => false
            ]
        ],

        'accepted_script' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.accepted_script',
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
                'cols' => 80,
                'rows' => 20,
                'searchable' => false
            ]
        ],

        'rejected_script' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.rejected_script',
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
                'cols' => 80,
                'rows' => 20,
                'searchable' => false
            ]
        ],
    ],
];
