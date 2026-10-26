<?php

namespace Bb\Consentbanners\ViewHelpers;

use Closure;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class CookieCategoriesViewHelper extends AbstractViewHelper
{
    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @throws Exception
     * @throws DBALException
     */
    public function render()
    {
        $templateVariableContainer = $this->renderingContext->getVariableProvider();
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_cookiebanner_domain_model_category');
        $queryBuilder->getRestrictions()->removeAll();
        $res = $queryBuilder
            ->add('select',
                'category.name, category.description, category.uid, ' .
                "GROUP_CONCAT(module.key ORDER BY module.order SEPARATOR ' ') AS module_key, " .
                "GROUP_CONCAT(module.name ORDER BY module.order SEPARATOR ' ') AS module_name, " .
                "GROUP_CONCAT(module.description ORDER BY module.order SEPARATOR ' ') AS module_description"
            )
            ->from('tx_cookiebanner_domain_model_category', 'category')
            ->leftJoin(
                'category',
                'tx_cookiebanner_domain_model_module',
                'module',
                $queryBuilder->expr()->eq('module.category', $queryBuilder->quoteIdentifier('category.uid'))
            )
            ->addOrderBy('category.order')
            ->groupBy('category.uid')
            ->executeQuery()
            ->fetchAllAssociative();
        $res = array_map(function ($o) {
            $options = ['module_key', 'module_name', 'module_description'];
            foreach ($options as $option)
                $o[$option] = $o[$option]
                    ? preg_split('/ /', $o[$option])
                    : null;
            return $o;
        }, $res);
        $templateVariableContainer->add('categories', $res);
        return $this->renderChildren();
    }
}
