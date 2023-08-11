<?php

namespace Bb\Consentbanners\DataProcessing;

use Bb\Consentbanners\Utility\CookieUtility;
use Bb\Consentbanners\Domain\Repository\SettingsRepository;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use \TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class ConsentBannerProcessor implements DataProcessorInterface
{
    /**
     * @var string
     */
    public static string $cName = 'BbConsentPreference';

    /**
     * @throws DBALException
     */
    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData): array
    {
        $settings = $contentObjectConfiguration['settings.'] ?? [];
        $requestSite = $this->getTypo3Request()->getAttribute('site');
        $consentPreferences = CookieUtility::getCookieValue(self::$cName);
        $settingsRepository = GeneralUtility::makeInstance(SettingsRepository::class);
        $banner = $settingsRepository->findByStorageIds([$requestSite->getRootPageId()]);

        if (!$consentPreferences) {
            $consentAccepted = false;
        }else{
            $consentPreferences = json_decode($consentPreferences, true);
            $consentAccepted = true;
        }

        $tempBanner = [];
        if($banner && $banner->getCategories()){
            $privacyPage = [];

            if (MathUtility::canBeInterpretedAsInteger($banner->getPrivacyPage())) {
                $privacyPage['uri'] = $cObj->getTypoLink_URL($banner->getPrivacyPage());
                $privacyPage['module_target'] = $settings['module_target'] ?? "";

                if (!empty($banner->getPrivacyPageLabel())) {
                    $privacyPage['label'] = $banner->getPrivacyPageLabel();
                } else {
                    $pageRecord = $this->getRecord('pages', $banner->getPrivacyPage(), 'uid, pid, ' . $GLOBALS['TCA']['pages']['ctrl']['languageField'] . ', nav_title, title');
                    $privacyPage['label'] = $pageRecord['nav_title'] ?? ($pageRecord['title'] ?? "");
                }
            }

            $tempBanner = [
                'consentAccepted'       => $consentAccepted,
                'layoutType'            => $banner->getLayoutType(),
                'showCategories'        => (bool)$banner->getShowCategories(),
                'cName'                 => self::$cName,
                'confirmDuration'       => MathUtility::canBeInterpretedAsInteger($banner->getConfirmDuration()) ? $banner->getConfirmDuration() : 20,
                'title'                 => $banner->getTitle(),
                'description'           => $banner->getDescription(),
                'privacyPage'           => $privacyPage,
                'buttonsDisplayNames'   => [
                    'acceptAll'             => !empty($banner->getAcceptAll()) ? $banner->getAcceptAll() : LocalizationUtility::translate('LLL:EXT:consentbanners/Resources/Private/Language/locallang.xlf:cookiebanner.acceptAll'),
                    'saveAndClose'          => !empty($banner->getSaveAndClose()) ? $banner->getSaveAndClose() : LocalizationUtility::translate('LLL:EXT:consentbanners/Resources/Private/Language/locallang.xlf:cookiebanner.saveAndClose'),
                    'confirmSelection'      => !empty($banner->getConfirmSelection()) ? $banner->getConfirmSelection() : LocalizationUtility::translate('LLL:EXT:consentbanners/Resources/Private/Language/locallang.xlf:cookiebanner.confirmSelection'),
                    'reject'                => !empty($banner->getReject()) ? $banner->getReject() : LocalizationUtility::translate('LLL:EXT:consentbanners/Resources/Private/Language/locallang.xlf:cookiebanner.reject'),
                    'advancedSettings'      => !empty($banner->getAdvancedSettings()) ? $banner->getAdvancedSettings() : LocalizationUtility::translate('LLL:EXT:consentbanners/Resources/Private/Language/locallang.xlf:cookiebanner.advancedSettings')
                ]
            ];

            $tempCategories = [];
            $tempModules = [];
            $tempRejectedScript = '';
            foreach ($banner->getCategories() as $category){
                $lockedAndActive = $category->getLockedAndActive();
                $tempCategories[] = ['uid' => $category->getUid(), 'name' => $category->getName(), 'description' => $category->getDescription(), 'lockedAndActive' => (bool)$lockedAndActive];

                if($category->getModules()->count() > 0) {
                    foreach ($category->getModules() as $module){
                        $tempModules[] = ['uid' => $module->getUid(), 'name' => $module->getName(), 'description' => $module->getDescription(), 'category' => ['uid' => $category->getUid()]];

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

            $tempBanner['categories'] =  $tempCategories;
            $tempBanner['modules'] =  $tempModules;

            GeneralUtility::makeInstance(AssetCollector::class)
                ->addInlineJavaScript(
                    'consent_data',
                    'var bbConsentBanner=' . json_encode($tempBanner) . ';'.$tempRejectedScript,
                    [],
                    ['priority' => true]
                );
        }

        $processedData['data']['consentBanner'] = $tempBanner;
        return $processedData;
    }

    /**
     * Get the record including possible translations
     * @param string $table
     * @param int $uid
     * @param string $fields
     * @return array
     * @throws DBALException
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
                        $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT))
                    )
                    ->execute()
                    ->fetchAssociative();

                if ($row) {
                    $row = GeneralUtility::makeInstance(PageRepository::class)->getRecordOverlay($table, $row, $this->getCurrentLanguage());
                    //$row = $this->getTypoScriptFrontendController()->sys_page->getRecordOverlay($table, $row, $this->getCurrentLanguage());
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
        $context = GeneralUtility::makeInstance(Context::class);
        try {
            $languageId = $context->getPropertyFromAspect('language', 'contentId');
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

    protected function getTypo3Request()
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}
