<?php
namespace Bb\Consentbanners\Domain\Model;

use \TYPO3\CMS\Extbase\DomainObject\AbstractEntity;


class Consent extends AbstractEntity
{
    /**
     * identification Key
     *
     * @var string
     */
    protected string $identificationKey = '';
    /**
     * pid
     *
     * @var int
     */
    protected int $pid = 0;

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

    public function getPid(): int
    {
        return $this->pid;
    }

    public function setPid(int $pid): void
    {
        $this->pid = $pid;
    }

}
