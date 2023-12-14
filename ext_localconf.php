<?php
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(static function () {
    // Add module configuration
    ExtensionManagementUtility::addTypoScriptSetup(
        '
        module.tx_consentbanners {
            settings {
                storagePid = 999
            }
            view {
                templateRootPaths.0 = EXT:consentbanners/Resources/Private/Backend/Templates/
                partialRootPaths.0 = EXT:consentbanners/Resources/Private/Backend/Partials/
                layoutRootPaths.0 = EXT:consentbanners/Resources/Private/Backend/Layouts/
            }
        }'
    );

    $iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);
    $iconRegistry->registerIcon(
        'module-cookie',
        SvgIconProvider::class,
        ['source' => 'EXT:consentbanners/Resources/Public/Icons/module_icon.svg']
    );
});