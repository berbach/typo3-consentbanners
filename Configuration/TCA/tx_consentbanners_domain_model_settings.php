<?php

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

return [
    'ctrl' => [
        'title' => 'Consent Banner',
        'label' => 'title',
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
                    --palette--;;content,
                    --palette--;;settings,
                --div--;LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:tab.buttons,
                    --palette--;LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:palette.buttonsDisplayName;buttonsDisplayName,
                    --palette--;LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:palette.privacyPage;privacyPage,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
                    categories,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,    
                    --palette--;;language,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    --palette--;;hidden,                
                '
        ]
    ],
    'palettes' => [
        'content' => [
            'showitem' => '
                    title,
                --linebreak--,
                    description
            ',
        ],
        'settings' => [
            'showitem' => '
                   layout_type, confirm_duration, show_categories
            ',
        ],
        'language' => [
            'showitem' => '
                sys_language_uid;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:sys_language_uid_formlabel,
                l10n_parent
            ',
        ],
        'buttonsDisplayName' => [
            'showitem' => '
                accept_all,
                confirm_selection,
                save_and_close,
                advanced_settings,
                reject
            ',
        ],
        'privacyPage' => [
            'showitem' => '
                privacy_page,
                    --linebreak--,
                privacy_page_label
            ',
        ],
        'hidden' => [
            'showitem' => '
                hidden;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:field.default.hidden
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
                'foreign_table' => 'tx_consentbanners_domain_model_settings',
                'foreign_table_where' => 'AND {#tx_consentbanners_domain_model_settings}.{#pid}=###CURRENT_PID### AND {#tx_consentbanners_domain_model_settings}.{#sys_language_uid} IN (-1,0)',
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

        'title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.title',
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
                'cols' => 100,
                'rows' => 10
            ]
        ],

        'layout_type' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.layoutType',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.layoutType.overlay', 'bb-cb-overlay'],
                    ['LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.layoutType.fullWidthBottom', 'bb-cb-bottom']
                ],
                'default' => 'bb-cb-overlay',
            ],
        ],

        'privacy_page' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.privacy_page',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'pages',
                'maxitems' => 1,
                'minitems' => 0,
                'size' => 1,
                'default' => 0,
                'suggestOptions' => [
                    'default' => [
                        'additionalSearchFields' => 'title, nav_title, slug, subtitle',
                        'addWhere' => 'AND pages.doktype = 1'
                    ]
                ],
                'fieldControl' => [
                    'elementBrowser' => [
                        'disabled' => false,
                    ],
                ],
            ]
        ],

        'confirm_duration' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.confirm_duration',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 20,
                'items' => [
                    ['LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.confirm_duration.l10days', 10],
                    ['LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.confirm_duration.l20days', 20],
                    ['LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.confirm_duration.l30days', 30],
                ],
            ],
        ],

        'privacy_page_label' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.privacy_page_label',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim'
            ],
        ],

        'accept_all' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.accept_all',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'confirm_selection' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.confirm_selection',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'save_and_close' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.save_and_close',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'advanced_settings' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.advanced_settings',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'reject' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.reject',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'show_categories' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.show_categories',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                    ],
                ],
                'eval' => 'maximumRecordsChecked',
                'validation' => [
                    'maximumRecordsChecked' => 2,
                    'maximumRecordsCheckedInPid' => 1
                ],

            ]
        ],

        'categories' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:field.categories',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_consentbanners_domain_model_category',
                'MM' => 'tx_consentbanners_categories_banner_mm',
                'foreign_table_where' => ' AND {#tx_consentbanners_domain_model_category}.{#sys_language_uid} IN (-1,0)',
                'size' => 5,
                'maxitems' => 5,
                'autoSizeMax' => 20,
                'fieldControl' => [
                    'editPopup' => [
                        'disabled' => false,
                    ],
                    'addRecord' => [
                        'disabled' => false,
                    ],
                    'listModule' => [
                        'disabled' => false,
                    ],
                ],
            ],
        ]
    ],
];