<?php
defined('TYPO3') || die();


\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule(
    $GLOBALS['TCA']['tt_content'],
    [
        'columns' => [
            'ce_consent_component' => [
                'exclude' => true,
                'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_db.xlf:field.consentbanner.component',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'items' => [
                        ['label' => 'Keine Zuordnung', 'value' => '']
                    ],
                    'itemsProcFunc' => Bb\ConsentBanner\Utility\TCASelectModuleUtility::class . '->getHtmlModules',
                ],
            ],
        ]
    ]
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '
    --div--;LLL:EXT:consent_banner/Resources/Private/Language/locallang_db.xlf:tab.consentbanner;,
    --palette--;;ceConsentSettings,',
    'html',
    'after:bodytext'
);

// Felder einer neuen Palette hinzufügen
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tt_content',
    'ceConsentSettings',
    'ce_consent_component'
);
