<?php

namespace Bb\Consentbanners\ViewHelpers;

use Bb\Consentbanners\Utility\CookieUtility;
use Closure;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3Fluid\Fluid\Core\Compiler\TemplateCompiler;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class AllowCookieViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeChildren = false;
    /**
     * @var boolean
     */
    protected $escapeOutput = false;


    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('class', 'string', 'Define classes for the placeholder element', false);
        $this->registerArgument('additionalAttributes', 'array', 'Additional tag attributes that can be added to the placeholder component', false, []);
    }

    /**
     * @param array $arguments
     * @param Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     *
     * @return string
     * @throws Exception
     * @throws DBALException
     */
    public static function renderStatic(array $arguments, Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string
    {
        if ($renderingContext->getVariableProvider()->get('data')['ce_consent_module'] === '0') {
            return $renderChildrenClosure();
        }

        $cookie = json_decode(CookieUtility::getCookieValue('BbConsentPreference'));
        $moduleName = $renderingContext->getVariableProvider()->get('data')['CType'];
        if (!$moduleName) {
            $baseRenderingContext = $renderingContext->getViewHelperVariableContainer()->getView()->getRenderingContext();
            $moduleName = $baseRenderingContext->getVariableProvider()->get('data')['CType'];
        }

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_consentbanners_domain_model_module');
        $queryBuilder->getRestrictions()->removeAll();
        $res = $queryBuilder
            ->select('uid', 'name', 'description', 'placeholder_headline', 'placeholder')
            ->from('tx_consentbanners_domain_model_module')
            ->where($queryBuilder->expr()->inSet('module_target', $queryBuilder->createNamedParameter($moduleName)));

        if ($moduleName === 'html')
            $res = $res
                ->where($queryBuilder->expr()->inSet('uid',
                    $queryBuilder->createNamedParameter(
                        $renderingContext->getVariableProvider()->get('data')['ce_consent_module']
                    )
                ));

        $res = $res
            ->execute()
            ->fetchAssociative();

        if ($cookie->{$res['uid']}) {
            return $renderChildrenClosure();
        }

        $normalisedClassArgument = '';
        if ($arguments['class'] && $arguments['class'] !== '') {
            $normalisedClassArgument = ' ' . $arguments['class'];
        }

        $normalisedAdditionalAttributes = '';
        foreach ($arguments['additionalAttributes'] as $attribute => $value) {
            $normalisedAdditionalAttributes .= ' ' . $attribute . '="' . $value . '"';
        }

        $html = '<div class="bb-consentbanner-placeholder' . $normalisedClassArgument . '"' . $normalisedAdditionalAttributes . '>';
        $html .= '<div class="bb-consentbanner-placeholder-wrapper">';
        $html .=
            '<h3 class="bb-consentbanner-placeholder-headline">' .
            $res['placeholder_headline'] .
            '</h3>';
        $html .=
            '<span class="bb-consentbanner-placeholder-text">' .
            $res['placeholder'] .
            '</span>';
        $html .=
            '<div class="bb-consentbanner-module" data-cookiebanner-module="' . $res['uid'] . '">
                <label class="bb-control-checkbox" aria-label="' . $res['name'] . '">
                    <span class="bb-control-label bb-label-module">' . $res['name'] . '</span>
                    <input type="checkbox" name="' . $res['uid'] . '">
                    <span class="bb-toggle"></span>
                </label>' .
//            '<p class="bb-consentbanner-description">' . $res['description'] . '</p>' .
            '</div>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;

    }

    /**
     * @param string $argumentsName
     * @param string $closureName
     * @param string $initializationPhpCode
     * @param ViewHelperNode $node
     * @param TemplateCompiler $compiler
     */
    public function compile(
        $argumentsName,
        $closureName,
        &$initializationPhpCode,
        ViewHelperNode $node,
        TemplateCompiler $compiler
    )
    {
        $compiler->disable();
    }

    /*protected static function serializeTagParameters(array $arguments): string
    {
        // array(param1 -> value1, param2 -> value2) --> param1="value1" param2="value2" for typolink.ATagParams
        $extraAttributes = [];
        $additionalAttributes = $arguments['additionalAttributes'] ?? [];
        foreach ($additionalAttributes as $attributeName => $attributeValue) {
            $extraAttributes[] = $attributeName . '="' . htmlspecialchars($attributeValue) . '"';
        }
        return implode(' ', $extraAttributes);
    }*/

    public function convertTypoScriptArrayToPlainArray(array $tsArray): array
    {
        foreach ($tsArray as $key => $value) {
            if (str_ends_with((string)$key, '.')) {
                $keyWithoutDot = substr((string)$key, 0, -1);
                $typoScriptNodeValue = $tsArray[$keyWithoutDot] ?? null;
                if (is_array($value)) {
                    $tsArray[$keyWithoutDot] = $this->convertTypoScriptArrayToPlainArray($value);
                    if ($typoScriptNodeValue !== null) {
                        $tsArray[$keyWithoutDot]['_typoScriptNodeValue'] = $typoScriptNodeValue;
                    }
                    unset($tsArray[$key]);
                } else {
                    $tsArray[$keyWithoutDot] = null;
                }
            }
        }
        return $tsArray;
    }

}
