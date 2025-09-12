<?php
declare(strict_types=1);

namespace Bb\ConsentBanner\Controller;

use Bb\ConsentBanner\Domain\Model\Banner;
use Bb\ConsentBanner\Domain\Repository\CategoryRepository;
use Bb\ConsentBanner\Domain\Repository\ConsentRepository;
use Bb\ConsentBanner\Domain\Repository\BannerRepository;

use Doctrine\DBAL\Driver\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\Components\Menu\Menu;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Backend\Utility\BackendUtility;

use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\IconSize;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;


class ManagementController extends ActionController
{
    /**
     * @var int
     */
    protected int $current_root_pid = 0;
    /**
     * @var int
     */
    protected int $rootPageId = 0;
    /**
     * @var int
     */
    protected int $current_sys_language = 0;
    /**
     * @var int
     */
    protected int $default_sys_language = 0;
    /**
     * @var string
     */
    protected string $redirect = '';
    /**
     * The module name of the backend module extending this class
     * @var string
     */
    protected string $moduleName = 'consentbanner_management';
    /**
     * The extension key of the controller extending this class
     * @var string
     */
    protected string $extKey = 'consentbanner';
    /**
     * @var array
     */
    protected array $actions = ['banner', 'consents', 'delete'];
    /**
     * @var array
     */
    protected array $docHeaderMenu = [];
    /**
     * @var array
     */
    protected array $asideMenu = [];
    /**
     * The buttons for the backend module
     *
     * For each button, provide an array with these keys:
     * ['table' => 'table_name', 'label' => 'Button Label', 'action' => 'actionName', 'controller' => 'ControllerName']
     *
     * @var array
     */
    protected array $buttons = [];
    /**
     * @var Site[]
     */
    protected array $sites = [];
    /**
     * @var SiteLanguage[]
     */
    protected array $languages;

    protected  $banner = null;


    /**
     * @param SiteFinder $siteFinder
     * @param PageRenderer $pageRenderer
     * @param BannerRepository $bannerRepository
     * @param ConsentRepository $consentRepository
     * @param ModuleTemplateFactory $moduleTemplateFactory
     * @param IconFactory $iconFactory
     * @param LanguageServiceFactory $languageServiceFactory
     */
    public function __construct(
        protected readonly SiteFinder $siteFinder,
        protected readonly PageRenderer $pageRenderer,
        protected readonly BannerRepository $bannerRepository,
        protected readonly ConsentRepository   $consentRepository,
        protected readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly IconFactory $iconFactory,
        private readonly LanguageServiceFactory $languageServiceFactory,
    )
    {}

    private function initializeRootPages():void
    {
        $sites = $this->siteFinder->getAllSites();
        foreach ($sites as $site) {
            $this->sites[$site->getRootPageId()] = $site;
        }

        ksort($this->sites, SORT_NATURAL);

        if($this->request->hasArgument('site')){
            $this->rootPageId = (int)$this->request->getArgument('site');
        }else{
            $firstKey = array_key_first($this->sites);
            $this->rootPageId = $this->sites[$firstKey]->getRootPageId();
        }
    }


    /**
     * @return void
     * @throws RouteNotFoundException
     * @throws \Doctrine\DBAL\Exception
     */
    protected function initializeAction(): void
    {
        $this->initializeRootPages();
        $params = $this->request->getQueryParams();


        $this->banner = $this->bannerRepository->findByRootPageId($this->rootPageId, $this->current_sys_language);

        $rootPageSites = $this->siteFinder->getAllSites();
        $tempRootPageSides = [];
        $rp_menu = [];
        foreach ($rootPageSites as $rootPageSite) {
            $recordRootPageSite = BackendUtility::getRecord('pages', $rootPageSite->getRootPageId(), 'uid, title, sys_language_uid');
        }

        parent::initializeAction();
    }

    /**
     * @return ResponseInterface the response with the content
     */
    public function bannerAction(): ResponseInterface
    {

        $moduleTemplate = $this->initializeModuleTemplate($this->request);

        $moduleTemplate->assignMultiple([
            'data' => $this->banner,
            'moduleName' => $this->moduleName,
            'defaultValues' => $this->getDefVals(),
            'returnUrl' => $this->uriBuilder->reset()->uriFor($this->request->getControllerActionName(), ['site' => $this->rootPageId, 'language' => 0], $this->request->getControllerName()),
            'currentRootPageId' => $this->rootPageId,
            'currentLanguageId' => $this->current_sys_language,
        ]);

        return $moduleTemplate->renderResponse('Management/Banner');
    }


    /**
     * @throws RouteNotFoundException
     */
    public function consentsAction(): ResponseInterface
    {
        $consentData = $this->consentRepository->findByStorageIds([$this->rootPageId], (int)$this->current_sys_language, true);
        $moduleTemplate = $this->initializeModuleTemplate($this->request);

        $moduleTemplate->assignMultiple([
            'data' => [
                'consents' => [],
            ],
            'moduleName' => $this->moduleName,
            'returnUrl' => $this->uriBuilder->reset()->uriFor($this->request->getControllerActionName(), ['site' => $this->rootPageId, 'language' => 0], $this->request->getControllerName()),
            'rootPageId' => $this->rootPageId,
            'currentLanguageId' => $this->current_sys_language,
            //'defaultLanguageId' => $this->default_sys_language,
        ]);


        return $moduleTemplate->renderResponse('Management/Consents');
    }


    /**
     * @param ServerRequestInterface $request
     * @return ModuleTemplate
     */
    protected function initializeModuleTemplate(ServerRequestInterface $request): ModuleTemplate
    {

        $view = $this->moduleTemplateFactory->create($request);
        $this->pageRenderer->addCssFile('EXT:consent_banner/Resources/Public/Css/Backend.css');

        $titleComponents = ['title' => '', 'context' => ''];
        $this->modifyDocHeaderComponent($view, $titleComponents);
        $view->setTitle(
            $titleComponents['title'],
            $titleComponents['context'],
        );

        return $view;
    }



    /**
     * Wrapper used for unit testing.
     *
     * @param string $route
     * @param array $parameters
     * @return string
     * @throws RouteNotFoundException
     */
    protected function getBuildRoute(string $route, array $parameters = []): string
    {
        /** @var UriBuilder $uriBuilder */
        $backendUriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        return (string)$backendUriBuilder->buildUriFromRoute($route, $parameters);
    }

    /**
     * @param $actionName
     * @return bool
     */
    protected function isActionMethod($actionName): bool
    {
        return $actionName === $this->request->getControllerActionName();
    }


    /**
     * @return string
     */
    private function getFullPluginName(): string
    {
        $extensionKey = str_replace('_', '', $this->extKey);
        return 'tx_' . $extensionKey . '_' . strtolower($this->moduleName);
    }

    /**
     * @return array<string,scalar>|false
     */
    public function getMetaInformation(): array|false
    {
        $permissionClause = $GLOBALS['BE_USER']->getPagePermsClause(Permission::PAGE_SHOW);
        return [];
//        return BackendUtility::readPageAccess(
//            $this->pageUid,
//            $permissionClause,
//        );
    }


    /**
     * @param ModuleTemplate $view
     * @param string[] $titleComponents
     * @return void
     */
    private function modifyDocHeaderComponent(ModuleTemplate $view, array &$titleComponents): void
    {
        $menu = $this->buildMenuActions($view, $titleComponents);
        $view->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
        $menu = $this->buildMenuSites($view, $titleComponents);
        $view->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);

        $buttonBar = $view->getDocHeaderComponent()->getButtonBar();
        $this->addButtons($buttonBar);

        $metaInformation = $this->getMetaInformation();
        if (is_array($metaInformation)) {
            $view->getDocHeaderComponent()->setMetaInformation($metaInformation);
        }
    }

    public function addButtons(ButtonBar $buttonBar): void
    {
        if (is_null($this->banner)) {
            $this->addCreateConsentBannerButton($buttonBar);
        }
    }

    public function addCreateConsentBannerButton(ButtonBar $buttonBar): void
    {
        $new = [
            'edit' => [
                BannerRepository::TABLE_NAME => [
                    $this->rootPageId => 'new'
                ]
            ],
            'defVals' => $this->getDefVals(),
            'returnUrl' => $this->uriBuilder->reset()->uriFor($this->request->getControllerActionName(), ['site' => $this->rootPageId, 'language' => 0], $this->request->getControllerName()),
        ];

        $createButton = $buttonBar->makeLinkButton()
            ->setHref($this->getBuildRoute('record_edit', $new))
            ->setTitle($this->getLanguageService()->sL('LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:action.banner.new.title'))
            ->setShowLabelText(true)
            ->setIcon($this->iconFactory->getIcon('actions-plus', IconSize::SMALL));
        $buttonBar->addButton($createButton);
    }

    private function getDefVals(): array
    {
        return [BannerRepository::TABLE_NAME => ['pid' => $this->rootPageId, $GLOBALS['TCA'][BannerRepository::TABLE_NAME]['ctrl']['languageField'] => 0]];
    }

    private function buildMenuActions(ModuleTemplate $view, array &$titleComponents): Menu
    {
        $menuItems = [
            'banner' => [
                'controller' => 'Management',
                'controllerArguments' => ['site' => $this->rootPageId],
                'action' => 'banner',
                'label' => $this->getLanguageService()->sL('LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:banner.headline'),
            ],
            'consents' => [
                'controller' => 'Management',
                'controllerArguments' => ['site' => $this->rootPageId],
                'action' => 'consents',
                'label' => $this->getLanguageService()->sL('LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:consents.headline'),
            ],
        ];

        $menu = $view->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('ManagementBannerMenu');
        foreach ($menuItems as $menuItemConfig) {
            $isActive = $this->request->getControllerActionName() === $menuItemConfig['action'];
            $menuItem = $menu->makeMenuItem()
                ->setTitle($menuItemConfig['label'])
                ->setHref($this->uriBuilder->reset()->uriFor(
                    $menuItemConfig['action'],
                    $menuItemConfig['controllerArguments'],
                    $menuItemConfig['controller'],
                ))
                ->setActive($isActive);
            $menu->addMenuItem($menuItem);
            if ($isActive) {
                $titleComponents['title'] = $menuItemConfig['label'];
            }
        }
        return $menu;
    }

    private function buildMenuSites(ModuleTemplate $view, array &$titleComponents): Menu
    {
        $menuItems = [];
        foreach ($this->sites as $site) {
            $menuItems['rootPageId::'.$site->getRootPageId()] = [
                'controller' => 'Management',
                'controllerArguments' => ['site' => $site->getRootPageId()],
                'action' => $this->request->getControllerActionName(),
                'label' => strtoupper($site->getIdentifier()),
            ];
        }

        $menu = $view->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('ManagementRootPagesMenu');
        foreach ($menuItems as $menuItemConfig) {
            $isActive = $this->rootPageId === (int)$menuItemConfig['controllerArguments']['site'];
            $menuItem = $menu->makeMenuItem()
                ->setTitle($menuItemConfig['label'])
                ->setHref($this->uriBuilder->reset()->uriFor(
                    $menuItemConfig['action'],
                    $menuItemConfig['controllerArguments'],
                    $menuItemConfig['controller'],
                ))
                ->setActive($isActive);
            $menu->addMenuItem($menuItem);
            if ($isActive) {
                $titleComponents['context'] = $menuItemConfig['label'];
            }
        }
        return $menu;
    }

    /**
     * Create menu for backend module
     * @param ModuleTemplate $moduleTemplate
     * @return void
     */
    protected function createDocHeaderLanguageMenu(ModuleTemplate $moduleTemplate): void
    {
        $menu = $moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('actionLanguages');

        foreach ($this->docHeaderMenu[$this->current_root_pid]['languageMenu'] as $menuItem) {
            $item = $menu->makeMenuItem()
                ->setTitle($menuItem['title'])
                ->setHref($menuItem['uri'])
                ->setActive($this->current_sys_language === $menuItem['sysLanguageUid']);
            $menu->addMenuItem($item);
        }

        $moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
    }

    protected function getLanguageService(): LanguageService
    {
        return $this->languageServiceFactory->createFromUserPreferences($GLOBALS['BE_USER']);
    }
}
