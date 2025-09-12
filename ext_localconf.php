<?php
declare(strict_types=1);

defined('TYPO3') || die('Access denied.');

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

call_user_func(static function () {
    // Add module configuration
    ExtensionManagementUtility::addTypoScriptSetup(
        'module.tx_consent_banner {
            settings {
                storagePid = 999
            }
            view {
                templateRootPaths.0 = EXT:consent_banner/Resources/Private/Backend/Templates/
                partialRootPaths.0 = EXT:consent_banner/Resources/Private/Backend/Partials/
                layoutRootPaths.0 = EXT:consent_banner/Resources/Private/Backend/Layouts/
            }
        }'
    );

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry']['1757502493'] = [
        'nodeName' => 'inputCurrentLanguagePlaceholder',
        'priority' => 40,
        'class' => \Bb\ConsentBanner\Form\Element\InputCurrentLanguagePlaceholder::class,
    ];
});