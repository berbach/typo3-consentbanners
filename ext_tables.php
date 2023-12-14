<?php
// all use statements must come first
use Bb\Consentbanners\Controller\BackendConsentbannerController;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3_MODE') || die('Access denied.');

call_user_func(static function () {

    ExtensionUtility::registerModule(
        'Consentbanners',
        'site',
        'Management',
        'after:configuration',
        [
            BackendConsentbannerController::class => 'settings, categories, modules, delete'
        ],
        [
            'access' => 'admin',
            'iconIdentifier' => 'module-cookie',
            'labels' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:module.label',
        ]
    );

    ExtensionManagementUtility::allowTableOnStandardPages('tx_consentbanners_domain_model_settings');
    ExtensionManagementUtility::allowTableOnStandardPages('tx_consentbanners_domain_model_category');
    ExtensionManagementUtility::allowTableOnStandardPages('tx_consentbanners_domain_model_module');
});

