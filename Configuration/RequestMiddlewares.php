<?php

use Bb\ConsentBanner\Middleware\ConsentMiddleware;

return [
    'frontend' => [
        'consent-banner' => [
            'target' => ConsentMiddleware::class,
            'before' => [
                'typo3/cms-frontend/authentication'
            ]
        ]
    ]
];
