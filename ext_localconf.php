<?php
declare(strict_types=1);

defined('TYPO3') || die('Access denied.');

use Bb\ConsentBanner\Controller\AjaxController;
use Bb\ConsentBanner\Hook\DataHandlerHook;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

call_user_func(static function () {

    ExtensionUtility::configurePlugin(
        'ConsentBanner',
        'Consent',
        [
            AjaxController::class => 'main'
        ],
        // non-cacheable actions
        [
            AjaxController::class => 'main'
        ],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

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

    \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule(
        $GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'],
        [
            'type',
            'hook',
        ]
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['banner']
        = DataHandlerHook::class;
});