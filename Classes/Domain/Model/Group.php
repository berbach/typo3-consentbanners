<?php

namespace Bb\ConsentBanner\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Group extends AbstractEntity
{
    /**
     * name
     *
     * @var string
     */
    protected string $groupTitle = '';
    /**
     * description
     *
     * @var string
     */
    protected string $groupDescription = '';
    /**
     * show uri
     *
     * @var string
     */
    protected string $showUri = '';
    /**
     * The categories the offer is assigned to
     *
     * @var ObjectStorage<Component>
     */
    protected ObjectStorage $groupComponents;
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
        $this->initializeObject();
    }
    /**
     * Called again with initialize object, as fetching an entity from the DB does not use the constructor
     */
    public function initializeObject(): void
    {
        $this->GroupComponents ??= new ObjectStorage();
    }
    /**
     * Returns the name
     *
     * @return string $groupTitle
     */
    public function getGroupTitle(): string
    {
        return $this->groupTitle;
    }
    /**
     * Sets the name
     *
     * @param string $groupTitle
     * @return void
     */
    public function setGroupTitle(string $groupTitle): void
    {
        $this->groupTitle = $groupTitle;
    }
    /**
     * Returns the description
     *
     * @return string $groupDescription
     */
    public function getGroupDescription(): string
    {
        return $this->groupDescription;
    }
    /**
     * Sets the description
     *
     * @param string $groupDescription
     * @return void
     */
    public function setGroupDescription(string $groupDescription): void
    {
        $this->groupDescription = $groupDescription;
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
     * @param Component $component
     */
    public function addGroupComponent(Component $component): void
    {
        $this->groupComponents->attach($component);
    }

    /**
     * Removes a component
     *
     * @param Component $component The component to be removed
     */
    public function removeGroupComponent(Component $component): void
    {
        $this->groupComponents->detach($component);
    }

    /**
     * Returns the components
     *
     * @return ObjectStorage<Component> $groupComponents
     */
    public function getGroupComponents(): ObjectStorage
    {
        return $this->groupComponents;
    }

    /**
     * Sets the components
     *
     * @param ObjectStorage<Component> $groupComponents
     */
    public function setGroupComponents(ObjectStorage $groupComponents): void
    {
        $this->groupComponents = $groupComponents;
    }
}