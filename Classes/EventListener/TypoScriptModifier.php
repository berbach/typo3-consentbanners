<?php

namespace Bb\Consentbanners\EventListener;

use Bb\Consentbanners\Domain\Repository\ModuleRepository;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use TYPO3\CMS\Backend\Controller\Event\BeforeFormEnginePageInitializedEvent;
use TYPO3\CMS\Extbase\Event\Mvc\BeforeActionCallEvent;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

class TypoScriptModifier
{
    /**
     * @var int
     */
    protected int $globalPid = 1;

    /**
     * @throws Exception
     * @throws DBALException
     */
    public function __invoke($event): void
    {
        if (!$event) {
            return;
        }

        if ($event instanceof BeforeActionCallEvent && $event->getActionMethodName() === 'modulesAction') {
            $this->janitor();
        }

        if (!($event instanceof BeforeFormEnginePageInitializedEvent)
            || !$event->getRequest()->getParsedBody()
            || !array_key_exists('tx_consentbanners_domain_model_module', $event->getRequest()->getParsedBody()['data'])
            || !in_array(array_values($event->getRequest()->getQueryParams()['edit']['tx_consentbanners_domain_model_module'])[0], ['edit', 'new'])
        ) {
            return;
        }
        // compare old and new "target" values
        [$oldTarget, $newTarget] = $this->getTargetChange($event);
        if ($newTarget === $oldTarget) {
            return;
        }

        // change TypoScripts if values are different
        $this->updateTypoScript($oldTarget, $newTarget);
    }

    /**
     * @param BeforeFormEnginePageInitializedEvent $event
     * @return array
     * @throws DBALException
     * @throws Exception
     */
    private function getTargetChange(BeforeFormEnginePageInitializedEvent $event): array
    {
        $updatedArray = $event->getRequest()->getParsedBody()['data']['tx_consentbanners_domain_model_module'];

        $updatedData = array_values($updatedArray)[0];
        $updatedKey = array_keys($updatedArray)[0];

        $moduleRepo = GeneralUtility::makeInstance(ModuleRepository::class);

        $oldModule = is_numeric($updatedKey)
            ? $moduleRepo->findModules($updatedKey)
            : false;

        $this->readPid($oldModule);

        return [$oldModule['module_target'] ?? false, $updatedData['module_target']];
    }

    /**
     * @throws Exception
     * @throws DBALException
     */
    private function updateTypoScript($oldTarget, $newTarget): void
    {
        $typoScript = $this->readFile();

        if ($oldTarget) {
            foreach (explode(',', $oldTarget) as $t) {
                $this->removeElement($typoScript, $t);
            }
        }

        if ($newTarget) {
            foreach (explode(',', $newTarget) as $t) {
                $this->addElement($typoScript, $t);
            }
        }

        // a little formatting
        $typoScript = preg_replace('/ {5,}/', '    ', $typoScript);
        $typoScript = preg_replace('/{\n {1,7}(?! )/', "{\n        ", $typoScript);

        $this->overrideFile($typoScript);
    }

    private function readPid($module): void
    {
        if (!isset($module['pid'])) {
            return;
        }
        $this->globalPid = $module['pid'];
    }

    /**
     * @throws Exception
     * @throws DBALException
     */
    private function janitor(): void
    {
        $moduleRepo = GeneralUtility::makeInstance(ModuleRepository::class);
        $modules = $moduleRepo->findModules();
        if(isset($modules[0])) {
            $this->readPid($modules[0]);
        }
        $elements = array_map(static function ($module) {
            return explode(',', $module['module_target']);
        }, $modules);
        $elements = flattenArray($elements);

        $typoScript = $this->readFile();

        preg_match_all('/# START ([^ ]+) #/', $typoScript, $matches);

        foreach ($matches[1] as $elementName) {
            if (in_array($elementName, $elements, true)) {
                continue;
            }
            $this->removeElement($typoScript, $elementName);
        }

        $this->overrideFile($typoScript);
    }

    /**
     * @throws Exception
     * @throws DBALException
     */
    private function readFile(): string
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_template');

        $contents = $queryBuilder
            ->select('config')
            ->from('sys_template')
            ->where(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($this->globalPid, \PDO::PARAM_INT))
            )
            ->execute()
            ->fetchAssociative();

        if (!isset($contents[0])) {
            $contents[0]['config'] = '';
        }

        if (!isset($contents[0]['config'])) {
            $contents[0]['config'] = '';
        }

        return $contents[0]['config'];
    }

    /**
     * @throws DBALException
     */
    private function overrideFile($typoScript): void
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_template');

        $queryBuilder
            ->update('sys_template', 't')
            ->where(
                $queryBuilder->expr()->eq('t.pid', $queryBuilder->createNamedParameter($this->globalPid, \PDO::PARAM_INT))
            )
            ->set('t.config', $typoScript)
            ->execute();
    }

    private function addElement(string &$typoScript, string $elementName): void
    {
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
        $typoScript = preg_replace('/[(\r?\n)\s]# START ' . $elementName . ' #[^#]+# END #[(\r?\n)\s]/', '', $typoScript, 1);
    }
}

function flattenArray(array $array): array
{
    $return = array();
    array_walk_recursive($array, static function ($a) use (&$return) {
        $return[] = $a;
    });
    return $return;
}
