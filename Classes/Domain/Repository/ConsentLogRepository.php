<?php

namespace Bb\ConsentBanner\Domain\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use phpDocumentor\Reflection\Types\Self_;
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
    public function save(string $identificationKey, int $version, array $services): void
    {
        $connection = $this->getConnectionForTable();
        $existsLog = $connection->select(['identification_key'], self::TABLE_NAME, ['identification_key' => $identificationKey])->fetchOne();

        $data = [
            'identification_key' => $identificationKey,
            'banner_version' => $version,
            'consent_services' => json_encode($services),
            'tstamp' => time()
        ];

        if ($existsLog) {
            $connection->update(self::TABLE_NAME, $data, ['identification_key' => $identificationKey]);
        } else {
            $data['crdate'] = time();
            $connection->insert(self::TABLE_NAME, $data);
        }
    }

    public function findByIdentificationKey(string $identificationKey): ?array
    {
        return $this->getConnectionForTable()->select(['*'], self::TABLE_NAME, ['identification_key' => $identificationKey])->fetchAssocative();
    }

    public function findAll(): array
    {
        $rows = $this->getQueryBuilderForTable()
            ->select('*')
            ->from(self::TABLE_NAME)
            ->orderBy('crdate', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();

        return array_map(static function (array $row): array {
            $row['consent_services'] = json_decode($row['consent_services'] ?? '{}', true) ?? [];
            return $row;
        }, $rows);
    }
    /**
     * @return QueryBuilder
     */
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
