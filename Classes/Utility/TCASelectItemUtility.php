<?php

namespace Bb\Consentbanners\Utility;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TCASelectItemUtility
{
    public function getAllContentElements(&$params): void
    {
        $groups = BackendUtility::getPagesTSconfig(0)['mod.']['wizards.']['newContentElement.']['wizardItems.'];
        $removeItems = GeneralUtility::trimExplode(',',BackendUtility::getPagesTSconfig(0)['TCEFORM.']['tt_content.']['CType.']['removeItems']);
        $temp = [];
        foreach ($groups as $key => $group) {
            $temp['cTypeItems']['group'] = ['label' => $group['header'], 'value' => '--div--', 'group' => $key];

            foreach ($group['elements.'] as $element) {
                if(!in_array($element['tt_content_defValues.']['CType'], $removeItems, true)) {
                    $temp['cTypeItems']['items'][] = ['label' => $element['title'], 'value' => $element['tt_content_defValues.']['CType'], 'icon' => $element['iconIdentifier'], 'group' => $key];
                }
            }

            if(isset($temp['cTypeItems']['items'])) {
                $params['items'][] = $temp['cTypeItems']['group'];
                $params['items'][] = $temp['cTypeItems']['items'];
            }
        }

        $params['items'] = array_filter($params['items'], static function ($item) {
            return isset($item['value']);
        });
    }
}
