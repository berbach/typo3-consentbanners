<?php
return [
    'dependencies' => ['core', 'backend'],
    'tags' => [
        'backend.contextmenu',
    ],
    'imports' => [
        '@bb/consentbanner/BackendFormHandler.js' => 'EXT:consent_banner/Resources/Public/JavaScript/BackendFormHandler.js',
        '@bb/consentbanner/BackendModalPrompts.js' => 'EXT:consent_banner/Resources/Public/JavaScript/BackendModalPrompts.js',
    ],
];