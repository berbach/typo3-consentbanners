<?php

namespace Bb\ConsentBanner\Domain\Repository;

use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class BannerRepository extends Repository
{
    /**
     * @var string
     */
    public const TABLE_NAME = 'tx_consentbanner_domain_model_banner';


    public function __construct(
        private readonly ConnectionPool $connectionPool
    ) {
        parent::__construct();
    }
    /**
     * @param int $rootPageId
     * @param int $languageId
     * @return object|null
     */
    public function findByRootPageId(int $rootPageId, int $languageId): ?object
    {
        $query = $this->createQuery();
        /* @var $querySettings Typo3QuerySettings */
        $querySettings = $query->getQuerySettings();
        $querySettings->setStoragePageIds([$rootPageId]);
        $this->setDefaultQuerySettings($querySettings);

        $query->setLimit(1);
        $query->matching($query->equals($GLOBALS['TCA'][self::TABLE_NAME]['ctrl']['languageField'], $languageId));

        $result = $query->execute();
        return $result->count() ? $result->getFirst() : null;
    }

    /**
     *
     * @param int $pid PID of record
     * @param bool $useDeleteClause Use the deleteClause to check if a record is deleted (default TRUE)
     * @return array|null Returns the row if found, otherwise NULL
     * @throws Exception
     */
    public function getRecordBannerInLanguage(int $pid, int $languageId, ?int $originalId = null, bool $useDeleteClause = true): ?array
    {
        $isLocalized = false;
        if (isset($GLOBALS['TCA'][self::TABLE_NAME]['ctrl']) && is_array($GLOBALS['TCA'][self::TABLE_NAME]['ctrl'])) {
            $tcaCtrl = $GLOBALS['TCA'][self::TABLE_NAME]['ctrl'];
            $isLocalized = isset($tcaCtrl['languageField'], $tcaCtrl['transOrigPointerField']) && $tcaCtrl['transOrigPointerField'] && $tcaCtrl['languageField'];

            if ($pid && $isLocalized) {
                $queryBuilder = $this->getQueryBuilderForTable();
                $queryBuilder->getRestrictions()->removeAll();

                // should the delete clause be used
                if ($useDeleteClause) {
                    $queryBuilder->getRestrictions()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
                }

                $queryBuilder
                    ->select('uid', 'pid', 'banner_title', 'sys_language_uid')
                    ->from(self::TABLE_NAME)
                    ->where(
                        $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid, Connection::PARAM_INT)),
                        $queryBuilder->expr()->eq($tcaCtrl['languageField'], $queryBuilder->createNamedParameter($languageId, Connection::PARAM_INT))
                    );

                if(!is_null($originalId)){
                    $queryBuilder->andWhere(
                        $queryBuilder->expr()->eq($tcaCtrl['transOrigPointerField'], $queryBuilder->createNamedParameter($originalId, Connection::PARAM_INT))
                    );
                }

                $queryBuilder->setMaxResults(1);

                $row = $queryBuilder->executeQuery()->fetchAssociative();

                if($row){
                    return $row;
                }
            }
        }
        return null;
    }

    protected function getQueryBuilderForTable(): QueryBuilder
    {
        return $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);
    }
}