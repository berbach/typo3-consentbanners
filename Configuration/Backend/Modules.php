<?php

use Bb\Consentbanners\Controller\BackendConsentbannerController;
/**
 * Definitions for modules provided by EXT:examples
 */
return [
    'site_consentbanners' => [
        'parent' => 'site',
        'position' => ['top'],
        'access' => 'admin',
        'workspaces' => 'live',
        'path' => '/module/site/consentbanners',
        'aliases' => ['site_ConsentbannersManagement'],
        'labels' => 'LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:module.label',
        'extensionName' => 'Consentbanners',
        'iconIdentifier' => 'module-cookie',
        'controllerActions' => [
            BackendConsentbannerController::class => [
                'settings', 'categories', 'modules', 'delete'
            ],
        ],
    ],
];