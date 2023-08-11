<?php

namespace Bb\Consentbanners\Utility;

use TYPO3\CMS\Backend\Utility\BackendUtility;

class TCASelectItemUtility
{
    public function getAllContentElements(&$params): void
    {
        $groups = BackendUtility::getPagesTSconfig(0)['mod.']['wizards.']['newContentElement.']['wizardItems.'];

        foreach ($groups as $key => $group) {
            $params['items'][] = ['label' => $group['header'], 'value' => '--div--', 'group' => $key];

            foreach ($group['elements.'] as $element) {
                $params['items'][] = ['label' => $element['title'], 'value' => $element['tt_content_defValues.']['CType'], 'icon' => $element['iconIdentifier'], 'group' => $key];
            }
        }

        $params['items'] = array_filter($params['items'], static function ($item) {
            return isset($item['value']);
        });
    }
}
