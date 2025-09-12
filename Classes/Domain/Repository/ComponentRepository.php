<?php

namespace Bb\ConsentBanner\Domain\Repository;

use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;


class ComponentRepository extends Repository
{
    /**
     * @var string
     */
    public const TABLE_NAME = 'tx_consentbanner_domain_model_consent_components';

    protected $defaultOrderings = [
        'uid' => QueryInterface::ORDER_ASCENDING
    ];

    public function __construct(
        private readonly ConnectionPool $connectionPool
    ) {
        parent::__construct();
    }

    public function initializeObject(): void
    {
        /* @var Typo3QuerySettings $querySettings */
        $querySettings = $this->createQuery()->getQuerySettings();
//        $querySettings->setRespectSysLanguage(false);
//        $querySettings->setLanguageOverlayMode(false);
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

        if ($useIgnoreEnable) {
            $querySettings->setIgnoreEnableFields(true);
        }

        $this->setDefaultQuerySettings($querySettings);

        if (!is_null($languageId) && ($languageId > 0))
        {
            $query->matching($query->equals($GLOBALS['TCA'][self::TABLE_NAME]['ctrl']['languageField'], $languageId));
        }

        return $query->execute();
    }

    /**
     * @param int $id
     * @param null $order
     * @return array|bool
     * @throws Exception
     */
    public function findModules(int $id, $order = null): array|bool
    {
        $queryBuilder = $this->getQueryBuilderForTable();
        $statement = $queryBuilder
            ->select(
                'name', 'description', 'accepted_script', 'rejected_script', 'module_target', 'uid', 'pid'
            )
            ->from(self::TABLE_NAME);

        if ($id) {
            $statement = $statement->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($id, Connection::PARAM_INT))
            );
        }

        if ($order) {
            $statement = $statement->orderBy($order);
        }

        $statement = $statement->executeQuery();

        if ($id) {
            return $statement->fetchAssociative();
        }

        return $statement->fetchAllAssociative();
    }

    protected function getQueryBuilderForTable(): QueryBuilder
    {
        return $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);
    }
}
