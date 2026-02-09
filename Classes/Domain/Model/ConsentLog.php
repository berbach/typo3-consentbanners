<?php
namespace Bb\ConsentBanner\Domain\Model;

use \TYPO3\CMS\Extbase\DomainObject\AbstractEntity;


class ConsentLog extends AbstractEntity
{
    /**
     * identification Key
     *
     * @var string
     */
    protected string $identificationKey = '';

    protected int $bannerVersion = 0;

    protected array $consentServices = [];




    /**
     * __construct
     */
    public function __construct() {
        //Do not remove the next line: It would break the functionality

    }
    public function getIdentificationKey(): string
    {
        return $this->identificationKey;
    }

    public function setIdentificationKey(string $identificationKey): void
    {
        $this->identificationKey = $identificationKey;
    }



}
