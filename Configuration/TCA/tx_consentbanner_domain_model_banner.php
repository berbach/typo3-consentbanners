<?php

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

return [
    'ctrl' => [
        'title' => 'Consent Banner',
        'label' => 'title',
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
                --div--;LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:tab.banner,
                    --palette--;;banner,
                    --palette--;;linksBannerMenu
                --div--;LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:tab.text_for_labels,
                    --palette--;;buttonsLabelText,
                    --palette--;;cookieInfoLabels,
                    --palette--;;cookieMoreInfoLabels,
                --div--;LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:tab.essential_group,
                    --palette--;;essential,
                    --palette--;;essentialOptIns,
                --div--;LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:tab.other_group,
                    --palette--;;otherGroup,
                --div--;LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:tab.banner_setting,
                    --palette--;;hidden,
                    --palette--;;cookieLifeTime,
                    --palette--;;language,
                '
        ]
    ],
    'palettes' => [
        'banner' => [
            'showitem' => '
                banner_title,
                    --linebreak--,
                banner_description,
                    --linebreak--, 
                banner_layout, is_text_link
            ',
        ],
        'cookieLifeTime' => [
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:palette.cookieLifeTime',
            'showitem' => '
                lifetime_banner, 
                    --linebreak--,
                lifetime_user_consent
            ',
        ],

        'buttonsLabelText' => [
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:palette.buttonsLabelText',
            'showitem' => '
                accept_all_text,
                confirm_selection_text,
                    --linebreak--, 
                save_and_close_text,
                advanced_settings_text,
                    --linebreak--, 
                accept_essential_text
            ',
        ],

        'cookieInfoLabels' => [
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:palette.cookieInfoLabels',
            'showitem' => '
                cookie_name_text,
                cookie_lifetime_text,
                    --linebreak--, 
                cookie_provider_text,
                cookie_purpose_text,
                    --linebreak--, 
                cookie_description_text
            ',
        ],

        'cookieMoreInfoLabels' => [
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:palette.cookieMoreInfoLabels',
            'showitem' => '
                cookie_infos_show_text,
                cookie_infos_close_text,
            ',
        ],

        'linksBannerMenu' => [
            'showitem' => '
                privacy_link,
                    --linebreak--,
                imprint_link
            ',
        ],
        'essential' => [
            'showitem' => '
                essential_title,
                    --linebreak--,
                essential_description,
            ',
        ],
        'essentialConsents' => [
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:palette.essentialOptIns',
            'showitem' => '
                essential_consents    
            ',
        ],
        'otherGroup' => [
            'showitem' => '
                group_consents
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
                'foreign_table' => 'tx_consentbanner_domain_model_settings',
                'foreign_table_where' => 'AND {#tx_consentbanner_domain_model_settings}.{#pid}=###CURRENT_PID### AND {#tx_consentbanner_domain_model_settings}.{#sys_language_uid} IN (-1,0)',
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

        'banner_title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.banner_title',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim',
                'required' => true
            ],
        ],


        'banner_description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.banner_description',
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
                'cols' => 100,
                'rows' => 10
            ]
        ],

        'banner_layout' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.banner_layout',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.layoutType.overlay', 'value' => 'bb-cb-overlay'],
                    ['label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.layoutType.fullWidthBottom', 'value' => 'bb-cb-bottom']
                ],
                'default' => 'bb-cb-bottom',
            ],
        ],

        'privacy_link' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.privacy_link',
            'config' => [
                'type' => 'link',
                'allowedTypes' => ['page'],
            ]
        ],

        'imprint_link' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.imprint_link',
            'config' => [
                'type' => 'link',
                'allowedTypes' => ['page'],
            ]
        ],

        'lifetime_banner' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.lifetime_banner',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 20,
                'items' => [
                    ['label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.lifetime_banner.l10days', 'value' => 10],
                    ['label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.lifetime_banner.l20days', 'value' => 20],
                    ['label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.lifetime_banner.l30days', 'value' => 30],
                ],
            ],
        ],

        'lifetime_user_consent' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.lifetime_banner',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 20,
                'items' => [
                    ['label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.lifetime_banner.l10days', 'value' => 10],
                    ['label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.lifetime_banner.l20days', 'value' => 20],
                    ['label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.lifetime_banner.l30days', 'value' => 30],
                ],
            ],
        ],

        'accept_all_text' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.accept_all_text',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'confirm_selection_text' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.confirm_selection_text',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'save_and_close_text' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.save_and_close_text',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'advanced_settings_text' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.advanced_settings_text',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'accept_essential_text' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.accept_essential_text',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'cookie_infos_show_text' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.cookie_infos_show_text',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'cookie_infos_close_text' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.reject',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'cookie_name_text' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.cookie_name_text',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'cookie_lifetime_text' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.cookie_lifetime_text',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'cookie_provider_text' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.cookie_provider_text',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'cookie_purpose_text' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.cookie_purpose_text',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'cookie_description_text' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.cookie_description_text',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],


        'essential_title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.essential_title',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim',
                'required' => true
            ],
        ],


        'essential_description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.essential_description',
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
                'cols' => 100,
                'rows' => 10
            ]
        ],

        'essential_consents' => [
            'exclude' => false,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.essential_opt_ins',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_consentbanner_domain_model_optin_module',
                'foreign_field' => 'group_id',
                'foreign_sortby' => 'sorting_foreign',
                'foreign_label' => 'name',
                'maxitems' => 5,
                'appearance' => [
                    'newRecordLinkTitle' => 'Add optin for cookie',
                    'useSortable' => true,
                    'levelLinksPosition' => 'top',
                    'enabledControls' => ['info' => false],
                    'collapseAll' => 1,
                    'expandSingle' => 1,
                ],
            ],
        ],

        'group_consents' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.group_consents',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_consentbanner_domain_model_group_category',
                'foreign_field' => 'banner_id',
                'foreign_sortby' => 'sorting_foreign',
                'foreign_label' => 'group_title',
                'maxitems' => 5,
                'appearance' => [
                    'newRecordLinkTitle' => 'Add group consents',
                    'useSortable' => true,
                    'levelLinksPosition' => 'top',
                    'enabledControls' => ['info' => false],
                    'collapseAll' => 1,
                    'expandSingle' => 1,
                ],
            ],
        ],

        'is_text_link' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.is_text_link',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        'label' =>  '',
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
    ],
];