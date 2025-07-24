<?php

use Bb\ConsentBanner\Controller\ManagementController;
/**
 * Definitions for modules provided by EXT:examples
 */
return [
    'consentbanner_management' => [
        'parent' => 'site',
        'position' => ['after' => 'web_ts'],
        'access' => 'admin',
        'workspaces' => 'live',
        'path' => '/module/site/consent_banner',
        'labels' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:module.label',
        'extensionName' => 'ConsentBanner',
        'iconIdentifier' => 'module-cookie',
        'controllerActions' => [
            ManagementController::class => [
                'settings', 'consents', 'delete'
            ],
        ],
    ],
];