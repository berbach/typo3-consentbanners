<?php

declare(strict_types=1);


use Bb\Consentbanners\Domain\Model\Consent;

return [
    Consent::class => [
        'tableName' => 'tx_yourextension_domain_model_yourmodel',
        'properties' => [
            'identificationKey' => [
                'fieldName' => 'identification_key',
            ],
            'pid' => [ // Hinzugefügt: Mapping für pid
                'fieldName' => 'pid',
            ]
        ],
        'primaryKey' => [
            'identification_key' // Bleibt dein eigener Schlüssel
        ],
    ],
];
