<?php

namespace Bb\Consentbanners\Domain\Repository;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;



class CategoryRepository extends Repository
{
    /**
     * @var array Default order is by title ascending
     */
    protected $defaultOrderings = [
        'locked_and_active' => QueryInterface::ORDER_DESCENDING,
        'sorting' => QueryInterface::ORDER_DESCENDING
    ];

    public function initializeObject(): void
    {
        /* @var $querySettings \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings */
        $querySettings = $this->createQuery()->getQuerySettings();
        //$querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

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

        return $query->execute();
    }

    /**
     * @param null $id
     * @return array
     * @throws DBALException
     * @throws Exception
     */
    public function findCategories($id = null): array
    {
        $queryBuilder = $this->getQueryBuilder();
        $statement = $queryBuilder
            ->add('select',
                "category.name, category.description, category.uid, GROUP_CONCAT(module.name ORDER BY module.order SEPARATOR ',') AS module_name")
            ->from('tx_consentbanners_domain_model_category', 'category')
            ->leftJoin(
                'category',
                'tx_consentbanners_domain_model_module',
                'module',
                $queryBuilder->expr()->eq('module.category', $queryBuilder->quoteIdentifier('category.uid'))
            );

        if ($id) {
            $statement = $statement->where(
                $queryBuilder->expr()->eq('category.uid', $queryBuilder->createNamedParameter($id, \PDO::PARAM_INT))
            );
        }

        $statement = $statement->addOrderBy('category.order');
        $statement = $statement->groupBy('category.uid');
        $statement = $statement->execute();

        $splitModuleName = static function ($data) {
            $data['module_name'] = explode(",", $data['module_name']);
            return $data;
        };

        if ($id !== null) {
            return $splitModuleName($statement->fetchAssociative());
        }

        return array_map($splitModuleName, $statement->fetchAllAssociative());
    }

    private function getConnection(string $table): Connection
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($table);
    }

    private function getQueryBuilder(): QueryBuilder
    {
        return $this->getConnection('tx_consentbanners_domain_model_category')->createQueryBuilder();
    }
}