<?php

use Bb\ConsentBanner\Middleware\ConsentMiddleware;

return [
    'frontend' => [
        'consent-banner' => [
            'target' => ConsentMiddleware::class,
            'after' => [
                'typo3/cms-frontend/site'
            ],
            'before' => [
                'typo3/cms-frontend/authentication'
            ]
        ]
    ]
];
