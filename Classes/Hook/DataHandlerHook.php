<?php

namespace Bb\ConsentBanner\Hook;

use Bb\ConsentBanner\Utility\StringUtility;
use Exception;
use Random\RandomException;
use TYPO3\CMS\Core\Database\ConnectionPool;
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
    private const CONSENT_TABLES = [
        'tx_consentbanner_domain_model_banner',
        'tx_consentbanner_domain_model_consent_groups',
        'tx_consentbanner_domain_model_consent_components',
    ];
    private const BANNER_TABLE = 'tx_consentbanner_domain_model_banner';

    /**
     * pids whose banner_version was already bumped in the current DataHandler run
     * @var int[]
     */
    protected array $bumpedPids = [];

    /**
     * Bumps banner_version whenever the banner or one of its groups/components is
     * created or changed, so the frontend re-opens the banner for a new version.
     * Runs once per pid per save (a banner + its inline children share one pid).
     */
    public function processDatamap_afterDatabaseOperations(string $status, string $table, string|int $id, array $fieldArray, DataHandler $dataHandler): void
    {
        if (!in_array($table, self::CONSENT_TABLES, true)) {
            return;
        }

        $uid = $id;
        if (str_contains((string)$id, 'NEW')) {
            $uid = $dataHandler->substNEWwithIDs[$id] ?? null;
        }
        if (!MathUtility::canBeInterpretedAsInteger($uid)) {
            return;
        }

        // Only bump on an actual change: a new record, or one DataHandler detected
        // real field changes for (historyRecords is filled only then). A save
        // without any change must not bump the version.
        $changed = $status === 'new'
            || isset($dataHandler->getHistoryRecords()[$table . ':' . $uid]);
        if (!$changed) {
            return;
        }

        $this->bumpBannerVersion($this->resolvePid($table, (int)$uid, $fieldArray));
    }

    /**
     * Also bump when a banner / group / component is deleted (deletion runs
     * through the command map, not the data map).
     */
    public function processCmdmap_deleteAction(string $table, string|int $id, array $recordToDelete, bool &$recordWasDeleted, DataHandler $dataHandler): void
    {
        if (!in_array($table, self::CONSENT_TABLES, true)) {
            return;
        }
        $this->bumpBannerVersion((int)($recordToDelete['pid'] ?? 0));
    }

    /**
     * Increments banner_version for all banners on the given pid, once per pid
     * per DataHandler run.
     */
    private function bumpBannerVersion(int $pid): void
    {
        if ($pid <= 0 || in_array($pid, $this->bumpedPids, true)) {
            return;
        }
        $this->bumpedPids[] = $pid;

        GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable(self::BANNER_TABLE)
            ->executeStatement(
                'UPDATE ' . self::BANNER_TABLE . ' SET banner_version = banner_version + 1 WHERE pid = ? AND deleted = 0',
                [$pid]
            );
    }

    private function resolvePid(string $table, int $uid, array $fieldArray): int
    {
        if (isset($fieldArray['pid']) && MathUtility::canBeInterpretedAsInteger($fieldArray['pid'])) {
            return (int)$fieldArray['pid'];
        }
        $record = BackendUtility::getRecord($table, $uid, 'pid');

        return (int)($record['pid'] ?? 0);
    }

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