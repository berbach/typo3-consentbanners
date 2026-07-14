<?php

namespace Bb\ConsentBanner\Utility;

use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Provides the selectable consent components for the tt_content field
 * "ce_consent_module". The stored value is the component_id, which is exactly
 * the key used in the consent cookie, so the AllowCookieViewHelper can look up
 * the consent state without an additional database round-trip.
 */
class TCASelectModuleUtility
{
    private const COMPONENT_TABLE = 'tx_consentbanner_domain_model_consent_components';

    /**
     * @throws Exception
     */
    public function getHtmlModules(array &$params): void
    {
        $params['items'][] = ['label' => 'Kein Drittanbieter', 'value' => '0'];

        $rootPageId = $this->resolveRootPageId($params);

        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder
            ->select('component_title', 'component_id')
            ->from(self::COMPONENT_TABLE)
            ->where(
                $queryBuilder->expr()->neq('component_id', $queryBuilder->createNamedParameter(''))
            )
            ->orderBy('component_title');

        if ($rootPageId > 0) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($rootPageId, \TYPO3\CMS\Core\Database\Connection::PARAM_INT))
            );
        }

        $components = $queryBuilder->executeQuery()->fetchAllAssociative();

        foreach ($components as $component) {
            $params['items'][] = [
                'label' => $component['component_title'] !== '' ? $component['component_title'] : $component['component_id'],
                'value' => $component['component_id'],
            ];
        }
    }

    private function resolveRootPageId(array $params): int
    {
        $site = $params['site'] ?? null;
        if ($site instanceof Site) {
            return $site->getRootPageId();
        }

        return 0;
    }

    private function getQueryBuilder(): QueryBuilder
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(self::COMPONENT_TABLE);
    }
}
