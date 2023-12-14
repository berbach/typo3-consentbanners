<?php

namespace Bb\Consentbanners\Utility;

use TYPO3\CMS\Backend\Utility\BackendUtility;

class TCASelectItemUtility
{
    public function getAllContentElements(&$params): void
    {
        $groups = BackendUtility::getPagesTSconfig(0)['mod.']['wizards.']['newContentElement.']['wizardItems.'];

        foreach ($groups as $group) {
            $params['items'][] = [$group['header'], '--div--'];

            foreach ($group['elements.'] as $element) {
                $params['items'][] = [$element['title'], $element['tt_content_defValues.']['CType'], $element['iconIdentifier']];
            }
        }

        $params['items'] = array_filter($params['items'], static function ($item) {
            return isset($item[1]);
        });
    }
}
