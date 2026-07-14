<?php

namespace Bb\ConsentBanner\EventListener;

use Bb\ConsentBanner\Controller\ManagementController;
use Doctrine\DBAL\Exception;
use TYPO3\CMS\Backend\Controller\Event\BeforeFormEnginePageInitializedEvent;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Event\Mvc\BeforeActionCallEvent;

/**
 * Keeps the site TypoScript (sys_template.config) in sync with the consent
 * components: every content element type that is gated by a component
 * (component_ce_target) is wrapped into a COA_INT block so it renders uncached
 * and the AllowCookieViewHelper can decide per request whether to show the real
 * content or a placeholder.
 */
class TypoScriptModifier
{
    private const COMPONENT_TABLE = 'tx_consentbanner_domain_model_consent_components';
    private const TARGET_FIELD = 'component_ce_target';

    /**
     * pid of the sys_template that is currently being modified
     */
    protected int $globalPid = 1;

    /**
     * @throws Exception
     */
    public function __invoke($event): void
    {
        if (!$event) {
            return;
        }

        // Clean up orphaned blocks whenever the banner management module is opened.
        if ($event instanceof BeforeActionCallEvent
            && $event->getControllerClassName() === ManagementController::class
            && $event->getActionMethodName() === 'bannerAction'
        ) {
            $this->janitor();
            return;
        }

        if (!($event instanceof BeforeFormEnginePageInitializedEvent)) {
            return;
        }

        $parsedBody = $event->getRequest()->getParsedBody();
        if (!is_array($parsedBody)
            || !isset($parsedBody['data'][self::COMPONENT_TABLE])
            || !is_array($parsedBody['data'][self::COMPONENT_TABLE])
        ) {
            return;
        }

        // A single save may contain several inline components (essential / group).
        foreach ($parsedBody['data'][self::COMPONENT_TABLE] as $recordId => $recordData) {
            if (!is_array($recordData) || !array_key_exists(self::TARGET_FIELD, $recordData)) {
                continue;
            }

            [$oldTarget, $pid] = $this->getExistingState($recordId, $recordData);
            $newTarget = (string)$recordData[self::TARGET_FIELD];

            if ($newTarget === $oldTarget) {
                continue;
            }

            $this->globalPid = $pid;
            $this->updateTypoScript($oldTarget, $newTarget);
        }
    }

    /**
     * Resolves the previously stored target and the pid of the sys_template to
     * modify for a given (possibly new) component record.
     *
     * @return array{0: string, 1: int} [oldTarget, pid]
     * @throws Exception
     */
    private function getExistingState(int|string $recordId, array $recordData): array
    {
        if (MathUtility::canBeInterpretedAsInteger($recordId) && !str_contains((string)$recordId, 'NEW')) {
            $queryBuilder = $this->getQueryBuilderForComponents();
            // Read the actually persisted value, regardless of hidden/deleted state.
            $queryBuilder->getRestrictions()->removeAll();
            $row = $queryBuilder
                ->select(self::TARGET_FIELD, 'pid')
                ->from(self::COMPONENT_TABLE)
                ->where(
                    $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter((int)$recordId, Connection::PARAM_INT))
                )
                ->executeQuery()
                ->fetchAssociative();

            if (is_array($row)) {
                return [(string)($row[self::TARGET_FIELD] ?? ''), (int)($row['pid'] ?? $this->globalPid)];
            }
        }

        // New record: no previous target; derive pid from the submitted data.
        $pid = isset($recordData['pid']) && MathUtility::canBeInterpretedAsInteger($recordData['pid'])
            ? (int)$recordData['pid']
            : $this->globalPid;

        return ['', $pid];
    }

    /**
     * @throws Exception
     */
    private function updateTypoScript(string $oldTarget, string $newTarget): void
    {
        $typoScript = $this->readFile();

        foreach (GeneralUtility::trimExplode(',', $oldTarget, true) as $element) {
            $this->removeElement($typoScript, $element);
        }

        foreach (GeneralUtility::trimExplode(',', $newTarget, true) as $element) {
            $this->addElement($typoScript, $element);
        }

        $this->overrideFile($this->formatTypoScript($typoScript));
    }

    /**
     * Removes managed COA_INT blocks whose content element type is no longer
     * referenced by any component. Reconciles every sys_template that carries
     * managed blocks, so blocks also disappear after the last component is gone.
     *
     * @throws Exception
     */
    private function janitor(): void
    {
        $targetsByPid = $this->collectTargetsByPid();

        foreach ($this->findManagedTemplates() as $template) {
            $pid = (int)$template['pid'];
            $typoScript = (string)$template['config'];
            $validTargets = $targetsByPid[$pid] ?? [];

            preg_match_all('/# START ([^ ]+) #/', $typoScript, $matches);

            $changed = false;
            foreach (array_unique($matches[1]) as $elementName) {
                if (in_array($elementName, $validTargets, true)) {
                    continue;
                }
                $this->removeElement($typoScript, $elementName);
                $changed = true;
            }

            if ($changed) {
                $this->globalPid = $pid;
                $this->overrideFile($this->formatTypoScript($typoScript));
            }
        }
    }

    /**
     * @return array<int, string[]> pid => list of gated content element types
     * @throws Exception
     */
    private function collectTargetsByPid(): array
    {
        $rows = $this->getQueryBuilderForComponents()
            ->select('pid', self::TARGET_FIELD)
            ->from(self::COMPONENT_TABLE)
            ->executeQuery()
            ->fetchAllAssociative();

        $targetsByPid = [];
        foreach ($rows as $row) {
            $pid = (int)$row['pid'];
            foreach (GeneralUtility::trimExplode(',', (string)$row[self::TARGET_FIELD], true) as $element) {
                $targetsByPid[$pid][] = $element;
            }
        }

        return $targetsByPid;
    }

    /**
     * @return array<int, array{pid: int, config: string}>
     * @throws Exception
     */
    private function findManagedTemplates(): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_template');

        return $queryBuilder
            ->select('pid', 'config')
            ->from('sys_template')
            ->where(
                $queryBuilder->expr()->like(
                    'config',
                    $queryBuilder->createNamedParameter('%# START %')
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /**
     * @throws Exception
     */
    private function readFile(): string
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_template');

        $contents = $queryBuilder
            ->select('config')
            ->from('sys_template')
            ->where(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($this->globalPid, Connection::PARAM_INT))
            )
            ->executeQuery()
            ->fetchAssociative();

        if ($contents && !empty($contents['config'])) {
            return (string)$contents['config'];
        }

        return '';
    }

    private function overrideFile(string $typoScript): void
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_template');

        $queryBuilder
            ->update('sys_template')
            ->where(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($this->globalPid, Connection::PARAM_INT))
            )
            ->set('config', $typoScript)
            ->executeStatement();
    }

    private function addElement(string &$typoScript, string $elementName): void
    {
        // Do not add a block twice.
        if (preg_match('/# START ' . preg_quote($elementName, '/') . ' #/', $typoScript)) {
            return;
        }

        $typoScript .= "\n# START " . $elementName . " #";
        $typoScript .= "
                tmp." . $elementName . " < tt_content." . $elementName . "
                tt_content." . $elementName . " >
                tt_content." . $elementName . " = COA_INT
                tt_content." . $elementName . " {
                    10 < tmp." . $elementName . "
                }";
        $typoScript .= "\n# END #\n";
    }

    private function removeElement(string &$typoScript, string $elementName): void
    {
        $typoScript = preg_replace(
            '/[(\r?\n)\s]# START ' . preg_quote($elementName, '/') . ' #[^#]+# END #[(\r?\n)\s]/',
            '',
            $typoScript,
            1
        );
    }

    private function formatTypoScript(string $typoScript): string
    {
        $typoScript = preg_replace('/ {5,}/', '    ', $typoScript);

        return preg_replace('/{\n {1,7}(?! )/', "{\n        ", $typoScript);
    }

    private function getQueryBuilderForComponents(): QueryBuilder
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(self::COMPONENT_TABLE);
    }
}
