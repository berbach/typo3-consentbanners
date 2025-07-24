<?php
declare(strict_types=1);

namespace Bb\Consentbanners\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Banner
 *
 * Dieses Model repräsentiert einen Datensatz aus der Tabelle
 * 'tx_consentbanners_domain_model_banner'.
 *
 */
class Banner extends AbstractEntity
{
    /**
     * @var string
     */
    protected string $bannerTitle = '';

    /**
     * @var string
     */
    protected string $bannerDescription = '';

    /**
     * @var string
     */
    protected string $bannerLayout = '';

    /**
     * @var string
     */
    protected string $userIdentificationText = '';

    /**
     * @var string
     */
    protected string $providerDescriptionText = '';

    /**
     * @var string
     */
    protected string $acceptAllText = '';

    /**
     * @var string
     */
    protected string $confirmSelectionText = '';

    /**
     * @var string
     */
    protected string $saveAndCloseText = '';

    /**
     * @var string
     */
    protected string $advancedSettingsText = '';

    /**
     * @var string
     */
    protected string $acceptEssentialText = '';

    /**
     * @var string
     */
    protected string $cookieInfosShowText = '';

    /**
     * @var string
     */
    protected string $cookieInfosCloseText = '';

    /**
     * @var string
     */
    protected string $cookieNameText = '';

    /**
     * @var string
     */
    protected string $cookieLifetimeText = '';

    /**
     * @var string
     */
    protected string $cookieProviderText = '';

    /**
     * @var string
     */
    protected string $cookiePurposeText = '';

    /**
     * @var string
     */
    protected string $cookieDescriptionText = '';

    /**
     * @var string
     */
    protected string $privacyLink = '';

    /**
     * @var string
     */
    protected string $imprintLink = '';

    /**
     * @var string
     */
    protected string $essentialTitle = '';

    /**
     * @var string
     */
    protected string $essentialDescription = '';

    /**
     * essential OptIns
     *
     * @var ObjectStorage<Module>
     */
    protected ObjectStorage $essentialOptIns;

    /**
     * group categories
     *
     * @var ObjectStorage<Category>
     */
    protected ObjectStorage $groupCategories;

    /**
     * @var bool
     */
    protected bool $isTextLink = false;

    /**
     * @var int
     */
    protected int $lifetimeBanner = 20;

    /**
     * @var int
     */
    protected int $lifetimeUserConsent = 365;

    /**
     * __construct
     */
    public function __construct() {
        $this->essentialOptIns = new ObjectStorage();
        $this->groupCategories = new ObjectStorage();
    }
    /**
     * Returns the $bannerTitle
     *
     * @return string $bannerTitle
     */
    public function getBannerTitle(): string
    {
        return $this->bannerTitle;
    }
    /**
     * Sets the $bannerTitle
     *
     * @param string $bannerTitle
     * @return void
     */
    public function setBannerTitle(string $bannerTitle): void
    {
        $this->bannerTitle = $bannerTitle;
    }
    /**
     * Returns the $bannerDescription
     *
     * @return string $bannerDescription
     */
    public function getBannerDescription(): string
    {
        return $this->bannerDescription;
    }
    /**
     * Sets the $bannerDescription
     *
     * @param string $bannerDescription
     * @return void
     */
    public function setBannerDescription(string $bannerDescription): void
    {
        $this->bannerDescription = $bannerDescription;
    }
    /**
     * Returns the $bannerLayout
     *
     * @return string $bannerLayout
     */
    public function getBannerLayout(): string
    {
        return $this->bannerLayout;
    }
    /**
     * Sets the $bannerLayout
     *
     * @param string $bannerLayout
     * @return void
     */
    public function setBannerLayout(string $bannerLayout): void
    {
        $this->bannerLayout = $bannerLayout;
    }
    /**
     * Returns the $userIdentificationText
     *
     * @return string $userIdentificationText
     */
    public function getUserIdentificationText(): string
    {
        return $this->userIdentificationText;
    }
    /**
     * Sets the $userIdentificationText
     *
     * @param string $userIdentificationText
     * @return void
     */
    public function setUserIdentificationText(string $userIdentificationText): void
    {
        $this->userIdentificationText = $userIdentificationText;
    }
    /**
     * Returns the $providerDescriptionText
     *
     * @return string $providerDescriptionText
     */
    public function getProviderDescriptionText(): string
    {
        return $this->providerDescriptionText;
    }
    /**
     * Sets the $providerDescriptionText
     *
     * @param string $providerDescriptionText
     * @return void
     */
    public function setProviderDescriptionText(string $providerDescriptionText): void
    {
        $this->providerDescriptionText = $providerDescriptionText;
    }
    /**
     * Returns the $acceptAllText
     *
     * @return string $acceptAllText
     */
    public function getAcceptAllText(): string
    {
        return $this->acceptAllText;
    }
    /**
     * Sets the $acceptAllText
     *
     * @param string $acceptAllText
     * @return void
     */
    public function setAcceptAllText(string $acceptAllText): void
    {
        $this->acceptAllText = $acceptAllText;
    }
    /**
     * Returns the $confirmSelectionText
     *
     * @return string $confirmSelectionText
     */
    public function getConfirmSelectionText(): string
    {
        return $this->confirmSelectionText;
    }
    /**
     * Sets the $confirmSelectionText
     *
     * @param string $confirmSelectionText
     * @return void
     */
    public function setConfirmSelectionText(string $confirmSelectionText): void
    {
        $this->confirmSelectionText = $confirmSelectionText;
    }
    /**
     * Returns the $saveAndCloseText
     *
     * @return string $saveAndCloseText
     */
    public function getSaveAndCloseText(): string
    {
        return $this->saveAndCloseText;
    }
    /**
     * Sets the $saveAndCloseText
     *
     * @param string $saveAndCloseText
     * @return void
     */
    public function setSaveAndCloseText(string $saveAndCloseText): void
    {
        $this->saveAndCloseText = $saveAndCloseText;
    }
    /**
     * Returns the $advancedSettingsText
     *
     * @return string $advancedSettingsText
     */
    public function getAdvancedSettingsText(): string
    {
        return $this->advancedSettingsText;
    }
    /**
     * Sets the $advancedSettingsText
     *
     * @param string $advancedSettingsText
     * @return void
     */
    public function setAdvancedSettingsText(string $advancedSettingsText): void
    {
        $this->advancedSettingsText = $advancedSettingsText;
    }
    /**
     * Returns the $acceptEssentialText
     *
     * @return string $acceptEssentialText
     */
    public function getAcceptEssentialText(): string
    {
        return $this->acceptEssentialText;
    }
    /**
     * Sets the acceptEssentialText
     *
     * @param string $acceptEssentialText
     * @return void
     */
    public function setAcceptEssentialText(string $acceptEssentialText): void
    {
        $this->acceptEssentialText = $acceptEssentialText;
    }
    /**
     * Returns the cookieInfosShowText
     *
     * @return string cookieInfosShowText
     */
    public function getCookieInfosShowText(): string
    {
        return $this->cookieInfosShowText;
    }
    /**
     * Sets the title
     *
     * @param string $cookieInfosShowText
     * @return void
     */
    public function setCookieInfosShowText(string $cookieInfosShowText): void
    {
        $this->cookieInfosShowText = $cookieInfosShowText;
    }
    /**
     * Returns the $cookieInfosCloseText
     *
     * @return string $cookieInfosCloseText
     */
    public function getCookieInfosCloseText(): string
    {
        return $this->cookieInfosCloseText;
    }
    /**
     * Sets the $cookieInfosCloseText
     *
     * @param string $cookieInfosCloseText
     * @return void
     */
    public function setCookieInfosCloseText(string $cookieInfosCloseText): void
    {
        $this->cookieInfosCloseText = $cookieInfosCloseText;
    }
    /**
     * Returns the $cookieNameText
     *
     * @return string $cookieNameText
     */
    public function getCookieNameText(): string
    {
        return $this->cookieNameText;
    }
    /**
     * Sets the $cookieNameText
     *
     * @param string $cookieNameText
     * @return void
     */
    public function setCookieNameText(string $cookieNameText): void
    {
        $this->cookieNameText = $cookieNameText;
    }
    /**
     * Returns the cookieLifetimeText
     *
     * @return string cookieLifetimeText
     */
    public function getCookieLifetimeText(): string
    {
        return $this->cookieLifetimeText;
    }
    /**
     * Sets the $cookieLifetimeText
     *
     * @param string $cookieLifetimeText
     * @return void
     */
    public function setCookieLifetimeText(string $cookieLifetimeText): void
    {
        $this->cookieLifetimeText = $cookieLifetimeText;
    }
    /**
     * Returns the $cookieProviderText
     *
     * @return string $cookieProviderText
     */
    public function getCookieProviderText(): string
    {
        return $this->cookieProviderText;
    }
    /**
     * Sets the $cookieProviderText
     *
     * @param string $cookieProviderText
     * @return void
     */
    public function setCookieProviderText(string $cookieProviderText): void
    {
        $this->cookieProviderText = $cookieProviderText;
    }
    /**
     * Returns the $cookiePurposeText
     *
     * @return string $cookiePurposeText
     */
    public function getCookiePurposeText(): string
    {
        return $this->cookiePurposeText;
    }
    /**
     * Sets the cookie Purpose Text
     *
     * @param string $cookiePurposeText
     * @return void
     */
    public function setCookiePurposeText(string $cookiePurposeText): void
    {
        $this->cookiePurposeText = $cookiePurposeText;
    }
    /**
     * Returns the $cookieDescriptionText
     *
     * @return string $cookieDescriptionText
     */
    public function getCookieDescriptionText(): string
    {
        return $this->cookieDescriptionText;
    }
    /**
     * Sets the cookie Description Text
     *
     * @param string $cookieDescriptionText
     * @return void
     */
    public function setCookieDescriptionText(string $cookieDescriptionText): void
    {
        $this->cookieDescriptionText = $cookieDescriptionText;
    }
    /**
     * Returns the $privacyLink
     *
     * @return string $privacyLink
     */
    public function getPrivacyLink(): string
    {
        return $this->privacyLink;
    }
    /**
     * Sets the privacy Link
     *
     * @param string $privacyLink
     * @return void
     */
    public function setPrivacyLink(string $privacyLink): void
    {
        $this->privacyLink = $privacyLink;
    }
    /**
     * Returns the $imprintLink
     *
     * @return string $imprintLink
     */
    public function getImprintLink(): string
    {
        return $this->imprintLink;
    }
    /**
     * Sets the imprint Link
     *
     * @param string $imprintLink
     * @return void
     */
    public function setImprintLink(string $imprintLink): void
    {
        $this->imprintLink = $imprintLink;
    }
    /**
     * Returns the $essentialTitle
     *
     * @return string $essentialTitle
     */
    public function getEssentialTitle(): string
    {
        return $this->essentialTitle;
    }
    /**
     * Sets the essential Title
     *
     * @param string $essentialTitle
     * @return void
     */
    public function setEssentialTitle(string $essentialTitle): void
    {
        $this->essentialTitle = $essentialTitle;
    }
    /**
     * Returns the $essentialDescription
     *
     * @return string $essentialDescription
     */
    public function getEssentialDescription(): string
    {
        return $this->essentialDescription;
    }
    /**
     * Sets the essential Description
     *
     * @param string $essentialDescription
     * @return void
     */
    public function setEssentialDescription(string $essentialDescription): void
    {
        $this->essentialDescription = $essentialDescription;
    }
    /**
     * Adds a essential OptIn
     *
     * @param Module $essentialOptIn
     */
    public function addEssentialOptIn(Module $essentialOptIn): void
    {
        $this->essentialOptIns->attach($essentialOptIn);
    }
    /**
     * Removes a essential OptIn
     *
     * @param Module $essentialOptIn
     */
    public function removeEssentialOptIn(Module $essentialOptIn): void
    {
        $this->essentialOptIns->detach($essentialOptIn);
    }
    /**
     * Returns the essential OptIns
     *
     * @return ObjectStorage<Module> $essentialOptIns
     */
    public function getEssentialOptIns(): ObjectStorage
    {
        return $this->essentialOptIns;
    }
    /**
     * Sets the essential OptIns
     *
     * @param ObjectStorage<Module> $essentialOptIns
     */
    public function setEssentialOptIns(ObjectStorage $essentialOptIns): void
    {
        $this->essentialOptIns = $essentialOptIns;
    }
    /**
     * Add a group category
     *
     * @param Category $groupCategory
     */
    public function addGroupCategory(Category $groupCategory): void
    {
        $this->groupCategories->attach($groupCategory);
    }
    /**
     * Removes a group category
     *
     * @param Category $groupCategory The group category to be removed
     */
    public function removeGroupCategory(Category $groupCategory): void
    {
        $this->groupCategories->detach($groupCategory);
    }
    /**
     * Returns the group Categories
     *
     * @return ObjectStorage<Category> $groupCategories
     */
    public function getGroupCategories(): ObjectStorage
    {
        return $this->groupCategories;
    }
    /**
     * Sets the group Categories
     *
     * @param ObjectStorage<Category> $groupCategories
     * @return void
     */
    public function setGroupCategories(ObjectStorage $groupCategories): void
    {
        $this->groupCategories = $groupCategories;
    }

    public function getIsTextLink(): bool
    {
        return $this->isTextLink;
    }

    public function setIsTextLink(bool $isTextLink): void
    {
        $this->isTextLink = $isTextLink;
    }

    public function getLifetimeBanner(): int
    {
        return $this->lifetimeBanner;
    }

    public function setLifetimeBanner(int $lifetimeBanner): void
    {
        $this->lifetimeBanner = $lifetimeBanner;
    }

    public function getLifetimeUserConsent(): int
    {
        return $this->lifetimeUserConsent;
    }

    public function setLifetimeUserConsent(int $lifetimeUserConsent): void
    {
        $this->lifetimeUserConsent = $lifetimeUserConsent;
    }
}