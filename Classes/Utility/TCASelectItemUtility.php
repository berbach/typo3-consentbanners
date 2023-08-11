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
        foreach ($groups as $key => $group) {
            $params['items'][] = ['label' => $group['header'], 'value' => '--div--', 'group' => rtrim($key, '.')];
            foreach ($group['elements.'] as $element) {

                if(!in_array($element['tt_content_defValues.']['CType'], $removeItems, true)) {
                    $params['items'][] = ['label' => $element['title'], 'value' => $element['tt_content_defValues.']['CType'], 'icon' => $element['iconIdentifier'], 'group' => rtrim($key, '.')];
                }
            }
        }

        $params['items'] = array_filter($params['items'], static function ($item) {
            return isset($item['value']);
        });
    }
}
