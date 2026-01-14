<?php
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use Bb\ConsentBanner\Controller\AjaxController;

return [
    'consent-write' => [
        'path' => '/ajax/consent/write',
        'target' => AjaxController::class . '::writeAction',
        'methods' => ['POST'],
    ],
    'consent-read' => [
        'path' => '/ajax/consent/read/{identificationKey}',
        'target' => AjaxController::class . '::readAction',
        'methods' => ['GET'],
    ],
];