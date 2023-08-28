<?php
defined('TYPO3') || die();

$GLOBALS['TCA']['tt_content']['columns'] = array_replace_recursive(
    $GLOBALS['TCA']['tt_content']['columns'],
    [
        'ce_consent_module' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_db.xlf:field.consentbanner.module',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'Select a module', 'value' => '']
                ],
                'itemsProcFunc' => Bb\Consentbanners\Utility\TCASelectModuleUtility::class . '->getHtmlModules',
            ],
        ],
    ]
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '
    --div--;LLL:EXT:consentbanners/Resources/Private/Language/locallang_db.xlf:tab.consentbanner;,
    --palette--;;ceConsentSettings,',
    'html',
    'after:bodytext'
);

// Felder einer neuen Palette hinzufügen
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tt_content',
    'ceConsentSettings',
    'ce_consent_module'
);
