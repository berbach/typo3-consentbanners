<?php

namespace Bb\Consentbanners\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Settings extends AbstractEntity
{
    /**
     * title
     *
     * @var string
     */
    protected $title = '';
    /**
     * show uri
     *
     * @var string
     */
    protected $showUri = '';
    /**
     * description
     *
     * @var string
     */
    protected $description = '';
    /**
     * accept_all
     *
     * @var string
     */
    protected $acceptAll = '';
    /**
     * confirm_selection
     *
     * @var string
     */
    protected $confirmSelection = '';
    /**
     * save_and_close
     *
     * @var string
     */
    protected $saveAndClose = '';
    /**
     * advanced_settings
     *
     * @var string
     */
    protected $advancedSettings = '';
    /**
     * privacy_page
     *
     * @var int
     */
    protected $privacyPage;
    /**
     * privacy_page_label
     *
     * @var string
     */
    protected $privacyPageLabel = '';
    /**
     * reject
     *
     * @var string
     */
    protected $reject = '';
    /**
     * show_categories
     *
     * @var int
     */
    protected $showCategories;
    /**
     * confirm_duration
     *
     * @var int
     */
    protected $confirmDuration;
    /**
     * layout_type
     *
     * @var string
     */
    protected $layoutType = '';
    /**
     * categories
     *
     * @var ObjectStorage<Category>
     */
    protected $categories;

    /**
     * __construct
     */
    public function __construct() {
        //Do not remove the next line: It would break the functionality
        $this->setCategories(new ObjectStorage);
    }
    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
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
     * Returns the layout type
     *
     * @return string $layoutType
     */
    public function getLayoutType(): string
    {
        return $this->layoutType;
    }

    /**
     * Sets the layout type
     *
     * @param string $layoutType
     * @return void
     */
    public function setLayoutType(string $layoutType): void
    {
        $this->layoutType = $layoutType;
    }

    /**
     * Returns the accept all
     *
     * @return string $layoutType
     */
    public function getAcceptAll(): string
    {
        return $this->acceptAll;
    }

    /**
     * Sets the accept all
     *
     * @param string $acceptAll
     * @return void
     */
    public function setAcceptAll(string $acceptAll): void
    {
        $this->acceptAll = $acceptAll;
    }

    /**
     * Returns the confirm_selection
     *
     * @return string $confirmSelection
     */
    public function getConfirmSelection(): string
    {
        return $this->confirmSelection;
    }

    /**
     * Sets the confirm_selection
     *
     * @param string $confirmSelection
     * @return void
     */
    public function setConfirmSelection(string $confirmSelection): void
    {
        $this->confirmSelection = $confirmSelection;
    }

    /**
     * Returns the save_and_close
     *
     * @return string $saveAndClose
     */
    public function getSaveAndClose(): string
    {
        return $this->saveAndClose;
    }

    /**
     * Sets the save_and_close
     *
     * @param string $saveAndClose
     * @return void
     */
    public function setSaveAndClose(string $saveAndClose): void
    {
        $this->saveAndClose = $saveAndClose;
    }

    /**
     * Returns the advanced Settings
     *
     * @return string $advancedSettings
     */
    public function getAdvancedSettings(): string
    {
        return $this->advancedSettings;
    }

    /**
     * Sets the advanced Settings
     *
     * @param string $advancedSettings
     * @return void
     */
    public function setAdvancedSettings(string $advancedSettings): void
    {
        $this->advancedSettings = $advancedSettings;
    }

    /**
     * Returns the privacy_page_label
     *
     * @return string $privacyPageLabel
     */
    public function getPrivacyPageLabel(): string
    {
        return $this->privacyPageLabel;
    }

    /**
     * Sets the privacy_page_label
     *
     * @param string $privacyPageLabel
     * @return void
     */
    public function setPrivacyPageLabel(string $privacyPageLabel): void
    {
        $this->privacyPageLabel = $privacyPageLabel;
    }
    /**
     * Returns the privacy_page
     *
     * @return int $privacyPage
     */
    public function getPrivacyPage(): int
    {
        return $this->privacyPage;
    }

    /**
     * Sets the privacy_page
     *
     * @param int $privacyPage
     * @return void
     */
    public function setPrivacyPage(int $privacyPage): void
    {
        $this->privacyPage = $privacyPage;
    }
    /**
     * Returns the reject
     *
     * @return string $reject
     */
    public function getReject(): string
    {
        return $this->reject;
    }

    /**
     * Sets the reject
     *
     * @param string $reject
     * @return void
     */
    public function setReject(string $reject): void
    {
        $this->reject = $reject;
    }
    /**
     * Returns the showCategories
     *
     * @return int $showCategories
     */
    public function getShowCategories(): int
    {
        return $this->showCategories;
    }

    /**
     * Sets the showCategories
     *
     * @param int $showCategories
     * @return void
     */
    public function setShowCategories(int $showCategories): void
    {
        $this->showCategories = $showCategories;
    }
    /**
     * Returns the confirm_duration
     *
     * @return int $confirmDuration
     */
    public function getConfirmDuration(): int
    {
        return $this->confirmDuration;
    }

    /**
     * Sets the confirm_duration
     *
     * @param int $confirmDuration
     * @return void
     */
    public function setConfirmDuration(int $confirmDuration): void
    {
        $this->confirmDuration = $confirmDuration;
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
     * Add a category
     *
     * @param Category $category
     */
    public function addCategories(Category $category): void
    {
        $this->categories->attach($category);
    }
    /**
     * Removes a category
     *
     * @param Category $categoryToRemove The Module to be removed
     */
    public function removeCategories(Category $categoryToRemove): void
    {
        $this->categories->detach($categoryToRemove);
    }
    /**
     * Sets the categories
     *
     * @param ObjectStorage<Category> $category
     * @return void
     */
    public function setCategories(ObjectStorage $category): void
    {
        $this->categories = $category;
    }

    /**
     * Returns the categories
     *
     * @return ObjectStorage<Category> $category
     */
    public function getCategories(): ObjectStorage
    {
        return $this->categories;
    }

}