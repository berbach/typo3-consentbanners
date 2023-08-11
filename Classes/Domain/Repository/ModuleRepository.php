<?php

namespace Bb\Consentbanners\Domain\Repository;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;


class ModuleRepository extends Repository
{
    protected $defaultOrderings = [
        'uid' => QueryInterface::ORDER_ASCENDING
    ];

    public function initializeObject(): void
    {
        /* @var $querySettings \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings */
        $querySettings = $this->createQuery()->getQuerySettings();
//        $querySettings->setRespectSysLanguage(false);
//        $querySettings->setLanguageOverlayMode(false);
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

        if ($languageId > 0) {
            $querySettings->setLanguageUid((int)$languageId);
        }

        if ($useIgnoreEnable) {
            $querySettings->setIgnoreEnableFields(true);
        }

        $this->setDefaultQuerySettings($querySettings);

        return $query->execute();
    }

    /**
     * @param null $id
     * @param null $order
     * @return array|bool
     * @throws DBALException
     * @throws Exception
     */
    public function findModules($id = null, $order = null): array|bool
    {
        $queryBuilder = $this->getQueryBuilder();
        $statement = $queryBuilder
            ->select(
                'name', 'description', 'accepted_script', 'rejected_script', 'module_target', 'uid', 'pid'
            )
            ->from('tx_consentbanners_domain_model_module');

        if ($id) {
            $statement = $statement->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($id, \PDO::PARAM_INT))
            );
        }

        if ($order) {
            $statement = $statement->orderBy($order);
        }

        $statement = $statement->execute();

        if ($id) {
            return $statement->fetchAssociative();
        }

        return $statement->fetchAllAssociative();
    }

    private function getConnection(): Connection
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tx_consentbanners_domain_model_category');
    }

    private function getQueryBuilder(): QueryBuilder
    {
        return $this->getConnection()->createQueryBuilder();
    }
}
