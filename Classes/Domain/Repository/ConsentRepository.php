<?php

namespace Bb\ConsentBanner\Domain\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;


class ConsentRepository extends Repository
{
    /**
     * @var string
     */
    public const TABLE_NAME = 'tx_consentbanner_domain_model_consent_data';

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



    protected function getQueryBuilderForTable(): QueryBuilder
    {
        return $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);
    }
}
