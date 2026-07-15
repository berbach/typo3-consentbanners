<?php

declare(strict_types=1);


use Bb\ConsentBanner\Domain\Model\Banner;
use Bb\ConsentBanner\Domain\Model\Component;
use Bb\ConsentBanner\Domain\Model\ConsentLog;
use Bb\ConsentBanner\Domain\Model\Cookie;
use Bb\ConsentBanner\Domain\Model\Group;

return [
    Banner::class => [
        'tableName' => 'tx_consentbanner_domain_model_banner',
    ],
    Cookie::class => [
        'tableName' => 'tx_consentbanner_domain_model_cookie',
    ],
    Group::class => [
        'tableName' => 'tx_consentbanner_domain_model_consent_groups',
    ],
    Component::class => [
        'tableName' => 'tx_consentbanner_domain_model_consent_components',
    ],
    ConsentLog::class => [
        'tableName' => 'tx_consentbanner_domain_model_consent_log',
    ],
];
