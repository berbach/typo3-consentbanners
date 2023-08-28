<?php
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
defined('TYPO3') || die('Access denied.');

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

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod{
            web_list {
                allowedNewTables := addToList(tx_consentbanners_domain_model_settings,tx_consentbanners_domain_model_category,tx_consentbanners_domain_model_module)
            }
        }
        '
    );
});