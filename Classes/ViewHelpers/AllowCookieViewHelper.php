<?php

namespace Bb\Consentbanners\ViewHelpers;

use Bb\Consentbanners\Domain\Model\Category;
use Bb\Consentbanners\Domain\Model\Module;
use Bb\Consentbanners\Domain\Repository\SettingsRepository;
use Bb\Consentbanners\Utility\CookieUtility;
use Closure;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\Compiler\TemplateCompiler;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class AllowCookieViewHelper extends AbstractViewHelper
{

    /**
     * @var boolean
     */
    protected $escapeChildren = false;
    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    protected ServerRequestInterface $request;

    protected Site $site;

    protected array $storageModule = [];

    protected array $moduleInBanner = [];

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
    public function render(): string
    {
        $this->request = $this->renderingContext->getAttribute(ServerRequestInterface::class);
        /** @var Site $site */
        $this->site = $this->request->getAttribute('site');

        $cookie = json_decode(CookieUtility::getCookieValue('BbConsentPreference'));
        $moduleName = $this->renderingContext->getVariableProvider()->get('data')['CType'];

        $data = [
            'isModule' => false,
            'placeholder_headline' => LocalizationUtility::translate('LLL:EXT:consentbanners/Resources/Private/Language/locallang.xlf:placeholderHeadline.removed.html'),
            'placeholder' => LocalizationUtility::translate('LLL:EXT:consentbanners/Resources/Private/Language/locallang.xlf:placeholder.removed.html'),
        ];

        if (!$moduleName) {
            $baseRenderingContext = $this->renderingContext->getViewHelperVariableContainer()->getView()->getRenderingContext();
            $moduleName = $baseRenderingContext->getVariableProvider()->get('data')['CType'];
        }

        if ($moduleName === 'html' && $this->renderingContext->getVariableProvider()->get('data')['ce_consent_module'] === '0') {
            $bodyText = $this->renderingContext->getVariableProvider()->get('data')['bodytext'];
            return $this->replaceExternalIframes($bodyText, $this->getPlaceholderHTML($data, false));
        }
        $removeIfrane = false;
        if ($this->hasModuleInBanners($moduleName)){

            if ($moduleName === 'html') {
                $mUid = $this->renderingContext->getVariableProvider()->get('data')['ce_consent_module'];
                if ($this->hasHtmlModuleWithId($mUid)){
                    $data = $this->getHtmlModuleById($mUid);
                }
                $removeIfrane = true;
            }else{
                $data = $this->getModuleByCType($moduleName);
            }
        }

        if ($moduleName === 'html' && $data['isModule'] === false) {
            $bodyText = $this->renderingContext->getVariableProvider()->get('data')['bodytext'];
            return $this->replaceExternalIframes($bodyText, $this->getPlaceholderHTML($data, false));
        }

        if (!is_null($cookie) && isset($data['uid'], $cookie->{$data['uid']}) && $cookie->{$data['uid']} === true) {
            return $this->renderChildren();
        }

        if ($removeIfrane){
            $bodyText = $this->renderingContext->getVariableProvider()->get('data')['bodytext'];
            return $this->replaceExternalIframes($bodyText, $this->getPlaceholderHTML($data));
        }else{
            return $this->getPlaceholderHTML($data);
        }

        return $this->renderChildren();

    }

    protected function hasModuleInBanners(string $moduleName): bool
    {


        $settingsRepository = GeneralUtility::makeInstance(SettingsRepository::class);
        $banner = $settingsRepository->findByStorageIds([$this->site->getRootPageId()]);

        if($banner && $banner->getCategories()){
            /** @var Category $category */
            foreach ($banner->getCategories() as $category){

                if($category->getModules()->count() > 0) {
                    /** @var Module $module */
                    foreach ($category->getModules() as $module) {
                        $moduleData = [
                            'isModule' => true,
                            'uid' => $module->getUid(),
                            'name' => $module->getName(),
                            'description' => $module->getDescription(),
                            'placeholder_headline' => $module->getPlaceholderHeadline(),
                            'placeholder' => $module->getPlaceholder(),
                            'module_target' => $module->getModuleTarget()];
                        if ($moduleName === 'html' && $module->getModuleTarget() === $moduleName) {
                            $this->moduleInBanner[$module->getModuleTarget()][] = 'html::'.$module->getUid();
                            $this->storageModule['module::'.$module->getUid()] = $moduleData;
                        }else{
                            // module_target is a multi-select (selectMultipleSideBySide),
                            // so it may hold a comma-separated list of CTypes. Register each
                            // target individually so single-CType lookups resolve.
                            foreach (GeneralUtility::trimExplode(',', (string)$module->getModuleTarget(), true) as $target) {
                                $this->moduleInBanner[$target] = $target;
                                $this->storageModule['module::'.$target] = $moduleData;
                            }
                        }
                    }
                }
            }
        }

        if (array_key_exists($moduleName, $this->moduleInBanner)) {
            return true;
        }

        return false;
    }

    protected function hasHtmlModuleWithId($id):bool
    {
        if (isset($this->moduleInBanner['html'])){
            return in_array('html::'.$id, $this->moduleInBanner['html']);
        }
        return false;
    }

    protected function getHtmlModuleById(int $id):array
    {
        return isset($this->storageModule['module::'.$id]) ? $this->storageModule['module::'.$id] : [];
    }

    protected function getModuleByCType(string $cType):array
    {
        return isset($this->storageModule['module::'.$cType]) ? $this->storageModule['module::'.$cType] : [];
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

    public function replaceExternalIframes(string $htmlContent, string $replacementHtml): string
    {
        // Erstelle ein DOMDocument-Objekt, um den HTML-Inhalt zu parsen
        $dom = new \DOMDocument();
        // Unterdrücke Warnungen bei fehlerhaftem HTML
        // Wichtig: Beim Speichern des modifizierten HTML kann es zu Zeichenkodierungsproblemen kommen,
        // wenn das Original-HTML nicht UTF-8 ist.
        @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $htmlContent);
        $dom->encoding = 'utf-8'; // Setze die Kodierung explizit auf UTF-8

        // Hole alle <iframe>-Elemente
        $iframes = $dom->getElementsByTagName('iframe');

        // Wir müssen die Liste der IFrames in umgekehrter Reihenfolge durchlaufen,
        // wenn wir Knoten entfernen/ersetzen, da sich sonst die NodeList-Indizes verschieben.
        // Eine robustere Methode ist es, eine Liste der zu ersetzenden Knoten zu sammeln
        // und die Ersetzung danach durchzuführen.
        $nodesToReplace = [];

        foreach ($iframes as $iframe) {
            // Überprüfe, ob das <iframe> ein 'src'-Attribut hat
            if ($iframe->hasAttribute('src')) {
                $src = $iframe->getAttribute('src');

                // Parse die URL des src-Attributs
                $srcParts = parse_url($src);

                // Wenn die URL ein Host-Teil hat
                if (isset($srcParts['host'])) {
                    $iframeDomain = $srcParts['host'];

                    // Entferne "www." von beiden Domains für einen besseren Vergleich
                    $iframeDomainClean = str_replace('www.', '', $iframeDomain);
                    $ownDomainClean = str_replace('www.', '', $this->site->getBase()->getHost());

                    // Vergleiche die Domain des IFrames mit der eigenen Domain
                    if ($iframeDomainClean !== $ownDomainClean) {
                        // Füge den IFrame-Knoten und die Ersetzungs-Information zur Liste hinzu
                        $nodesToReplace[] = [
                            'node' => $iframe,
                            'replacement_html' => $replacementHtml
                        ];
                    }
                }
                // Relative URLs (ohne Host) werden hier nicht als extern betrachtet und bleiben unverändert.
                // Wenn du relative URLs auch ersetzen möchtest, müsstest du hier eine andere Logik einfügen.
            }
        }

        // Führe die Ersetzungen durch
        foreach ($nodesToReplace as $item) {
            $node = $item['node'];
            $htmlToInsert = $item['replacement_html'];

            // Erstelle ein temporäres DOMDocument, um den HTML-String zu parsen
            // Dies ist nötig, um den HTML-String als DOM-Knoten einzufügen
            $tempDom = new \DOMDocument();
            // Hier ist es wichtig, den HTML-Inhalt in ein temporäres Element zu packen,
            // damit DOMDocument ihn korrekt als Fragment parsen kann.
            @$tempDom->loadHTML('<div>' . $htmlToInsert . '</div>');
            $fragment = $tempDom->getElementsByTagName('div')->item(0);

            // Füge die Kinder des Fragments an der Stelle des IFrame-Knotens ein
            if ($fragment) {
                $parent = $node->parentNode;
                while ($fragment->hasChildNodes()) {
                    $child = $fragment->removeChild($fragment->firstChild);
                    $parent->insertBefore($dom->importNode($child, true), $node);
                }
                // Entferne den ursprünglichen IFrame-Knoten
                $parent->removeChild($node);
            }
        }

        // Speichere den modifizierten HTML-Inhalt
        // Beachte, dass loadHTML einen DOCTYPE und body/html-Tags hinzufügen kann.
        // Wir extrahieren nur den inneren Inhalt des body-Tags, um nur das modifizierte HTML zu erhalten.
        $body = $dom->getElementsByTagName('body')->item(0);
        $modifiedHtml = '';
        if ($body) {
            foreach ($body->childNodes as $node) {
                $modifiedHtml .= $dom->saveHTML($node);
            }
        }

        return $modifiedHtml;
    }

    protected function getPlaceholderHTML(array $data,  bool $showToogle = true): string
    {
        $normalisedClassArgument = '';
        if ($this->hasArgument('class') && $this->arguments['class'] !== '') {
            $normalisedClassArgument = ' ' . $this->arguments['class'];
        }

        $normalisedAdditionalAttributes = '';
        if ($this->hasArgument('additionalAttributes')) {
            foreach ($this->arguments['additionalAttributes'] as $attribute => $value) {
                $normalisedAdditionalAttributes .= ' ' . $attribute . '="' . $value . '"';
            }
        }
        $html = '<div class="bb-consentbanner-placeholder' . $normalisedClassArgument . '"' . $normalisedAdditionalAttributes . '>';
        $html .= '<div class="bb-consentbanner-placeholder-wrapper">';
        if (!empty($data['placeholder_headline'])) {
            $html .=
                '<h3 class="bb-consentbanner-placeholder-headline">' .
                $data['placeholder_headline'] .
                '</h3>';
        }elseif (!empty($data['name'])){
            $html .=
                '<h3 class="bb-consentbanner-placeholder-headline">' .
                $data['name'] .
                '</h3>';
        }
        if (!empty($data['placeholder'])) {
            $html .=
                '<span class="bb-consentbanner-placeholder-text">' .
                $data['placeholder'] .
                '</span>';
        }
        if($showToogle && !empty($data['uid'])) {
            $html .=
                '<div class="bb-consentbanner-module" data-cookiebanner-module="' . $data['uid'] . '">
                <label class="bb-control-checkbox" aria-label="' . ($data['name'] ?? '') . '">
                    <span class="bb-control-label bb-label-module">' . ($data['name'] ?? '') . '</span>
                    <input type="checkbox" name="' . $data['uid'] . '">
                    <span class="bb-toggle"></span>
                </label>' .
//            '<p class="bb-consentbanner-description">' . $res['description'] . '</p>' .
                '</div>';
        }
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

}
