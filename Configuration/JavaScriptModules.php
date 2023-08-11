<?php
return [
    'dependencies' => ['core', 'backend'],
    'tags' => [
        'backend.contextmenu',
    ],
    'imports' => [
        '@bb/consentbanners/BackendFormHandler.js' => 'EXT:consentbanners/Resources/Public/JavaScript/BackendFormHandler.js',
        '@bb/consentbanners/BackendModalPrompts.js' => 'EXT:consentbanners/Resources/Public/JavaScript/BackendModalPrompts.js',
    ],
];