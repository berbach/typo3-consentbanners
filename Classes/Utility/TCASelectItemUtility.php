<?php

namespace Bb\ConsentBanner\Utility;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class TCASelectItemUtility
{
    public function getAllContentElements(&$params): void
    {
        $groupedArray = [];

        // TCA global laden
        $tca = $GLOBALS['TCA']['tt_content'] ?? [];

        if (isset($tca['columns']['CType']['config'])) {
            $configCType = $tca['columns']['CType']['config'];

            if (isset($configCType['items']) && is_array($configCType['items'])) {
                /** @var $site Site */
                $site = $params['site'];
                $tceForm = BackendUtility::getPagesTSconfig($site->getRootPageId())['TCEFORM.'];
                $removeItems = isset($tceForm['tt_content.']['CType.']['removeItems']) ? GeneralUtility::trimExplode(',',$tceForm['tt_content.']['CType.']['removeItems'].', list, felogin_login') : [];
                $removeGroups = GeneralUtility::trimExplode(',','menu, forms');
                $itemGroups = $configCType['itemGroups'] ?? [];
                if(isset($configCType['itemGroups']) && !empty($configCType['itemGroups'])) {
                    foreach ($itemGroups as $key => $group) {
                        if (!in_array($key, $removeGroups, true)) {
                            $groupedArray[$key] = [
                                "label" => $group,
                                "items" => []
                            ];
                        }
                    }
                }else{
                    $groupedArray["items"] = [];
                }


                foreach ($configCType['items'] as $item) {
                    $groupName = $item['group'];
                    if(!in_array($item['value'], $removeItems, true) && !in_array($groupName, $removeGroups, true)) {
                        if (isset($groupedArray[$groupName])) {
                            unset($item["group"]);
                            $groupedArray[$groupName]["items"][$item['value']] = $item;
                        }else {
                            $groupedArray["items"][$item['value']] = $item;
                        }
                    }
                }

                foreach ($groupedArray as $key => $item) {
                    if (!empty($groupedArray[$key]['items'])){
                        $params['items'][] = ['label' => $groupedArray[$key]['label'], 'value' => '--div--'];
                        foreach ($groupedArray[$key]['items'] as $item) {
                            $params['items'][] = ['label' => $item['label'], 'value' => $item['value']];
                        }
                    }elseif (!empty($groupedArray['items'])){
                        foreach ($groupedArray['items'] as $item) {
                            $params['items'][] = ['label' => $item['label'], 'value' => $item['value']];
                        }
                    }
                }

            }
        }
    }
}
