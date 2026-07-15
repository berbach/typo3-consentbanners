<?php

namespace Bb\ConsentBanner\DataProcessing;

use Bb\ConsentBanner\Domain\Model\Banner;
use Bb\ConsentBanner\Utility\CookieUtility;
use Bb\ConsentBanner\Domain\Repository\BannerRepository;
use Bb\ConsentBanner\Utility\Counter;
use Doctrine\DBAL\Driver\Exception;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Core\RequestId;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use \TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Resource\ResourceCompressor;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

class ConsentBannerProcessor implements DataProcessorInterface
{
    /**
     * @var string
     */
    public static string $cName = 'BbConsentPreferences';
    /**
     * @var ContentObjectRenderer
     */
    protected ContentObjectRenderer $cObj;

    /**

    /**
     * @param ContentObjectRenderer $cObj
     * @param array $contentObjectConfiguration
     * @param array $processorConfiguration
     * @param array $processedData
     * @return array
     * @throws AspectNotFoundException
     * @throws \Doctrine\DBAL\Exception
     */
    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData): array
    {
        $this->cObj = $cObj;
        $settings = $contentObjectConfiguration['settings.'] ?? [];
        $requestSite = $this->getTypo3Request()->getAttribute('site');
        $consentPreferences = CookieUtility::getCookieValue(self::$cName);
        $bannerRepository = GeneralUtility::makeInstance(BannerRepository::class);
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $resourceCompressor = GeneralUtility::makeInstance(ResourceCompressor::class);
        /* @var Banner $banner */
        $banner = $bannerRepository->findByRootPageId($requestSite->getRootPageId(), $this->getCurrentLanguage());

        if (!$consentPreferences) {
            $consentAccepted = false;
        }else{
            $consentPreferences = json_decode($consentPreferences, true);
            $consentAccepted = true;
        }

        $tempBanner = [];
        if(!empty($banner)){
            $privacyPage = [];

//            if (MathUtility::canBeInterpretedAsInteger($banner->getPrivacyPage())) {
//                $privacyPage['uri'] = $cObj->createUrl(['parameter' => $banner->getPrivacyPage()]);
//                $privacyPage['module_target'] = $settings['module_target'] ?? "";
//
//                if (!empty($banner->getPrivacyPageLabel())) {
//                    $privacyPage['label'] = $banner->getPrivacyPageLabel();
//                } else {
//                    $pageRecord = $this->getRecord('pages', $banner->getPrivacyPage(), 'uid, pid, ' . $GLOBALS['TCA']['pages']['ctrl']['languageField'] . ', nav_title, title');
//                    if(!empty($pageRecord['nav_title'])){
//                        $privacyPage['label'] = $pageRecord['nav_title'];
//                    }elseif (!empty($pageRecord['title'])){
//                        $privacyPage['label'] = $pageRecord['title'];
//                    }else{
//                        $privacyPage['label'] = "";
//                    }
//                }
//            }


            $tempBanner = [

                'banner' => [
                    'id'                    => $banner->getBannerId(),
                    'hash'                  => $banner->getBannerHash(),
                    'title'                 => $banner->getBannerTitle(),
                    'description'           => $banner->getBannerDescription(),
                    'active'                => $banner->getBannerActive(),
                    'version'               => $banner->getBannerVersion(),
                ],
                'footerNavigation'      => $this->addFooterNavigation($banner->getBannerNavigation()),
                'displayTexts' => [
                    'buttons'     => [
                        'acceptAll'             => !empty($banner->getAcceptAllText()) ? $banner->getAcceptAllText() : $this->getTranslate('accept_all_text'),
                        'acceptEssential'       => !empty($banner->getAcceptEssentialText()) ? $banner->getAcceptEssentialText() : $this->getTranslate('accept_essential_text'),
                        'close'                 => $this->getTranslate('close_text'),
                        'saveAndClose'          => !empty($banner->getSaveAndCloseText()) ? $banner->getSaveAndCloseText() : $this->getTranslate('save_and_close_text'),
                        'confirmSelection'      => !empty($banner->getConfirmSelectionText()) ? $banner->getConfirmSelectionText() : $this->getTranslate('confirm_selection_text'),
                        'advancedSettings'      => !empty($banner->getAdvancedSettingsText()) ? $banner->getAdvancedSettingsText() : $this->getTranslate('advanced_settings_text'),
                        'showInfo'              => !empty($banner->getCookieInfosShowText()) ? $banner->getCookieInfosShowText() : $this->getTranslate('cookie_infos_show_text'),
                        'closeInfo'             => !empty($banner->getCookieInfosCloseText()) ? $banner->getCookieInfosCloseText() : $this->getTranslate('cookie_infos_close_text'),
                    ],
                    'cookie'      => [
                        'name'          => !empty($banner->getCookieNameText()) ? $banner->getCookieNameText() : $this->getTranslate('cookie_name_text'),
                        'lifetime'      => !empty($banner->getCookieLifetimeText()) ? $banner->getCookieLifetimeText() : $this->getTranslate('cookie_lifetime_text'),
                        'provider'      => !empty($banner->getCookieProviderText()) ? $banner->getCookieProviderText() : $this->getTranslate('cookie_provider_text'),
                        'purpose'       => !empty($banner->getCookiePurposeText()) ? $banner->getCookiePurposeText() : $this->getTranslate('cookie_purpose_text'),
                        'description'   => !empty($banner->getCookieDescriptionText()) ? $banner->getCookieDescriptionText() : $this->getTranslate('cookie_description_text'),
                    ]

                ],
                'openerVariant'         => $banner->getPrivacySettingsVariant(), //10 Text link | 20 Button Widget
                'openerData'            => $this->addBannerOpenerVariant($banner->getPrivacySettingsVariant(), [
                    '10' => [
                        'targetFooterNavigation'    => $banner->getTargetFooterNavigation(),
                        'textLinkPosition'          => $banner->getTextLinkPosition(),
                        'textLinkText'              => !empty($banner->getTextLinkText()) ? $banner->getTextLinkText() : $this->getTranslate('text_link_text'),
                    ],
                    '20' => [
                        'buttonWidgetPosition'      => $banner->getButtonWidgetPosition(),
                        'buttonWidgetText'          => !empty($banner->getButtonWidgetText()) ? $banner->getButtonWidgetText() : $this->getTranslate('button_widget_text'),
                    ]
                ]),
                'groups' => $this->addGroups(
                    [
                        'id'                => $banner->getEssentialGroupId(),
                        'hash'              => $banner->getEssentialGroupHash(),
                        'title'             => !empty($banner->getEssentialTitle()) ? $banner->getEssentialTitle() : $this->getTranslate('essential_title_text'),
                        'description'       => $banner->getEssentialDescription(),
                        'components'        => $this->addComponents($banner->getEssentialComponents(), $banner->getEssentialGroupId()),
                        'lockedAndActive'   => true
                    ],
                    $banner->getConsentOtherGroups()
                ),
                'layout'                => $banner->getBannerLayout(), // cb-bottom || cb-overlay
                'lifetimes' => [
                    'banner'            => $banner->getLifetimeBanner(), // 14 || 21 || 28 Days
                    'userConsent'       => $banner->getLifetimeUserConsent() // 365 || 730 || 1095 Days
                ],
                'consentAccepted'       => $consentAccepted,
                'cName'                 => self::$cName,
            ];



//            $tempGroups = [];
//            $tempModules = [];
//            $tempRejectedScript = '';
//
//
//
//
//            $tempGroups[] = ['uid' => $banner->getUid(), 'name' => $banner->getEssentialTitle(), 'description' => $banner->getEssentialDescription(), 'lockedAndActive' => true];
//
//            foreach ($banner->getConsentOtherGroups() as $otherGroup){
//                //$lockedAndActive = $category->getLockedAndActive();
//                $tempGroups[] = ['uid' => $otherGroup->getUid(), 'name' => $otherGroup->getGroupTitle(), 'description' => $otherGroup->getGroupDescription(), 'lockedAndActive' => false];
//
//                if($otherGroup->getGroupComponents()->count() > 0) {
//                    foreach ($otherGroup->getGroupComponents() as $module){
//                        $tempModules[] = ['uid' => $module->getUid(), 'name' => $module->getName(), 'description' => $module->getDescription(), 'group' => ['uid' => $otherGroup->getUid()]];
//
//                        if (!$consentPreferences && $module->getRejectedScript() !== '') {
//                            $tempRejectedScript .= $this->clearJavaScript($module->getRejectedScript());
//                        }
//
//                        if(!empty($consentPreferences) && is_array($consentPreferences) && array_key_exists($module->getUid(), $consentPreferences)) {
//                            if(!is_bool($consentPreferences[$module->getUid()])){continue;}
//
//                            if ($consentPreferences[$module->getUid()] && $module->getAcceptedScript() !== '') {
//                                $tempRejectedScript .= $this->clearJavaScript($module->getAcceptedScript());
//                            } else if (!$consentPreferences[$module->getUid()] && $module->getRejectedScript() !== '') {
//                                $tempRejectedScript .= $this->clearJavaScript($module->getRejectedScript());
//                            }
//                        }
//                    }
//                }
//            }
//
//            $tempBanner['groups'] =  $tempGroups;
//            $tempBanner['modules'] =  $tempModules;
//
//            GeneralUtility::makeInstance(AssetCollector::class)
//                ->addInlineJavaScript(
//                    'consent_data',
//                    'var bbConsentBanner=' . json_encode($tempBanner) . ';'.$tempRejectedScript,
//                    ['nonce' => $this->resolveNonceValue()],
//                    ['priority' => true]
//                );
        }

        GeneralUtility::makeInstance(AssetCollector::class)
            ->addInlineJavaScript(
                'banner_data',
                $resourceCompressor->compressJavaScriptSource(json_encode($tempBanner)),
                ['nonce' => $this->resolveNonceValue(), 'id' => 'bbBannerData', 'type' => 'application/json', 'crossorigin' => 'anonymous'],
                ['priority' => true]
            );

        $processedData['banner']['data'] = $tempBanner;
        return $processedData;
    }

    /**
     * @throws AspectNotFoundException
     * @throws \Doctrine\DBAL\Exception
     */
    protected function addFooterNavigation(?string $navigationIds): array
    {
        $pageIds = GeneralUtility::trimExplode(',', $navigationIds, true);
        $menuItems = [];
        if (!empty($pageIds)){
            foreach ($pageIds as $pageId){
                $pageRecord = $this->getRecord('pages', $pageId, 'uid, pid, ' . $GLOBALS['TCA']['pages']['ctrl']['languageField'] . ', nav_title, title');
                //$pageRecord = BackendUtility::getRecord('pages', $pageId);
                $menuItems[] = [
                    'uid' => $pageRecord['uid'],
                    'title' => !empty($pageRecord['nav_title']) ? $pageRecord['nav_title'] : $pageRecord['title'],
                    'url' => $this->cObj->createUrl(['parameter' => $pageId])
                ];
            }
        }
        return $menuItems;
    }

    /**
     * @param int $variant
     * @param array $openerVariants
     * @return array
     */
    protected function addBannerOpenerVariant(int $variant, array $openerVariants):array
    {
        if (isset($openerVariants[$variant])) {
            $openerVariants[$variant]['type'] = $variant;
            return $openerVariants[$variant];
        }
        return [];
    }



    /**
     * @param object $components
     * @return array
     */
    protected function addComponents(object $components, int|string $groupId): array
    {
        if ($components->count() > 0){
            $counter = GeneralUtility::makeInstance(Counter::class, 10, 10);
            $tempComponents = [];
            foreach ($components as $component) {
                $tempComponents[$counter->count()] = [
                    'id' => $component->getComponentId(),
                    'hash' => $component->getComponentHash(),
                    'groupId' => $groupId,
                    'title' => $component->getComponentTitle(),
                    'description' => $component->getComponentDescription(),
                    'integrationType' => $component->getIntegrationType(),
                    'signals' => $component->getConsentModeSignals(),
                    'acceptedScript' => $component->getAcceptedScript(),
                    'rejectedScript' => $component->getRejectedScript(),
                    'cookies' => $this->addCookies($component)
                ];
                $counter->increment();
            }
            return $tempComponents;
        }
        return [];
    }

    /**
     * Collects the cookies of a component as a plain array for the frontend.
     *
     * @return array<int, array{name: string, provider: string, purpose: string, lifetime: string, description: string}>
     */
    protected function addCookies(object $component): array
    {
        $cookies = [];
        foreach ($component->getCookies() as $cookie) {
            $cookies[] = [
                'name' => $cookie->getCookieName(),
                'provider' => $cookie->getCookieProvider(),
                'purpose' => $cookie->getCookiePurpose(),
                'lifetime' => $cookie->getCookieLifetime(),
                'description' => $cookie->getCookieDescription(),
            ];
        }
        return $cookies;
    }

    /**
     * @param array $essentialGroup
     * @param object $otherGroups
     * @return array
     */
    protected function addGroups(array $essentialGroup, object $otherGroups): array
    {
        $counter = GeneralUtility::makeInstance(Counter::class, 10, 10);
        $tempGroups = [];

        if ($otherGroups->count() > 0){
            $tempGroups[$counter->count()] = $essentialGroup;
            $counter->increment();
            foreach ($otherGroups as $group){
                $tempGroup = [
                    'id'                => $group->getGroupId(),
                    'hash'              => $group->getGroupHash(),
                    'title'             => $group->getGroupTitle(),
                    'description'       => $group->getGroupDescription(),
                    'components'        => $this->addComponents($group->getGroupComponents(), $group->getGroupId()),
                    'lockedAndActive'   => false
                ];

                $tempGroups[$counter->count()] = $tempGroup;
                $counter->increment();
            }
        }

        return $tempGroups;
    }

    /**
     * Get the record including possible translations
     * @throws \Doctrine\DBAL\Exception
     * @throws AspectNotFoundException
     */
    protected function getRecord(string $table, int $uid, string $fields = '*'): array
    {
        if (MathUtility::canBeInterpretedAsInteger($uid)) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getQueryBuilderForTable($table);
            try {
                $row = $queryBuilder
                    ->select(...GeneralUtility::trimExplode(',', $fields, true))
                    ->from($table)
                    ->where(
                        $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT))
                    )
                    ->executeQuery()
                    ->fetchAssociative();

                if ($row) {
                    $row = GeneralUtility::makeInstance(PageRepository::class)->getLanguageOverlay($table, $row, $this->getContext()->getAspect('language'));
                }

                if (is_array($row) && !empty($row)) {
                    return $row;
                }
            } catch (Exception $e) {
                // do nothing
            }
        }
        return [];
    }

    /**
     * Get current language
     *
     * @return int $languageId
     */
    protected function getCurrentLanguage(): int
    {
        $languageId = 0;
        try {
            $languageId = $this->getContext()->getPropertyFromAspect('language', 'contentId');
        } catch (AspectNotFoundException $e) {
            // do nothing
        }
        return (int)$languageId;
    }

    protected function getTranslate(string $key): string
    {
        return LocalizationUtility::translate('LLL:EXT:consent_banner/Resources/Private/Language/locallang.xlf:'. trim($key));
    }

    /**
     * @param string $value
     * @return string $value
     */
    protected function clearJavaScript(string $value):string
    {
        $value = preg_replace('#/\*.*?\*/#s', '', $value);
        $value = preg_replace('/.*<script.*>(.*?)<\/script>.*$/is', '$1', $value);
        $value = str_replace(["\t\r\n", "\n", "\r", "var "], ['', '', '', 'var__'], $value);
        $value = preg_replace('/\s+/', '',$value);
        return str_replace("var__", 'var ', $value);
    }

    protected function generateAlphaId(string $input, int $length = 11): string
    {
        $chars2 = $this->clearString($input).'0123456789';
        // URL-sichere Zeichen: A-Z, a-z, 0-9, - und _
        // YouTube verwendet typischerweise A-Z, a-z und 0-9
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $id = '';
        $charLength = strlen($chars);

        for ($i = 0; $i < $length; $i++) {
            // Generiert ein zufälliges Zeichen aus der $chars-Zeichenkette
            $id .= $chars[random_int(0, $charLength - 1)];
        }

        return $id;
    }

    protected function clearString($input):string
    {
        $input = str_replace(['ä', 'ö', 'ü', 'ß'], ['ae', 'oe', 'ue', 'ss'], $input);
        $input = preg_replace('/\s+/', '', $input);
        return preg_replace('/[^a-zA-Z]/', '', $input);
    }

    protected function getContext(): Context
    {
        return GeneralUtility::makeInstance(Context::class);
    }

    protected function getTypo3Request(): ServerRequestInterface
    {
        return $this->cObj->getRequest();
    }

    protected function resolveNonceValue(): string
    {
        return GeneralUtility::makeInstance(RequestId::class)->nonce->consume();
    }



}
