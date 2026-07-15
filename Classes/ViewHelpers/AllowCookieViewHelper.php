<?php

namespace Bb\ConsentBanner\ViewHelpers;

use Bb\ConsentBanner\DataProcessing\ConsentBannerProcessor;
use Bb\ConsentBanner\Utility\CookieUtility;
use Doctrine\DBAL\Driver\Exception;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Gates the output of a content element behind the visitor's consent.
 *
 * Two cases are handled:
 *  1. A consent component covers the element's CType (field component_ce_target,
 *     e.g. the "YouTube" component targets "cevideoplayer"). The visitor can
 *     accept it inline via a toggle; on consent the real content is rendered,
 *     otherwise a component placeholder (with toggle) is shown.
 *  2. A plain TYPO3 "html" element that embeds an external iframe but is not
 *     covered by any component. The iframe is replaced by a generic placeholder
 *     (no toggle), since no consent option was configured for it.
 */
class AllowCookieViewHelper extends AbstractViewHelper
{
    private const COMPONENT_TABLE = 'tx_consentbanner_domain_model_consent_components';
    private const LL = 'LLL:EXT:consent_banner/Resources/Private/Language/locallang.xlf:';

    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    protected ServerRequestInterface $request;

    protected ?Site $site = null;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('class', 'string', 'Define classes for the placeholder element', false);
        $this->registerArgument('additionalAttributes', 'array', 'Additional tag attributes that can be added to the placeholder component', false, []);
    }

    /**
     * @throws Exception
     */
    public function render(): string
    {
        $this->request = $this->renderingContext->getAttribute(ServerRequestInterface::class);
        $this->site = $this->request->getAttribute('site');

        $data = $this->getContentElementData();
        $cType = (string)($data['CType'] ?? '');
        if ($cType === '') {
            return $this->renderChildren();
        }

        // The "html" content element is gated per element via ce_consent_component
        // and handled separately from the CType auto-match below.
        if ($cType === 'html') {
            return $this->renderHtmlElement($data);
        }

        // A component covers this content element type (e.g. cevideoplayer).
        $component = $this->findComponentByCType($cType);
        if ($component !== []) {
            return $this->renderComponentGate($component);
        }

        return $this->renderChildren();
    }

    /**
     * Renders a plain "html" content element.
     *
     * Gating for "html" is opt-in: it only applies when a consent component
     * targets the "html" CType (component_ce_target). That target is what makes
     * the TypoScriptModifier wrap tt_content.html in a COA_INT block, so the
     * element renders uncached and per-request decisions are safe. Without such a
     * component the element is served from the page cache and must not produce
     * consent-dependent output, so it is rendered unchanged.
     *
     * When gating is active:
     *  1. If a component is assigned to this specific element (ce_consent_component),
     *     that component gates the element: on consent the real content is rendered,
     *     otherwise a placeholder with an inline accept toggle is shown.
     *  2. Without an assignment external iframes in the body text are replaced by a
     *     generic placeholder (no toggle), while the surrounding text stays intact.
     *
     * @throws Exception
     */
    protected function renderHtmlElement(array $data): string
    {
        // Not gated unless a component targets the "html" CType (→ COA_INT).
        if ($this->findComponentByCType('html') === []) {
            return $this->renderChildren();
        }

        $assignedId = trim((string)($data['ce_consent_component'] ?? ''));
        if ($assignedId !== '' && $assignedId !== '0') {
            $component = $this->findComponentById($assignedId);
            if ($component !== []) {
                return $this->renderComponentGate($component);
            }
        }

        $placeholder = $this->buildPlaceholder(
            (string)LocalizationUtility::translate(self::LL . 'placeholderHeadline.removed.html'),
            (string)LocalizationUtility::translate(self::LL . 'placeholder.removed.html')
        );

        return $this->replaceExternalIframes((string)($data['bodytext'] ?? ''), $placeholder);
    }

    /**
     * Gates the child content behind the consent state of the given component.
     *
     * On consent the real content is rendered; otherwise a placeholder with an
     * inline accept toggle is returned. The real content is additionally kept in
     * an inert <template> inside the placeholder so it can be swapped in on
     * consent without a page reload.
     */
    protected function renderComponentGate(array $component): string
    {
        if ($this->hasConsent((string)$component['component_id'])) {
            return $this->renderChildren();
        }

        $headline = (string)($component['placeholder_title'] ?? '');
        if ($headline === '') {
            $headline = (string)($component['component_title'] ?? '');
        }

        return $this->buildPlaceholder(
            $headline,
            (string)($component['placeholder_description'] ?? ''),
            (string)($component['component_id'] ?? ''),
            $this->renderChildren()
        );
    }

    /**
     * Reads the tt_content row of the element currently being rendered.
     */
    protected function getContentElementData(): array
    {
        $data = $this->renderingContext->getVariableProvider()->get('data');
        if (!is_array($data) || !isset($data['CType'])) {
            $baseRenderingContext = $this->renderingContext->getViewHelperVariableContainer()->getView()->getRenderingContext();
            $data = $baseRenderingContext->getVariableProvider()->get('data');
        }

        return is_array($data) ? $data : [];
    }

    /**
     * Finds the consent component that covers the given content element type.
     *
     * @throws Exception
     */
    protected function findComponentByCType(string $cType): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(self::COMPONENT_TABLE);

        $queryBuilder
            ->select('component_id', 'component_title', 'placeholder_title', 'placeholder_description')
            ->from(self::COMPONENT_TABLE)
            ->where(
                $queryBuilder->expr()->inSet('component_ce_target', $queryBuilder->createNamedParameter($cType))
            )
            ->setMaxResults(1);

        $rootPageId = $this->site instanceof Site ? $this->site->getRootPageId() : 0;
        if ($rootPageId > 0) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($rootPageId, Connection::PARAM_INT))
            );
        }

        $row = $queryBuilder->executeQuery()->fetchAssociative();

        return is_array($row) ? $row : [];
    }

    /**
     * Finds a consent component by its component_id (the value stored in the
     * ce_consent_component field and used as the consent cookie key).
     *
     * @throws Exception
     */
    protected function findComponentById(string $componentId): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(self::COMPONENT_TABLE);

        $queryBuilder
            ->select('component_id', 'component_title', 'placeholder_title', 'placeholder_description')
            ->from(self::COMPONENT_TABLE)
            ->where(
                $queryBuilder->expr()->eq('component_id', $queryBuilder->createNamedParameter($componentId))
            )
            ->setMaxResults(1);

        $rootPageId = $this->site instanceof Site ? $this->site->getRootPageId() : 0;
        if ($rootPageId > 0) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($rootPageId, Connection::PARAM_INT))
            );
        }

        $row = $queryBuilder->executeQuery()->fetchAssociative();

        return is_array($row) ? $row : [];
    }

    /**
     * Checks the consent cookie for the given component id.
     */
    protected function hasConsent(string $componentId): bool
    {
        $raw = CookieUtility::getCookieValue(ConsentBannerProcessor::$cName);
        if ($raw === '') {
            return false;
        }

        $preferences = json_decode($raw, true);

        return is_array($preferences) && ($preferences[$componentId] ?? false) === true;
    }

    /**
     * Builds the placeholder markup.
     *
     * With a component id an inline accept toggle is rendered; without one
     * (generic case) only headline and text are shown.
     */
    protected function buildPlaceholder(string $headline, string $text, string $componentId = '', string $deferred = ''): string
    {
        $headlineEsc = htmlspecialchars($headline);
        $componentIdEsc = htmlspecialchars($componentId);

        $normalisedClassArgument = '';
        if ($this->hasArgument('class') && $this->arguments['class'] !== '') {
            $normalisedClassArgument = ' ' . $this->arguments['class'];
        }

        $normalisedAdditionalAttributes = '';
        if ($this->hasArgument('additionalAttributes')) {
            foreach ($this->arguments['additionalAttributes'] as $attribute => $value) {
                $normalisedAdditionalAttributes .= ' ' . htmlspecialchars((string)$attribute) . '="' . htmlspecialchars((string)$value) . '"';
            }
        }

        $componentAttribute = $componentId !== '' ? ' data-cookiebanner-component="' . $componentIdEsc . '"' : '';

        $html = '<div class="bb-consentbanner-placeholder' . htmlspecialchars($normalisedClassArgument) . '"' . $componentAttribute . $normalisedAdditionalAttributes . '>';
        $html .= '<div class="bb-consentbanner-placeholder-wrapper">';
        if ($headline !== '') {
            $html .= '<h3 class="bb-consentbanner-placeholder-headline">' . $headlineEsc . '</h3>';
        }

        if ($componentId !== '') {
            // Component placeholder with inline accept toggle.
            $html .= '<div class="bb-consentbanner-component" data-cookiebanner-component="' . $componentIdEsc . '">'
                . '<label class="bb-control-checkbox" aria-label="' . $headlineEsc . '">'
                . '<span class="bb-control-label bb-label-module">' . $headlineEsc . '</span>'
                . '<input type="checkbox" name="' . $componentIdEsc . '">'
                . '<span class="bb-toggle"></span>'
                . '</label>';
            if ($text !== '') {
                $html .= '<span class="bb-consentbanner-placeholder-text">' . $text . '</span>';
            }
            $html .= '</div>';
        } elseif ($text !== '') {
            // Generic placeholder (no toggle).
            $html .= '<span class="bb-consentbanner-placeholder-text">' . $text . '</span>';
        }

        $html .= '</div>';

        // Deferred real content: inert until JS clones it into the DOM on consent.
        if ($deferred !== '') {
            $html .= '<template class="bb-consentbanner-deferred">' . $deferred . '</template>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Replaces every iframe that points to an external host with the given
     * placeholder markup, keeping the surrounding body text intact.
     */
    public function replaceExternalIframes(string $htmlContent, string $replacementHtml): string
    {
        if (trim($htmlContent) === '' || !str_contains($htmlContent, '<iframe')) {
            return $htmlContent;
        }

        $dom = new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $htmlContent);
        $dom->encoding = 'utf-8';

        $ownDomainClean = $this->site instanceof Site
            ? str_replace('www.', '', $this->site->getBase()->getHost())
            : '';

        // Collect first, replace afterwards (NodeList indices shift on removal).
        $nodesToReplace = [];
        foreach ($dom->getElementsByTagName('iframe') as $iframe) {
            if (!$iframe->hasAttribute('src')) {
                continue;
            }
            $srcParts = parse_url($iframe->getAttribute('src'));
            if (!isset($srcParts['host'])) {
                // Relative URLs are not considered external.
                continue;
            }
            if (str_replace('www.', '', $srcParts['host']) !== $ownDomainClean) {
                $nodesToReplace[] = $iframe;
            }
        }

        foreach ($nodesToReplace as $node) {
            $tempDom = new \DOMDocument();
            @$tempDom->loadHTML('<div>' . $replacementHtml . '</div>');
            $fragment = $tempDom->getElementsByTagName('div')->item(0);
            if ($fragment) {
                $parent = $node->parentNode;
                while ($fragment->hasChildNodes()) {
                    $child = $fragment->removeChild($fragment->firstChild);
                    $parent->insertBefore($dom->importNode($child, true), $node);
                }
                $parent->removeChild($node);
            }
        }

        $body = $dom->getElementsByTagName('body')->item(0);
        $modifiedHtml = '';
        if ($body) {
            foreach ($body->childNodes as $node) {
                $modifiedHtml .= $dom->saveHTML($node);
            }
        }

        return $modifiedHtml;
    }
}
