<?php

namespace Bb\ConsentBanner\Domain\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\Types;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;


class ConsentLogRepository extends Repository
{
    /**
     * @var string
     */
    public const TABLE_NAME = 'tx_consentbanner_domain_model_consent_log';

    protected $defaultOrderings = [
        'crdate' => QueryInterface::ORDER_ASCENDING
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
//        $querySettings->setRespectSysLanguage(false);
//        $querySettings->setLanguageOverlayMode(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * @param string $identificationKey
     * @param int $version
     * @param array $services
     * @return bool
     * @throws Exception
     */
    public function save(string $identificationKey, int $version, array $services, int $rootPageId = 0): void
    {
        $connection = $this->getConnectionForTable();
        // Site-scoped: one record per user per root page (composite key).
        $criteria = ['identification_key' => $identificationKey, 'root_page_id' => $rootPageId];
        $exists = $connection->count('*', self::TABLE_NAME, $criteria);

        $data = [
            'identification_key' => $identificationKey,
            'root_page_id' => $rootPageId,
            'banner_version' => $version,
            // Pass the array; the JSON column type encodes it exactly once.
            'consent_services' => $services,
            'tstamp' => time()
        ];
        $types = ['consent_services' => Types::JSON];

        if ($exists) {
            $connection->update(self::TABLE_NAME, $data, $criteria, $types);
        } else {
            $data['crdate'] = time();
            $connection->insert(self::TABLE_NAME, $data, $types);
        }
    }

    public function findByIdentificationKey(string $identificationKey): ?array
    {
        $row = $this->getConnectionForTable()->select(['*'], self::TABLE_NAME, ['identification_key' => $identificationKey])->fetchAssociative();

        return is_array($row) ? $row : null;
    }

    public function findAll(string $identificationKeySearch = '', int $rootPageId = 0): array
    {
        $queryBuilder = $this->getQueryBuilderForTable();
        $queryBuilder
            ->select('*')
            ->from(self::TABLE_NAME)
            ->orderBy('crdate', 'DESC');

        if ($rootPageId > 0) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->eq(
                    'root_page_id',
                    $queryBuilder->createNamedParameter($rootPageId, \TYPO3\CMS\Core\Database\Connection::PARAM_INT)
                )
            );
        }

        if ($identificationKeySearch !== '') {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->eq(
                    'identification_key',
                    $queryBuilder->createNamedParameter($identificationKeySearch)
                )
            );
        }

        $rows = $queryBuilder
            ->executeQuery()
            ->fetchAllAssociative();

        return array_map(static function (array $row): array {
            $services = $row['consent_services'] ?? '';
            if (is_string($services)) {
                $services = json_decode($services, true);
            }
            // Tolerate accidentally double-encoded values.
            if (is_string($services)) {
                $services = json_decode($services, true);
            }
            $row['consent_services'] = is_array($services) ? $services : [];
            return $row;
        }, $rows);
    }
    /**
     * @return QueryBuilder
     */
    /**
     * Deletes consent log entries not updated within the given number of days
     * (retention = lifetime_user_consent). Returns the number of deleted rows.
     */
    public function deleteExpired(int $lifetimeDays, int $rootPageId = 0): int
    {
        if ($lifetimeDays <= 0) {
            return 0;
        }
        $threshold = time() - ($lifetimeDays * 86400);
        $queryBuilder = $this->getQueryBuilderForTable();
        $queryBuilder
            ->delete(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->lt(
                    'tstamp',
                    $queryBuilder->createNamedParameter($threshold, \TYPO3\CMS\Core\Database\Connection::PARAM_INT)
                )
            );

        if ($rootPageId > 0) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->eq(
                    'root_page_id',
                    $queryBuilder->createNamedParameter($rootPageId, \TYPO3\CMS\Core\Database\Connection::PARAM_INT)
                )
            );
        }

        return (int)$queryBuilder->executeStatement();
    }

    protected function getQueryBuilderForTable(): QueryBuilder
    {
        return $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);
    }

    /**
     * @return Connection
     */
    protected function getConnectionForTable(): Connection
    {
        return $this->connectionPool->getConnectionForTable(self::TABLE_NAME);
    }
}
