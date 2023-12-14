<?php

namespace Bb\Consentbanners\Utility;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class TCASelectModuleUtility
{
    /**
     * @throws Exception
     * @throws DBALException
     */
    public function getHtmlModules(&$params): void
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_consentbanners_domain_model_module');
        $queryBuilder->getRestrictions()->removeAll();
        $modules = $queryBuilder
            ->select('uid', 'name')
            ->from('tx_consentbanners_domain_model_module')
            ->where($queryBuilder->expr()->inSet('module_target', $queryBuilder->createNamedParameter('html')))
            ->execute()
            ->fetchAllAssociative();

        $params['items'][] = ['No Module', '0'];
        foreach ($modules as $module) {
            $params['items'][] = [$module['name'], $module['uid']];
        }
    }
}
