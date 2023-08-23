<?php

namespace Bb\Consentbanners\Utility;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TCASelectItemUtility
{
    public function getAllContentElements(&$params): void
    {
        $groups = BackendUtility::getPagesTSconfig(0)['mod.']['wizards.']['newContentElement.']['wizardItems.'];
        $CType = isset(BackendUtility::getPagesTSconfig(0)['TCEFORM.']['tt_content.']['CType.']) ? BackendUtility::getPagesTSconfig(0)['TCEFORM.']['tt_content.']['CType.'] : null;
        $removeItems = !is_null($CType) && isset($CType['removeItems']) ? $CType['removeItems'] : null;
        $removeItems = !is_null($removeItems) ? GeneralUtility::trimExplode(',',$removeItems) : null;
        foreach ($groups as $key => $group) {
            $params['items'][] = ['label' => $group['header'], 'value' => '--div--', 'group' => rtrim($key, '.')];
            foreach ($group['elements.'] as $element) {

                if(!is_null($removeItems) && !in_array($element['tt_content_defValues.']['CType'], $removeItems, false)) {
                    $params['items'][] = ['label' => $element['title'], 'value' => $element['tt_content_defValues.']['CType'], 'icon' => $element['iconIdentifier'], 'group' => rtrim($key, '.')];
                }
                if (is_null($removeItems)){
                    $params['items'][] = ['label' => $element['title'], 'value' => $element['tt_content_defValues.']['CType'], 'icon' => $element['iconIdentifier'], 'group' => rtrim($key, '.')];
                }
            }
        }

        $params['items'] = array_filter($params['items'], static function ($item) {
            return isset($item['value']);
        });
    }
}
