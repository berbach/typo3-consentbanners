<?php
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

call_user_func(static function () {
    TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        'consent_banner',
        'Configuration/TypoScript/',
        'Consent Banner'
    );
});