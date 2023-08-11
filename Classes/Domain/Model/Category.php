<?php

namespace Bb\Consentbanners\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Category extends AbstractEntity
{
    /**
     * name
     *
     * @var string
     */
    protected string $name = '';
    /**
     * show uri
     *
     * @var string
     */
    protected string $showUri = '';
    /**
     * description
     *
     * @var string
     */
    protected string $description = '';
    /**
     * The categories the offer is assigned to
     *
     * @var ObjectStorage<Module>
     */
    protected ObjectStorage $modules;
    /**
     * locked_and_active
     *
     * @var int
     */
    protected int $lockedAndActive;
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
     * Returns the locked And Active
     *
     * @return int
     */
    public function getLockedAndActive():int
    {
        return $this->lockedAndActive;
    }
    /**
     * Sets the locked And Active
     *
     * @param int $lockedAndActive
     * @return void
     */
    public function setLockedAndActive(int $lockedAndActive):void
    {
        $this->lockedAndActive = $lockedAndActive;
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
     * Returns the description
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
    /**
     * Adds a module
     *
     * @param Module $module
     */
    public function addModules(Module $module): void
    {
        $this->modules->attach($module);
    }

    /**
     * Removes a Module
     *
     * @param Module $moduleToRemove The Module to be removed
     */
    public function removeModules(Module $moduleToRemove): void
    {
        $this->modules->detach($moduleToRemove);
    }

    /**
     * Returns the modules
     *
     * @return ObjectStorage<Module> $modules
     */
    public function getModules(): ObjectStorage
    {
        return $this->modules;
    }

    /**
     * Sets the module
     *
     * @param ObjectStorage<Module> $modules
     */
    public function setModules(ObjectStorage $modules): void
    {
        $this->modules = $modules;
    }
}