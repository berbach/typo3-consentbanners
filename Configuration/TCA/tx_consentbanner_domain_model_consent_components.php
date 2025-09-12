<?php

return [
    'ctrl' => [
        'title' => 'Consent components',
        'label' => 'component_title',
        'sortby' => 'sorting',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
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
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    --palette--;;header,
                    --palette--;;content,
                --div--;LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:tab.javascript,
                    --palette--;;javascript,
                --div--;LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.placeholder,
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
                component_title,
            ',
        ],
        'content' => [
            'showitem' => '
                component_description,
                --linebreak--,
                module_target
            ',
        ],
        'placeholder' => [
            'showitem' => '
                placeholder_title,
                --linebreak--,
                placeholder_description,
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
                'foreign_table' => 'tx_consentbanner_domain_model_consent_components',
                'foreign_table_where' => 'AND {#tx_consentbanner_domain_model_consent_components}.{#pid}=###CURRENT_PID### AND {#tx_consentbanner_domain_model_consent_components}.{#sys_language_uid} IN (-1,0)',
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

        'component_title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.name',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim',
                'required' => true
            ],
        ],

        'component_description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.description',
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
                'cols' => 50,
                'rows' => 10
            ]
        ],

        'module_target' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.target',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'items' => [],
                'itemsProcFunc' => Bb\Consentbanners\Utility\TCASelectItemUtility::class . '->getAllContentElements',
            ]
        ],

        'placeholder_title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.placeholder_headline',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim'
            ],
        ],

        'placeholder_description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.placeholder',
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
                'rows' => 5
            ]
        ],

        'accepted_script' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.accepted_script',
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
                'cols' => 80,
                'rows' => 20
            ]
        ],

        'rejected_script' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.rejected_script',
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
                'cols' => 80,
                'rows' => 20
            ]
        ],

        'group_id' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
