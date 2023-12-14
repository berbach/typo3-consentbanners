<?php
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3_MODE') || die();

call_user_func(static function () {
    TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        'consentbanners',
        'Configuration/TypoScript/',
        'Consent Banner'
    );
});