<?php
declare(strict_types=1);

namespace Bb\ConsentBanner\Domain\Model;

use TYPO3\CMS\Extbase\Annotation\ORM\Lazy;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Banner
 *
 * Dieses Model repräsentiert einen Datensatz aus der Tabelle
 * 'tx_consentbanner_domain_model_banner'.
 *
 */
class Banner extends AbstractEntity
{
    /**
     * @var string
     */
    protected string $bannerId = '';
    /**
     * @var string
     */
    protected string $bannerHash = '';
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
    protected string $bannerLayout = 'cb-bottom';

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
     * @var string|null
     */
    protected ?string $bannerNavigation = null;


    /**
     * @var string
     */
    protected string $essentialGroupId = '';
    /**
     * @var string
     */
    protected string $essentialGroupHash = '';
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
     * @var ObjectStorage<Component>
     */
    #[Lazy]
    protected ObjectStorage $essentialComponents;

    /**
     * group categories
     *
     * @var ObjectStorage<Group>
     */
    #[Lazy]
    protected ObjectStorage $consentOtherGroups;

    /**
     * @var int
     */
    protected int $privacySettingsVariant = 10;

    /**
     * @var string
     */
    protected string $textLinkPosition = 'last';

    /**
     * @var string
     */
    protected string $textLinkText = '';
    /**
     * @var string
     */
    protected string $buttonWidgetPosition = 'left';
    /**
     * @var string
     */
    protected string $buttonWidgetText = '';

    /**
     * @var string
     */
    protected string $targetFooterNavigation = '';

    /**
     * @var int
     */
    protected int $lifetimeBanner = 14;

    /**
     * @var int
     */
    protected int $lifetimeUserConsent = 1095;
    /**
     * @var bool
     */
    protected bool $hidden;
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
        $this->essentialComponents = new ObjectStorage();
        $this->consentOtherGroups = new ObjectStorage();
    }
    /**
     * Returns the $bannerTitle
     *
     * @return bool $bannerTitle
     */
    public function getBannerActive(): bool
    {
        return !$this->hidden;
    }
    /**
     * Sets the $bannerTitle
     *
     * @param bool $hidden
     * @return void
     */
    public function setBannerActive(bool $hidden): void
    {
        $this->hidden = $hidden;
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
     * Adds a essential Component
     *
     * @param Component $essentialComponent
     */
    public function addEssentialComponent(Component $essentialComponent): void
    {
        $this->essentialComponents->attach($essentialComponent);
    }
    /**
     * Removes a essential Component
     *
     * @param Component $essentialComponent
     */
    public function removeEssentialComponent(Component $essentialComponent): void
    {
        $this->essentialComponents->detach($essentialComponent);
    }
    /**
     * Returns the essential Components
     *
     * @return ObjectStorage<Component> $essentialComponents
     */
    public function getEssentialComponents(): ObjectStorage
    {
        return $this->essentialComponents;
    }
    /**
     * Sets the essential Components
     *
     * @param ObjectStorage<Component> $essentialComponents
     */
    public function setEssentialComponents(ObjectStorage $essentialComponents): void
    {
        $this->essentialComponents = $essentialComponents;
    }
    /**
     * Add a group category
     *
     * @param Group $consentOtherGroup
     */
    public function addConsentOtherGroup(Group $consentOtherGroup): void
    {
        $this->consentOtherGroups->attach($consentOtherGroup);
    }
    /**
     * Removes a group category
     *
     * @param Group $consentOtherGroup The group category to be removed
     */
    public function removeConsentOtherGroup(Group $consentOtherGroup): void
    {
        $this->consentOtherGroups->detach($consentOtherGroup);
    }
    /**
     * Returns the group Categories
     *
     * @return ObjectStorage<Group> $consentOtherGroups
     */
    public function getConsentOtherGroups(): ObjectStorage
    {
        return $this->consentOtherGroups;
    }
    /**
     * Sets the group Categories
     *
     * @param ObjectStorage<Group> $consentOtherGroups
     * @return void
     */
    public function setConsentOtherGroups(ObjectStorage $consentOtherGroups): void
    {
        $this->consentOtherGroups = $consentOtherGroups;
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

    public function getBannerNavigation(): ?string
    {
        return $this->bannerNavigation;
    }

    public function setBannerNavigation(?string $bannerNavigation): void
    {
        $this->bannerNavigation = $bannerNavigation;
    }

    public function getPrivacySettingsVariant(): int
    {
        return $this->privacySettingsVariant;
    }

    public function setPrivacySettingsVariant(int $privacySettingsVariant): void
    {
        $this->privacySettingsVariant = $privacySettingsVariant;
    }

    public function getTextLinkPosition(): string
    {
        return $this->textLinkPosition;
    }

    public function setTextLinkPosition(string $textLinkPosition): void
    {
        $this->textLinkPosition = $textLinkPosition;
    }

    public function getButtonWidgetPosition(): string
    {
        return $this->buttonWidgetPosition;
    }

    public function setButtonWidgetPosition(string $buttonWidgetPosition): void
    {
        $this->buttonWidgetPosition = $buttonWidgetPosition;
    }

    public function getTargetFooterNavigation(): string
    {
        return $this->targetFooterNavigation;
    }

    public function setTargetFooterNavigation(string $targetFooterNavigation): void
    {
        $this->targetFooterNavigation = $targetFooterNavigation;
    }

    public function getTextLinkText(): string
    {
        return $this->textLinkText;
    }

    public function setTextLinkText(string $textLinkText): void
    {
        $this->textLinkText = $textLinkText;
    }

    public function getButtonWidgetText(): string
    {
        return $this->buttonWidgetText;
    }

    public function setButtonWidgetText(string $buttonWidgetText): void
    {
        $this->buttonWidgetText = $buttonWidgetText;
    }

    public function getEssentialGroupId(): string
    {
        return $this->essentialGroupId;
    }

    public function setEssentialGroupId(string $essentialGroupId): void
    {
        $this->essentialGroupId = $essentialGroupId;
    }

    public function getEssentialGroupHash(): string
    {
        return $this->essentialGroupHash;
    }

    public function setEssentialGroupHash(string $essentialGroupHash): void
    {
        $this->essentialGroupHash = $essentialGroupHash;
    }

    public function getBannerId(): string
    {
        return $this->bannerId;
    }

    public function setBannerId(string $bannerId): void
    {
        $this->bannerId = $bannerId;
    }

    public function getBannerHash(): string
    {
        return $this->bannerHash;
    }

    public function setBannerHash(string $bannerHash): void
    {
        $this->bannerHash = $bannerHash;
    }
}