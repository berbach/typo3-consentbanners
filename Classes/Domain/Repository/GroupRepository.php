<?php

namespace Bb\ConsentBanner\Domain\Repository;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;



class GroupRepository extends Repository
{
    /**
     * @var string
     */
    public const TABLE_NAME = 'tx_consentbanner_domain_model_consent_groups';
    /**
     * @var array Default order is by title ascending
     */
    protected $defaultOrderings = [
        'sorting' => QueryInterface::ORDER_DESCENDING
    ];

    public function __construct(
        private readonly ConnectionPool $connectionPool
    ) {
        parent::__construct();
    }

    public function initializeObject(): void
    {
        /* @var $querySettings Typo3QuerySettings */
        $querySettings = $this->createQuery()->getQuerySettings();
        //$querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * @param int $rootPageId
     * @param int|null $languageId
     * @param bool $useIgnoreEnable
     * @return object|null
     */
    public function findByRootPageId(int $rootPageId, ?int $languageId = null, bool $useIgnoreEnable = false): ?object
    {
        $query = $this->createQuery();
        /* @var $querySettings Typo3QuerySettings */
        $querySettings = $query->getQuerySettings();
        $querySettings->setStoragePageIds([$rootPageId]);
        if($useIgnoreEnable) {
            $querySettings->setIgnoreEnableFields(true);
        }
        $this->setDefaultQuerySettings($querySettings);

//        if($languageId > 0) {
//            $querySettings->setLanguageUid((int)$languageId);
//        }


        return $query->execute();
    }

//    /**
//     * @param null $id
//     * @return array
//     */
//    public function findCategories($id = null): array
//    {
//        $queryBuilder = $this->getQueryBuilderForTable();
//        $statement = $queryBuilder
//            ->add('select',
//                "category.name, category.description, category.uid, GROUP_CONCAT(module.name ORDER BY module.order SEPARATOR ',') AS module_name")
//            ->from(self::TABLE_NAME, 'category')
//            ->leftJoin(
//                'category',
//                ModuleRepository::TABLE_NAME,
//                'module',
//                $queryBuilder->expr()->eq('module.category', $queryBuilder->quoteIdentifier('category.uid'))
//            );
//
//        if ($id) {
//            $statement = $statement->where(
//                $queryBuilder->expr()->eq('category.uid', $queryBuilder->createNamedParameter($id, Connection::PARAM_INT))
//            );
//        }
//
//        $statement = $statement->addOrderBy('category.order');
//        $statement = $statement->groupBy('category.uid');
//        $statement = $statement->execute();
//
//        $splitModuleName = static function ($data) {
//            $data['module_name'] = explode(",", $data['module_name']);
//            return $data;
//        };
//
//        if ($id !== null) {
//            return $splitModuleName($statement->fetchAssociative());
//        }
//
//        return array_map($splitModuleName, $statement->fetchAllAssociative());
//    }

    protected function getQueryBuilderForTable(): QueryBuilder
    {
        return $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);
    }
}