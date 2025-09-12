<?php

namespace Bb\ConsentBanner\DataProcessing;

use Bb\ConsentBanner\Domain\Model\Banner;
use Bb\ConsentBanner\Utility\CookieUtility;
use Bb\ConsentBanner\Domain\Repository\BannerRepository;
use Doctrine\DBAL\Driver\Exception;
use Psr\Http\Message\ServerRequestInterface;
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
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

class ConsentBannerProcessor implements DataProcessorInterface
{
    /**
     * @var string
     */
    public static string $cName = 'BbConsentPreference';

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
        if(!empty($banner) && $banner->getConsentOtherGroups() && $banner->getConsentOtherGroups()->count() > 0){
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
                'consentAccepted'       => $consentAccepted,
                'layoutType'            => $banner->getBannerLayout(),
                //'showCategories'        => (bool)$banner->getShowCategories(),
                'isTextLink'            => $banner->getIsTextLink(),
                'cName'                 => self::$cName,
                'lifetimeBanner'        => MathUtility::canBeInterpretedAsInteger($banner->getLifetimeBanner()) ? $banner->getLifetimeBanner() : 20,
                'title'                 => $banner->getBannerTitle(),
                'description'           => $banner->getBannerDescription(),
                'footerNavigation'      => $privacyPage,
                'closeBtn'              => LocalizationUtility::translate('LLL:EXT:consent_banner/Resources/Private/Language/locallang.xlf:cookiebanner.closeBtn'),
                'buttonsDisplayNames'   => [
//                    'acceptAll'             => !empty($banner->getAcceptAll()) ? $banner->getAcceptAll() : LocalizationUtility::translate('LLL:EXT:consent_banner/Resources/Private/Language/locallang.xlf:cookiebanner.acceptAll'),
//                    'saveAndClose'          => !empty($banner->getSaveAndClose()) ? $banner->getSaveAndClose() : LocalizationUtility::translate('LLL:EXT:consent_banner/Resources/Private/Language/locallang.xlf:cookiebanner.saveAndClose'),
//                    'confirmSelection'      => !empty($banner->getConfirmSelection()) ? $banner->getConfirmSelection() : LocalizationUtility::translate('LLL:EXT:consent_banner/Resources/Private/Language/locallang.xlf:cookiebanner.confirmSelection'),
//                    'reject'                => !empty($banner->getReject()) ? $banner->getReject() : LocalizationUtility::translate('LLL:EXT:consent_banner/Resources/Private/Language/locallang.xlf:cookiebanner.reject'),
//                    'advancedSettings'      => !empty($banner->getAdvancedSettings()) ? $banner->getAdvancedSettings() : LocalizationUtility::translate('LLL:EXT:consent_banner/Resources/Private/Language/locallang.xlf:cookiebanner.advancedSettings')
                ]
            ];

            $tempGroups = [];
            $tempModules = [];
            $tempRejectedScript = '';



            $tempGroups[] = ['uid' => $banner->getUid(), 'name' => $banner->getEssentialTitle(), 'description' => $banner->getEssentialDescription(), 'lockedAndActive' => true];

            foreach ($banner->getConsentOtherGroups() as $otherGroup){
                //$lockedAndActive = $category->getLockedAndActive();
                $tempGroups[] = ['uid' => $otherGroup->getUid(), 'name' => $otherGroup->getGroupTitle(), 'description' => $otherGroup->getGroupDescription(), 'lockedAndActive' => false];

                if($otherGroup->getGroupComponents()->count() > 0) {
                    foreach ($otherGroup->getGroupComponents() as $module){
                        $tempModules[] = ['uid' => $module->getUid(), 'name' => $module->getName(), 'description' => $module->getDescription(), 'group' => ['uid' => $otherGroup->getUid()]];

                        if (!$consentPreferences && $module->getRejectedScript() !== '') {
                            $tempRejectedScript .= $this->clearJavaScript($module->getRejectedScript());
                        }

                        if(!empty($consentPreferences) && is_array($consentPreferences) && array_key_exists($module->getUid(), $consentPreferences)) {
                            if(!is_bool($consentPreferences[$module->getUid()])){continue;}

                            if ($consentPreferences[$module->getUid()] && $module->getAcceptedScript() !== '') {
                                $tempRejectedScript .= $this->clearJavaScript($module->getAcceptedScript());
                            } else if (!$consentPreferences[$module->getUid()] && $module->getRejectedScript() !== '') {
                                $tempRejectedScript .= $this->clearJavaScript($module->getRejectedScript());
                            }
                        }
                    }
                }
            }

            $tempBanner['groups'] =  $tempGroups;
            $tempBanner['modules'] =  $tempModules;

            GeneralUtility::makeInstance(AssetCollector::class)
                ->addInlineJavaScript(
                    'consent_data',
                    'var bbConsentBanner=' . json_encode($tempBanner) . ';'.$tempRejectedScript,
                    ['nonce' => $this->resolveNonceValue()],
                    ['priority' => true]
                );
        }

        GeneralUtility::makeInstance(AssetCollector::class)
            ->addInlineJavaScript(
                'banner_data',
                $resourceCompressor->compressJavaScriptSource(json_encode(['bannerGroups' => [['groupId' => 1, 'groupTitle' => 'Essenziell'], ['groupId' => 2, 'groupTitle' => 'Sonstige']]])),
                ['nonce' => $this->resolveNonceValue(), 'id' => 'bbBannerData', 'type' => 'application/json', 'crossorigin' => 'anonymous'],
                ['priority' => true]
            );

        $processedData['data']['consentBanner'] = $tempBanner;
        return $processedData;
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

    protected function getContext(): Context
    {
        return GeneralUtility::makeInstance(Context::class);
    }

    protected function getTypo3Request(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }

    protected function resolveNonceValue(): string
    {
        return GeneralUtility::makeInstance(RequestId::class)->nonce->consume();
    }



}
