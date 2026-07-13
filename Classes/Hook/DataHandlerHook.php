<?php

namespace Bb\ConsentBanner\Hook;

use Bb\ConsentBanner\Utility\StringUtility;
use Exception;
use Random\RandomException;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Schema\Exception\UndefinedFieldException;
use TYPO3\CMS\Core\Schema\Exception\UndefinedSchemaException;
use TYPO3\CMS\Core\Schema\TcaSchemaFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class DataHandlerHook
{


    /**
     * Prevent saving of a news record if the editor doesn't have access to all categories of the news record
     *
     * @param array $fieldArray
     * @param string $table
     * @param int|string $id
     * @param $parentObject DataHandler
     * @throws RandomException
     * @throws Exception
     */
    public function processDatamap_preProcessFieldArray(array &$fieldArray, string $table, int|string $id, DataHandler $parentObject): void
    {
        if (!in_array($table, ['tx_consentbanner_domain_model_banner', 'tx_consentbanner_domain_model_consent_groups', 'tx_consentbanner_domain_model_consent_components'], true)) {
            return;
        }
        $fielKeyArray = match ($table) {
            'tx_consentbanner_domain_model_banner' => ['essential_group_id', 'essential_group_hash', 'essential_title'],
            'tx_consentbanner_domain_model_consent_groups' => ['group_id', 'group_hash', 'group_title'],
            'tx_consentbanner_domain_model_consent_components' => ['component_id', 'component_hash', 'component_title'],
        };

        $fnId = ['class' => StringUtility::class, 'method' => 'generateUniqueId', 'args' => []];
        $fnHash = ['class' => StringUtility::class, 'method' => 'generateHash', 'args' => ['fieldKey' => $fielKeyArray[2]]];
        if($table === 'tx_consentbanner_domain_model_banner'){
            $fnHashBanner = ['class' => StringUtility::class, 'method' => 'generateHash', 'args' => ['fieldKey' => 'banner_title']];
            $verification = ['banner_id' => $fnId, 'banner_hash' => $fnHashBanner, $fielKeyArray[0] => $fnId, $fielKeyArray[1] => $fnHash];
        }else{
            $verification = [$fielKeyArray[0] => $fnId, $fielKeyArray[1] => $fnHash];
        }

        if (MathUtility::canBeInterpretedAsInteger($id) && !str_contains($id, 'NEW')) {
            $tableRecord = BackendUtility::getRecord($table, $id);
        }

        foreach ($verification as $key => $value){
            if (is_array($value) && $this->array_keys_exists(['class', 'method'], $value)){
                $callFn = false;
                if (MathUtility::canBeInterpretedAsInteger($id) && !str_contains($id, 'NEW')) {
                    $hasRecordKeyFieldValue = !empty($tableRecord[$key]);
                    $callFn = !$hasRecordKeyFieldValue;
                }elseif (str_contains($id, 'NEW')){
                    $hasRecordKeyFieldValue = false;
                    $callFn = true;
                }


                if($callFn){
                    if(is_array($value['args']) && !empty($value['args'])){
                        $fieldName = $value['args']['fieldKey'];
                        if (str_contains($id, 'NEW') && !empty($fieldArray[$fieldName])){
                            $valueParam = $fieldArray[$fieldName];
                        }
                        if (!$hasRecordKeyFieldValue && isset($tableRecord)){
                            $valueParam = !empty($fieldArray[$fieldName]) ? $fieldArray[$fieldName] : $tableRecord[$fieldName];
                        }

                        if (!empty($valueParam)){
                            $fieldArray[$key] = call_user_func([$value['class'], $value['method']], $valueParam);
                        }
                    }else{
                        $fieldArray[$key] = call_user_func([$value['class'], $value['method']]);
                    }
                }

            }
        }
    }

    protected function array_keys_exists(array $keys, array $array): bool
    {
        $diff = array_diff_key(array_flip($keys), $array);
        return count($diff) === 0;
    }

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}