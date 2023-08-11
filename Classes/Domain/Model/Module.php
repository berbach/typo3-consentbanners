<?php

namespace Bb\Consentbanners\Domain\Model;

use \TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;


class Module extends AbstractEntity
{
    /**
     * pid
     *
     * @var int
     */
    protected $pid;
    /**
     * name
     *
     * @var string
     */
    protected string $name = '';
    /**
     * description
     *
     * @var string
     */
    protected string $description = '';
    /**
     * rejected_script
     *
     * @var string
     */
    protected string $rejectedScript= '';
    /**
     * accepted_script
     *
     * @var string
     */
    protected string $acceptedScript = '';
    /**
     * target
     *
     * @var string
     */
    protected string $moduleTarget = '';
    /**
     * target
     *
     * @var string
     */
    protected string $placeholder = '';
    /**
     * show uri
     *
     * @var string
     */
    protected string $showUri = '';
    /**
     * hidden
     *
     * @var int
     */
    protected int $hidden;
    /**
     * deleted
     *
     * @var int
     */
    protected int $deleted;
    /**
     * __construct
     */
    public function __construct() {
        //Do not remove the next line: It would break the functionality

    }

    /**
     * Returns the pid
     *
     * @return int $pid
     */
    public function getPid(): int
    {
        return $this->pid;
    }

    /**
     * Returns the name
     *
     * @return string $name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the name
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Returns the rejected Script
     *
     * @return string $rejectedScript
     */
    public function getRejectedScript(): string
    {
        return $this->rejectedScript;
    }

    /**
     * Sets the rejected Script
     *
     * @param string $rejectedScript
     * @return void
     */
    public function setRejectedScript(string $rejectedScript): void
    {
        $this->rejectedScript = $rejectedScript;
    }

    /**
     * Returns the accepted Script
     *
     * @return string $acceptedScript
     */
    public function getAcceptedScript(): string
    {
        return $this->acceptedScript;
    }

    /**
     * Sets the accepted Script
     *
     * @param string $acceptedScript
     * @return void
     */
    public function setAcceptedScript(string $acceptedScript): void
    {
        $this->acceptedScript = $acceptedScript;
    }

    /**
     * Returns the description
     *
     * @return string $description
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Sets the description
     *
     * @param string $description
     * @return void
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Returns the target
     *
     * @return string $moduleTarget
     */
    public function getModuleTarget(): string
    {
        return $this->moduleTarget;
    }

    /**
     * Sets the target
     *
     * @param string $moduleTarget
     * @return void
     */
    public function setModuleTarget(string $moduleTarget): void
    {
        $this->moduleTarget = $moduleTarget;
    }

    /**
     * Returns the placeholder
     *
     * @return string $placeholder
     */
    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    /**
     * Sets the placeholder
     *
     * @param string $placeholder
     * @return void
     */
    public function setPlaceholder(string $placeholder): void
    {
        $this->placeholder = $placeholder;
    }

    /**
     * Returns the hidden
     *
     * @return int
     */
    public function getHidden():int
    {
        return $this->hidden;
    }

    /**
     * Sets the hidden
     *
     * @param int $hidden
     * @return void
     */
    public function setHidden(int $hidden):void
    {
        $this->hidden = $hidden;
    }
    /**
     * Returns the deleted
     *
     * @return int
     */
    public function getDeleted():int
    {
        return $this->deleted;
    }

    /**
     * Sets the deleted
     *
     * @param int $deleted
     * @return void
     */
    public function setDeleted(int $deleted):void
    {
        $this->deleted = $deleted;
    }
    /**
     * Returns the show Uri
     *
     * @return string $showUri
     */
    public function getShowUri():string
    {

        return $this->showUri;
    }
    /**
     * Sets the show uri
     *
     * @param string $showUri
     * @return void
     */
    public function setShowUri(string $showUri): void
    {
        $this->showUri = $showUri;
    }
}
