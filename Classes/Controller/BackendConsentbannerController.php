<?php
declare(strict_types=1);

namespace Bb\Consentbanners\Controller;

use Bb\Consentbanners\Domain\Repository\CategoryRepository;
use Bb\Consentbanners\Domain\Repository\ModuleRepository;
use Bb\Consentbanners\Domain\Repository\SettingsRepository;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Backend\Utility\BackendUtility;

use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Backend\Routing\UriBuilder;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;


class BackendConsentbannerController extends ActionController
{
    /**
     * @var int
     */
    protected int $current_root_pid;
    /**
     * @var int
     */
    protected int $current_sys_language;
    /**
     * @var int
     */
    protected int $default_sys_language;
    /**
     * @var string
     */
    protected string $redirect = '';
    /**
     * The module name of the backend module extending this class
     * @var string
     */
    protected string $moduleName = 'site_consentbanners';
    /**
     * The extension key of the controller extending this class
     * @var string
     */
    protected string $extKey = 'consentbanners';
    /**
     * @var array
     */
    protected array $actions = ['settings', 'categories', 'modules'];
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
    /**
     * @var SiteFinder
     */
    protected SiteFinder $siteFinder;
    /**
     * @var PageRenderer
     */
    protected PageRenderer $pageRenderer;


    /**
     * @param ModuleTemplateFactory $moduleTemplateFactory
     * @param SiteFinder $siteFinder
     * @param SettingsRepository $settingsRepository
     * @param CategoryRepository $categoryRepository
     * @param ModuleRepository $moduleRepository
     * @param IconFactory $iconFactory
     * @param PageRenderer $pageRenderer
     */
    public function __construct(
        protected readonly ModuleTemplateFactory $moduleTemplateFactory,
        SiteFinder         $siteFinder,
        protected readonly SettingsRepository $settingsRepository,
        protected readonly CategoryRepository $categoryRepository,
        protected readonly ModuleRepository   $moduleRepository,
        protected readonly IconFactory $iconFactory,
        PageRenderer $pageRenderer
    )
    {
        $this->siteFinder = $siteFinder;
        $this->pageRenderer = $pageRenderer;
    }

    /**
     * @return void
     * @throws DBALException
     * @throws Exception
     * @throws RouteNotFoundException
     */
    protected function initializeAction(): void
    {
        $params = $this->request->getQueryParams();

        if (isset($params['rootPageId'], $params['sysLanguageUid'])) {
            $this->current_root_pid = (int)$params['rootPageId'];
            $this->current_sys_language = (int)$params['sysLanguageUid'];
        }

        if (isset($params['tx_consentbanners_site_consentbanners']) && is_array($params['tx_consentbanners_site_consentbanners'])) {
            $current_action = $params['tx_consentbanners_site_consentbanners']['action'] ?? 'settings';
            $current_controller = $params['tx_consentbanners_site_consentbanners']['controller'] ?? 'BackendConsentbanner';
        } else {
            $current_action = 'settings';
            $current_controller = 'BackendConsentbanner';
        }

        $rootPageSites = $this->siteFinder->getAllSites();
        $tempRootPageSides = [];
        $rp_menu = [];
        foreach ($rootPageSites as $rootPageSite) {
            $recordRootPageSite = BackendUtility::getRecord('pages', $rootPageSite->getRootPageId(), 'uid, title, sys_language_uid');
            if (!is_null($recordRootPageSite)) {
                //Set Title from ROOT Page
                $ll_menu = [];
                $defaultLanguage = $rootPageSite->getDefaultLanguage();
                if (empty($this->current_root_pid)) {
                    $this->current_root_pid = $rootPageSite->getRootPageId();
                }
                if (empty($this->current_sys_language) && $defaultLanguage->isEnabled()) {
                    // @extensionScannerIgnoreLine
                    $this->current_sys_language = $defaultLanguage->getLanguageId();
                }

                $settingsInDefaultLanguage = $this->settingsRepository->getRecordSettingsInLanguage($rootPageSite->getRootPageId(), $defaultLanguage->getLanguageId());
                if (!is_null($settingsInDefaultLanguage) && $this->current_root_pid === $settingsInDefaultLanguage['pid'] && $defaultLanguage->isEnabled()) {

                    $rp_menu[$rootPageSite->getRootPageId()] = [
                        'isRecord' => true,
                        'title' => $recordRootPageSite['title'],
                        'uri' => $this->getBuildRoute($this->moduleName, ['tx_consentbanners_site_consentbanners' => ['action' => $current_action, 'controller' => $current_controller], 'SET' => ['language' => $defaultLanguage->getLanguageId()], 'sysLanguageUid' => $defaultLanguage->getLanguageId(), 'rootPageId' => $rootPageSite->getRootPageId()])
                    ];

                    $ll_menu[$defaultLanguage->getLanguageId()] = [
                        'isRecord' => true,
                        'uid' => $settingsInDefaultLanguage['uid'],
                        'pid' => $settingsInDefaultLanguage['pid'],
                        'sysLanguageUid' => $settingsInDefaultLanguage['sys_language_uid'],
                        'title' => $defaultLanguage->getTitle(),
                        'uri' => $this->getBuildRoute($this->moduleName, ['tx_consentbanners_site_consentbanners' => ['action' => $current_action, 'controller' => $current_controller], 'SET' => ['language' => $defaultLanguage->getLanguageId()], 'sysLanguageUid' => $defaultLanguage->getLanguageId(), 'rootPageId' => $rootPageSite->getRootPageId()])
                    ];
                    $this->default_sys_language = $defaultLanguage->getLanguageId();
                    foreach ($rootPageSite->getLanguages() as $language) {
                        if ($defaultLanguage->getLanguageId() === $language->getLanguageId()) {
                            continue;
                        }

                        if ($language->isEnabled()) {
                            $settingsInLanguage = $this->settingsRepository->getRecordSettingsInLanguage($rootPageSite->getRootPageId(), $language->getLanguageId());

                            $returnUrl = $this->getBuildRoute($this->moduleName, ['tx_consentbanners_site_consentbanners' => ['action' => $current_action, 'controller' => $current_controller], 'SET' => ['language' => $language->getLanguageId()], 'sysLanguageUid' => $language->getLanguageId(), 'rootPageId' => $rootPageSite->getRootPageId()]);

                            if (!is_null($settingsInLanguage)) {
                                $ll_menu[$language->getLanguageId()] = [
                                    'isRecord' => true,
                                    'uid' => $settingsInLanguage['uid'],
                                    'pid' => $settingsInLanguage['pid'],
                                    'sysLanguageUid' => $settingsInLanguage['sys_language_uid'],
                                    'title' => $language->getTitle(),
                                    'uri' => $this->getBuildRoute($this->moduleName, ['tx_consentbanners_site_consentbanners' => ['action' => $current_action, 'controller' => $current_controller], 'SET' => ['language' => $language->getLanguageId()], 'sysLanguageUid' => $language->getLanguageId(), 'rootPageId' => $rootPageSite->getRootPageId()])

                                ];

                            } else {
                                //Conf Create Settings in Language
                                $edit = [
                                    'edit' => [
                                        SettingsRepository::$tableName => [
                                            $rootPageSite->getRootPageId() => 'new'
                                        ]
                                    ],
                                    'defVals' => [
                                        SettingsRepository::$tableName => [
                                            $GLOBALS['TCA'][SettingsRepository::$tableName]['ctrl']['languageField'] => $language->getLanguageId(),
                                        ]
                                    ],
                                    'returnUrl' => $returnUrl
                                ];

                                $ll_menu[$language->getLanguageId()] = [
                                    'isRecord' => false,
                                    'uid' => 0,
                                    'pid' => $rootPageSite->getRootPageId(),
                                    'sysLanguageUid' => $language->getLanguageId(),
                                    'title' => $language->getTitle(),
                                    'uri' => $this->getBuildRoute('record_edit', $edit)

                                ];

                            }
                        }
                    }

                } else {
                    $new = [
                        'edit' => [
                            SettingsRepository::$tableName => [
                                $rootPageSite->getRootPageId() => 'new'
                            ]
                        ],
                        'defVals' => [
                            SettingsRepository::$tableName => [
                                $GLOBALS['TCA'][SettingsRepository::$tableName]['ctrl']['languageField'] => $defaultLanguage->getLanguageId(),
                            ]
                        ],
                        'returnUrl' => $this->getBuildRoute($this->moduleName, ['tx_consentbanners_site_consentbanners' => ['action' => $current_action, 'controller' => $current_controller], 'SET' => ['language' => $defaultLanguage->getLanguageId()], 'sysLanguageUid' => $defaultLanguage->getLanguageId(), 'rootPageId' => $rootPageSite->getRootPageId()])
                    ];

                    $newSettingsUri = $this->getBuildRoute('record_edit', $new);
                }

                $edit = [
                    'edit' => [
                        SettingsRepository::$tableName => [
                            $rootPageSite->getRootPageId() => 'new'
                        ]
                    ],
                    'defVals' => [
                        SettingsRepository::$tableName => [
                            $GLOBALS['TCA'][SettingsRepository::$tableName]['ctrl']['languageField'] => $defaultLanguage->getLanguageId(),
                        ]
                    ],
                    'returnUrl' => $this->getBuildRoute($this->moduleName, ['tx_consentbanners_site_consentbanners' => ['action' => $current_action, 'controller' => $current_controller], 'SET' => ['language' => $defaultLanguage->getLanguageId()], 'sysLanguageUid' => $defaultLanguage->getLanguageId(), 'rootPageId' => $rootPageSite->getRootPageId()])
                ];

                $tempRootPageSides[$rootPageSite->getRootPageId()] = [
                    'pid' => $rootPageSite->getRootPageId(),
                    'title' => $recordRootPageSite['title'],
                    //'defaultLanguageId' => $this->default_sys_language,
                    'currentLanguageId' => $this->current_sys_language,
                    'languageMenu' => isset($newSettingsUri) ? [] : $ll_menu,
                    'rootPageMenu' => $rp_menu,
                    'newSettingsUri' => $newSettingsUri ?? (empty($ll_menu) ? $this->getBuildRoute('record_edit', $edit) : '')
                ];
            } else {
                //FlashMassage
            }

        }

        $this->docHeaderMenu = $tempRootPageSides;
        parent::initializeAction();
    }

    /**
     * @return ResponseInterface the response with the content
     * @throws RouteNotFoundException
     */
    public function settingsAction(): ResponseInterface
    {

        $settingsData = $this->settingsRepository->findByStorageIds([(int)$this->current_root_pid], (int)$this->current_sys_language, true);
        $moduleTemplate = $this->initializeModuleTemplate($this->request);

        $this->registerAsideMenu();
        $this->registerDocHeaderButton();

        $this->createDocHeaderLanguageMenu($moduleTemplate);

        $moduleTemplate->assignMultiple([
            'data' => [
                'banner' => $settingsData,
            ],
            'moduleName' => $this->moduleName,
            'returnUrl' => $this->getBuildRoute($this->moduleName, [$this->getFullPluginName() => ['action' => $this->request->getControllerActionName(), 'controller' => $this->request->getControllerName()], 'SET' => ['language' => $this->current_sys_language], 'sysLanguageUid' => $this->current_sys_language, 'rootPageId' => $this->current_root_pid]),
            'currentRootPageId' => $this->current_root_pid,
            'currentLanguageId' => $this->current_sys_language,
            //'defaultLanguageId' => $this->default_sys_language,
            'docHeaderMenu' => $this->docHeaderMenu,
            'asideMenu' => $this->asideMenu
        ]);

        //$moduleTemplate->getDocHeaderComponent()->setMetaInformation([]);

        // Adding title, menus, buttons, etc. using $moduleTemplate ...
        //$moduleTemplate->setContent($this->view->render());
        return $moduleTemplate->renderResponse();
    }

    /**
     * @return ResponseInterface
     * @throws RouteNotFoundException
     */
    public function categoriesAction(): ResponseInterface
    {


//        $this->pageRenderer->loadRequireJsModule('TYPO3/CMS/Backend/ContextMenu');
//        $this->pageRenderer->loadRequireJsModule('TYPO3/CMS/Backend/Modal');
//        $this->pageRenderer->loadRequireJsModule('TYPO3/CMS/Backend/Tooltip');
//        $this->pageRenderer->loadRequireJsModule('TYPO3/CMS/Backend/AjaxDataHandler');
//        $this->pageRenderer->loadRequireJsModule('TYPO3/CMS/Recordlist/Recordlist');
//        $this->pageRenderer->loadRequireJsModule('TYPO3/CMS/Consentbanners/BackendFormHandler');
//        $this->pageRenderer->loadRequireJsModule('TYPO3/CMS/Consentbanners/BackendModalPrompts');

        $this->registerAsideMenu();
        $this->registerDocHeaderButton();

        $moduleTemplate = $this->initializeModuleTemplate($this->request);

        if ($this->default_sys_language === $this->current_sys_language) {
            $this->createDocHeaderButtons($moduleTemplate);
        }

        $this->createDocHeaderLanguageMenu($moduleTemplate);

        $categoriesData = $this->categoryRepository->findByStorageIds([(int)$this->current_root_pid], (int)$this->current_sys_language, true);

        foreach ($categoriesData as $category) {

            $params = [
                'data' => [
                    'tx_consentbanners_domain_model_module' => [
                        $category->getUid() => [
                            'hidden' => $category->getHidden() === 1 ? 0 : 1
                        ]
                    ]
                ],
                'redirect' => $this->redirect
            ];

            $category->setShowUri($this->getBuildRoutePath('/record/commit', $params));
        }

        $moduleTemplate->assignMultiple([
            'data' => [
                'categories' => $categoriesData
            ],
            'moduleName' => $this->moduleName,
            'returnUrl' => $this->getBuildRoute($this->moduleName, [$this->getFullPluginName() => ['action' => $this->request->getControllerActionName(), 'controller' => $this->request->getControllerName()], 'SET' => ['language' => $this->current_sys_language], 'sysLanguageUid' => $this->current_sys_language, 'rootPageId' => $this->current_root_pid]),
            'currentRootPageId' => $this->current_root_pid,
            'currentLanguageId' => $this->current_sys_language,
            'defaultLanguageId' => $this->default_sys_language,
            'docHeaderMenu' => $this->docHeaderMenu,
            'asideMenu' => $this->asideMenu
        ]);

        // Adding title, menus, buttons, etc. using $moduleTemplate ...
        return $moduleTemplate->renderResponse();
    }

    /**
     * @return ResponseInterface
     * @throws RouteNotFoundException
     */
    public function modulesAction(): ResponseInterface
    {
        $this->registerAsideMenu();
        $this->registerDocHeaderButton();

        $moduleTemplate = $this->initializeModuleTemplate($this->request);

        if ($this->default_sys_language === $this->current_sys_language) {
            $this->createDocHeaderButtons($moduleTemplate);
        }

        $this->createDocHeaderLanguageMenu($moduleTemplate);

        $modulesData = $this->moduleRepository->findByStorageIds([(int)$this->current_root_pid], (int)$this->current_sys_language, true);

        foreach ($modulesData as $module) {
            $params = [
                'data' => [
                    'tx_consentbanners_domain_model_module' => [
                        $module->getUid() => [
                            'hidden' => $module->getHidden() === 1 ? 0 : 1
                        ]
                    ]
                ],
                'redirect' => $this->redirect
            ];

            $module->setShowUri($this->getBuildRoutePath('/record/commit', $params));
        }

        $moduleTemplate->assignMultiple([
            'data' => [
                'modules' => $modulesData,
            ],
            'moduleName' => $this->moduleName,
            'returnUrl' => $this->getBuildRoute($this->moduleName, [$this->getFullPluginName() => ['action' => $this->request->getControllerActionName(), 'controller' => $this->request->getControllerName()], 'SET' => ['language' => $this->current_sys_language], 'sysLanguageUid' => $this->current_sys_language, 'rootPageId' => $this->current_root_pid]),
            'currentRootPageId' => $this->current_root_pid,
            'currentLanguageId' => $this->current_sys_language,
            'defaultLanguageId' => $this->default_sys_language,
            'docHeaderMenu' => $this->docHeaderMenu,
            'asideMenu' => $this->asideMenu
        ]);

        // Adding title, menus, buttons, etc. using $moduleTemplate ...
        return $moduleTemplate->renderResponse();
    }

    protected function initializeModuleTemplate(
        ServerRequestInterface $request
    ): ModuleTemplate {

        $this->pageRenderer->addCssFile('EXT:consentbanners/Resources/Public/Css/Backend.css');

        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $moduleTemplate->getDocHeaderComponent()->setMetaInformation([]);
        return $moduleTemplate;
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
     * Wrapper used for unit testing.
     *
     * @param string $action
     * @param array $parameters
     * @return string
     * @throws RouteNotFoundException
     */
    protected function getBuildActionRoute(string $action, array $parameters = []): string
    {
        if ($this->request) {
            $currentRequest = $this->request;
            $fullPluginName = $this->getFullPluginName();

            $parameters[$fullPluginName] = [
                'action' => $action,
                'controller' => $currentRequest->getControllerName()
            ];

            $parameters['sysLanguageUid'] = (int)$this->current_sys_language;
            $parameters['rootPageId'] = (int)$this->current_root_pid;
        }
        return $this->uriBuilder->reset()->uriFor($action, [], $currentRequest->getControllerName());
        //return $this->getBuildRoute($this->moduleName, $parameters);
    }

    /**
     * Wrapper used for unit testing.
     *
     * @param string $pathInfo
     * @param array $parameters
     * @return string
     * @throws RouteNotFoundException
     */
    protected function getBuildRoutePath(string $pathInfo, array $parameters = []): string
    {
        /** @var UriBuilder $uriBuilder */
        $backendUriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        return (string)$backendUriBuilder->buildUriFromRoutePath($pathInfo, $parameters);
    }

    /**
     * @param ModuleTemplate $moduleTemplate
     * @return void
     */
    protected function createDocHeaderButtons(ModuleTemplate $moduleTemplate): void
    {
        $buttonBar = $moduleTemplate->getDocHeaderComponent()->getButtonBar();

        foreach ($this->buttons as $key => $button) {
            if ($button === null) {
                continue;
            }

            $viewButton = $buttonBar->makeLinkButton()
                ->setHref($button['href'])
                ->setTitle($button['title'])
                ->setShowLabelText(true)
                ->setIcon($button['icon'])
                ->setDataAttributes($button['dataAttributes']);

            if (array_key_exists('classes', $button)) {
                $viewButton->setClasses($button['classes']);
            }

            if ($button['displayConditions'] === null ||
                (
                    array_key_exists($this->request->getControllerName(), $button['displayConditions']) &&
                    in_array($this->request->getControllerActionName(), $button['displayConditions'][$this->request->getControllerName()], true)
                )
            ) {
                $buttonBar->addButton($viewButton, ButtonBar::BUTTON_POSITION_LEFT, $key);
            }
        }
    }

    /**
     * @return void
     * @throws RouteNotFoundException
     */
    protected function registerAsideMenu(): void
    {
        if (!empty($this->actions)) {
            foreach ($this->actions as $actionName) {
                $uri = $this->getBuildActionRoute($actionName);
                $this->asideMenu[] = [
                    'title' => LocalizationUtility::translate('LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:action.' . $actionName . '.title'),
                    'uri' => $uri,
                    'isActive' => $this->isActionMethod($actionName)
                ];
            }
        }
    }

    /**
     * @return void
     * @throws RouteNotFoundException
     */
    protected function registerDocHeaderButton(): void
    {
        if (empty($this->buttons)) {
            $this->buttons = [
                $this->createNewRecordButton(
                    'tx_consentbanners_domain_model_category',
                    LocalizationUtility::translate('LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:action.categories.new.title'),
                    $this->getBuildActionRoute('categories'),
                    [
                        'ConsentBackend' => ['categories']
                    ],
                    true
                ),
                $this->createNewRecordButton(
                    'tx_consentbanners_domain_model_module',
                    LocalizationUtility::translate('LLL:EXT:consentbanners/Resources/Private/Language/locallang_mod.xlf:action.modules.new.title'),
                    $this->getBuildActionRoute('modules'),
                    [
                        'ConsentBackend' => ['modules']
                    ],
                    true
                )
            ];
        }
    }


    /**
     * @param $actionName
     * @return bool
     */
    protected function isActionMethod($actionName): bool
    {
        if ($this->request) {
            $currentRequest = $this->request;
            return $actionName === $currentRequest->getControllerActionName();
        }
        return false;
    }

    /**
     * Return button to create new record
     *
     * @param string $table Name of the table
     * @param string $title Title of the button
     * @param string $returnUrl Url to return to after creating new record. If defined, $returnParameter will be ignored
     * @param array $displayConditions An array configuring display conditions with key as controller name and action as array with actions
     * @param bool $showLabelText
     * @param string $iconIdentifier Name of the icon to use. If no icon is defined, the icon of the record will be used.
     * @param array $dataAttributes The data attributes to add to the button
     * @return array
     * @throws RouteNotFoundException
     */
    protected function createNewRecordButton(string $table, string $title, string $returnUrl, array $displayConditions = [], bool $showLabelText = false, string $iconIdentifier = 'actions-add', array $dataAttributes = []): array
    {

        $icon = $this->iconFactory->getIcon($iconIdentifier, Icon::SIZE_SMALL);

        $url = $this->getBuildRoute('record_edit', [
            'edit[' . $table . '][' . $this->current_root_pid . ']' => 'new',
            'returnUrl' => $returnUrl
        ]);

        return [
            'type' => 'new',
            'href' => $url,
            'title' => $title,
            'showLabelText' => $showLabelText,
            'icon' => $icon,
            'dataAttributes' => $dataAttributes,
            'displayConditions' => $displayConditions
        ];
    }

    /**
     * Return button to create new record
     *
     * @param string $table Name of the table
     * @param string $title Title of the button
     * @param int $recordId
     * @param string $returnUrl Url to return to after creating new record. If defined, $returnParameter will be ignored
     * @param array $displayConditions An array configuring display conditions with key as controller name and action as array with actions
     * @param bool $showLabelText
     * @param string $iconIdentifier Name of the icon to use. If no icon is defined, the icon of the record will be used.
     * @param array $dataAttributes The data attributes to add to the button
     * @return array
     * @throws RouteNotFoundException
     */
    protected function createEditRecordButton(string $table, string $title, int $recordId, string $returnUrl, array $displayConditions = [], bool $showLabelText = false, string $iconIdentifier = 'actions-open', array $dataAttributes = []): array
    {

        $icon = $this->iconFactory->getIcon($iconIdentifier, Icon::SIZE_SMALL);

        $url = $this->getBuildRoute('record_edit', [
            'edit[' . $table . '][' . $recordId . ']' => 'edit',
            'returnUrl' => $returnUrl
        ]);

        return [
            'type' => 'edit',
            'href' => $url,
            'title' => $title,
            'showLabelText' => $showLabelText,
            'icon' => $icon,
            'dataAttributes' => $dataAttributes,
            'displayConditions' => $displayConditions
        ];
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
     * Returns the Language Service
     *
     * @return LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
