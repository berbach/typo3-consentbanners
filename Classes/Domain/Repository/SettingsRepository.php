<?php

namespace Bb\Consentbanners\Domain\Repository;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;

class SettingsRepository extends Repository
{
    /**
     * @var string
     */
    public static string $tableName = 'tx_consentbanners_domain_model_settings';

    /**
     * @param array $storageIds
     * @param int|null $languageId
     * @param bool $useIgnoreEnable
     * @return object|null
     */
    public function findByStorageIds(array $storageIds, int $languageId = null, bool $useIgnoreEnable = false): ?object
    {
        $query = $this->createQuery();
        /* @var $querySettings Typo3QuerySettings */
        $querySettings = $query->getQuerySettings();

        $querySettings->setStoragePageIds($storageIds);

        if($languageId > 0) {
            $querySettings->setLanguageUid((int)$languageId);
        }

        if($useIgnoreEnable) {
            $querySettings->setIgnoreEnableFields(true);
        }

        $this->setDefaultQuerySettings($querySettings);

        return $query->execute()->getFirst();
    }

    /**
     *
     * @param int $pid PID of record
     * @param bool $useDeleteClause Use the deleteClause to check if a record is deleted (default TRUE)
     * @return array|null Returns the row if found, otherwise NULL
     * @throws Exception
     * @throws DBALException
     */
    public function getRecordSettingsInLanguage(int $pid, int $languageId, int $originalId = null, bool $useDeleteClause = true): ?array
    {
        $isLocalized = false;
        if (isset($GLOBALS['TCA'][self::$tableName]['ctrl']) && is_array($GLOBALS['TCA'][self::$tableName]['ctrl'])) {
            $tcaCtrl = $GLOBALS['TCA'][self::$tableName]['ctrl'];
            $isLocalized = isset($tcaCtrl['languageField'], $tcaCtrl['transOrigPointerField']) && $tcaCtrl['transOrigPointerField'] && $tcaCtrl['languageField'];

            if ($pid && $isLocalized) {
                $queryBuilder = $this->getQueryBuilder();

                $queryBuilder->getRestrictions()->removeAll();

                // should the delete clause be used
                if ($useDeleteClause) {
                    $queryBuilder->getRestrictions()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
                }

                $queryBuilder
                    ->select('uid', 'pid', 'title', 'sys_language_uid')
                    ->from(self::$tableName)
                    ->where(
                        $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid, \PDO::PARAM_INT)),

                        $queryBuilder->expr()->eq($tcaCtrl['languageField'], $queryBuilder->createNamedParameter($languageId, \PDO::PARAM_INT))
                    );

                if(!is_null($originalId)){
                    $queryBuilder->andWhere(
                        $queryBuilder->expr()->eq($tcaCtrl['transOrigPointerField'], $queryBuilder->createNamedParameter($originalId, \PDO::PARAM_INT))
                    );
                }

                $queryBuilder->setMaxResults(1);

                $row = $queryBuilder->execute()->fetchAssociative();

                if($row){
                    return $row;
                }
            }
        }
        return null;
    }

    private function getQueryBuilder(): QueryBuilder
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(self::$tableName);
    }
}