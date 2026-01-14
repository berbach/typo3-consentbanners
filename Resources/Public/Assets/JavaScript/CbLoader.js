
import {createToggle, createElementWithAttrs, isBotAgent, typeofIsAndValueIsNot, generateUserUid, generateUserHash} from "./Lib/utils";


import Debug from './Lib/debug';
const cookieUtils = require('./Lib/cookie')
window.DEVMODE = process.env.NODE_ENV !== 'production' ?? false;

Debug.setDevMode(DEVMODE);

/**
 * @TODO
 * Check is user hash LocalStorage
 * Check is user hash in DB
 * @constructor
 */
const CbManager = function ()  {

    const CB_NAME = 'bb-consentbanner';
    const CB_PREFIX = CB_NAME + '-';
    const CB_GROUP_PREFIX = 'group-'

    const LAST_PREFERENCES_NAME = this.bannerPreferences?.cName ?? 'BbConsentPreferences';

    this.bannerPreferences = JSON.parse(document.getElementById('bbBannerData').innerHTML)

    this.userIdentificationKey = ""

    this.isBottomLayout = !typeofIsAndValueIsNot(this.bannerPreferences?.layout, 'string', 'cb-bottom');

    this.cookiePreferences = JSON.parse(cookieUtils.get(this.bannerPreferences?.cName ?? 'BbConsentPreferences'));
    this.localPreferences = localStorage.getItem(LAST_PREFERENCES_NAME) ? JSON.parse(localStorage.getItem(LAST_PREFERENCES_NAME)) : [];

    this.changePreferences = null
    this.acceptAllButton = null
    this.saveAndCloseButton = null
    this.confirmSelectionButton = null
    this.advancedSettingsButton = null
    this.showCookieInfoButton = null


    this.banner = null
    this.bannerBody = null
    this.bannerStage = null
    this.bannerMain = null
    this.bannerFooter = null


    this.init = () => {
        console.log('CBManager JavaScript loaded')


        console.log(this.getUserUid())
        console.log(this.getUserHash())


        if (isBotAgent()) {
            console.log('Set Essential Cookies')
        }

        if(this.isPreferencesCookie() && this.isPreferencesLocalStorage()){
            this.openerChangePreferences()
        }else {
            this.CreateWrapperBanner()
            this.createBanner()
        }
    }

    this.attachBannerEventListeners = () => {
        console.log(this.bannerMain)
        this.bannerMain?.addEventListener('submit', e => {
            e.preventDefault()
        })

        console.log(this.acceptAllButton)
        this.acceptAllButton?.addEventListener('click', () => {
            console.log('acceptAllButton')
            console.log(this.collectAndModifyData(true))
            this.saveConsentLog()
        })

        this.saveAndCloseButton?.addEventListener('click', () => {
            console.log('saveAndCloseButton')
            console.log(this.collectData())
        })
        console.log(this.confirmSelectionButton)
        this.confirmSelectionButton?.addEventListener('click', () => {
            console.log('confirmSelectionButton')
            console.log(this.collectData())
        })

        this.advancedSettingsButton?.addEventListener('click', () => {
            console.log('advancedSettingsButton')
            this.createBannerOverlay()
        })

        this.attachSyncToggles();
    }

    this.openerChangePreferences = () => {
        const openerType = this.bannerPreferences?.openerVariant ?? 10

        if(openerType === 10){
            const targetWrapper = document.querySelector('.bb-nav__service');
            const cloneFirstElementChild = targetWrapper.firstElementChild.cloneNode();

            cloneFirstElementChild.innerHTML = '';

            this.changePreferences = createElementWithAttrs('button', {
                className: ['bb-nav__link', 'bb-text-widget', CB_PREFIX + 'link'].join(' '),
                type: 'button',
                innerText: this.bannerPreferences?.openerData?.textLinkText ?? 'Cookie Settings'
            }, cloneFirstElementChild)

            if (this.bannerPreferences?.openerData?.textLinkPosition === 'first'){
                targetWrapper.insertBefore(cloneFirstElementChild, targetWrapper.firstElementChild);
            }
            if (this.bannerPreferences?.openerData?.textLinkPosition === 'last'){
                targetWrapper.appendChild(cloneFirstElementChild)
            }
        }

        if(openerType === 20){
            const buttonPosition = this.bannerPreferences?.openerData?.buttonWidgetPosition ?? 'left';
            const buttonText = this.bannerPreferences?.openerData?.buttonWidgetText ?? 'Cookie Settings';

            this.changePreferences = createElementWithAttrs("button", {
                className: ["bb-button-widget", CB_PREFIX + "button", "bb-button-widget-" + buttonPosition].join(' '),
                innerHTML: createElementWithAttrs("span", { innerText: buttonText}).outerHTML
            });
            //TODO Widget Position
            console.log(document.querySelector('body > div'))
            document.querySelector('body > div').insertAdjacentElement('afterend', this.changePreferences)
        }


        this.changePreferences?.addEventListener('click', () => {
            console.log('click Change Preferences');
        })
    }


    this.CreateWrapperBanner = () => {
        console.log('CreateWrapperBanner');
        this.banner = createElementWithAttrs('div', {
            className: [CB_NAME, `bb-${this.bannerPreferences?.layout ?? 'cb-bottom'}`].join(" "),
            tabindex: "-1",
            "data-nosnippet": "true"
        });

        document.querySelector('body').insertAdjacentElement('afterbegin', this.banner);
    }



    this.createBanner = (isOverlay = false) => {
        console.log('createBanner');
        this.bannerBody = createElementWithAttrs('div', {className: [CB_PREFIX + 'body'].join(' ')})
        this.bannerStage = createElementWithAttrs('div', {className: [CB_PREFIX + 'stage'].join(' ')})
        this.bannerMain = createElementWithAttrs('form', {className: [CB_PREFIX + 'main'].join(' ')})
        this.bannerFooter = createElementWithAttrs('div', {className: [CB_PREFIX + 'footer'].join(' ')})

        const bannerContent = createElementWithAttrs('div', {
            className: [
                CB_PREFIX + 'content',
                !this.isBottomLayout ? 'bb-type-scroll' : 'bb-type-dynamic'
            ].join(' ')
        })

        const bannerHeader = createElementWithAttrs('div', {className: CB_PREFIX + 'header'})

        if (this.bannerPreferences?.banner?.title !== '') {
            createElementWithAttrs('p', {
                className: CB_PREFIX + '-title',
                innerHTML: createElementWithAttrs("strong", { innerText: this.bannerPreferences?.banner?.title}).outerHTML
            }, bannerHeader)
        }

        if (this.bannerPreferences?.banner?.description !== '') {
            createElementWithAttrs('p', {
                className: CB_PREFIX + '-description',
                innerHTML: this.bannerPreferences?.banner?.description // innerHTML to decode html entities
            }, bannerHeader)
        }

        bannerContent.appendChild(bannerHeader)
        bannerContent.appendChild(this.createPreferences(isOverlay))

        this.bannerMain.appendChild(bannerContent)
        this.bannerMain.appendChild(this.createContainerButtons(isOverlay))


        this.bannerFooter.appendChild(this.createContainerFooter())

        this.bannerStage.appendChild(this.bannerMain)
        this.bannerStage.appendChild(this.bannerFooter)

        this.banner.innerHTML = `
            <!--googleoff: index-->
            ${this.bannerBody.outerHTML}
            <!--googleon: index-->
            `;

        if(document.querySelector('.' + CB_PREFIX + 'stage') == null && isOverlay === false) {
            document.querySelector('.' + CB_PREFIX + 'body').appendChild(this.bannerStage)
            document.querySelector('.' + CB_NAME).classList.add('visible')
        }
    }

    this.createBannerOverlay = () => {
        console.log('createBannerOverlay');

        this.banner.classList.remove("bb-cb-bottom", 'visible')
        this.banner.classList.add('bb-cb-overlay', 'visible')

        // if(document.querySelector('.' + CB_PREFIX + 'stage') == null) {
        //     document.querySelector('.' + CB_PREFIX + 'body').appendChild(this.bannerStage)
        //     document.querySelector('.' + CB_NAME).classList.add('visible')
        // }

    }

    this.createPreferences = (isOverlay = false) => {
        console.log('createPreferences');

        const containerPreferences = createElementWithAttrs('div', {className: [CB_PREFIX + 'preferences'].join(' ')})

        if (typeof this.bannerPreferences?.groups === "object" && Object.keys(this.bannerPreferences?.groups).length > 0) {
            const contentGroups = createElementWithAttrs('div', {
                className: [CB_PREFIX + 'groups'].join(' ')
            })

            for (let groupKey in this.bannerPreferences?.groups){
                let group = this.bannerPreferences.groups[groupKey];

                const groupComponents = createElementWithAttrs('div', {
                    className: [CB_PREFIX + 'components'].join(' ')
                })

                if (typeof group?.components === "object" && Object.keys(group?.components).length > 0){
                    for (let componentKey in group?.components){
                        let component = group?.components[componentKey];

                        groupComponents.appendChild(
                            createToggle(false, CB_PREFIX, component?.title, component?.id, component?.description,
                                {
                                    'data-group-id': component?.groupId,
                                    'data-component-title' : component?.title,
                                    checked: !!group.lockedAndActive,
                                    disabled: !!group.lockedAndActive
                                }
                            )
                        )
                    }
                }

                if(groupComponents.children.length > 0 || group.lockedAndActive) {
                    contentGroups.appendChild(
                        createToggle(
                            true, CB_PREFIX, group?.title, CB_GROUP_PREFIX + group?.id, group?.description,
                            {
                                checked: !!group.lockedAndActive,
                                disabled: !!group.lockedAndActive
                            },
                            groupComponents.children.length > 0 ? groupComponents : null
                        )
                    )
                }
            }

            containerPreferences.appendChild(contentGroups)
        }
        return containerPreferences;
    }

    this.createContainerButtons = (isOverlay = false) => {
        console.log('createButtons');

        let buttonLabels = Object.keys(this.bannerPreferences?.displayTexts?.buttons).length > 0 ? this.bannerPreferences?.displayTexts?.buttons : {}
        const containerButtons = createElementWithAttrs('div', {className: [CB_PREFIX + 'buttons'].join(' ')})
        /**
         * @TODO
         * acceptEssential Button einbauen
         * close or X Button einbauen
         */
        this.saveAndCloseButton = createElementWithAttrs('button', {
            className: ['bb-button' , 'bb-btn--typeS', 'bb-show-overlay'].join(' '),
            type: 'submit',
            innerText: buttonLabels?.saveAndClose
        }, containerButtons)
        this.acceptAllButton = createElementWithAttrs('button', {
            className: ['bb-button' , 'bb-btn--typeS', 'bb-show-all'].join(' '),
            type: 'submit',
            innerText: buttonLabels?.acceptAll
        }, containerButtons)
        this.confirmSelectionButton = createElementWithAttrs('button', {
            className: ['bb-button' , 'bb-btn--typeS', 'bb-show-bottom'].join(' '),
            type: 'submit',
            innerText: buttonLabels?.confirmSelection
        }, containerButtons)
        this.advancedSettingsButton = createElementWithAttrs('button', {
            className: ['bb-button' , 'bb-btn--typeS', 'bb-show-bottom'].join(' '),
            type: 'button',
            innerText: buttonLabels?.advancedSettings
        }, containerButtons)


        this.attachBannerEventListeners(isOverlay);
        return containerButtons;
    }

    this.createContainerFooter = () => {
        console.log('createContainerFooter');
        let buttonLabels = Object.keys(this.bannerPreferences?.displayTexts?.buttons).length > 0 ? this.bannerPreferences?.displayTexts?.buttons : {}
        let footerLinks = this.bannerPreferences?.footerNavigation.length > 0 ? this.bannerPreferences?.footerNavigation : []
        const containerFooter = createElementWithAttrs('div', {className: [CB_PREFIX + 'footer-row'].join(' ')})
        let containerFooterCell = createElementWithAttrs('div', {className: CB_PREFIX + 'footer-cell'})

        this.showCookieInfoButton = createElementWithAttrs('button', {
            className: [CB_PREFIX + '-link', 'bb-link--cookie-info'].join(' '),
            type: 'button',
            innerText: buttonLabels?.showInfo,
        }, containerFooterCell)

        containerFooter.appendChild(containerFooterCell)

        this.showCookieInfoButton?.addEventListener('click', () => {
            console.log('click show cookie infomation');
        })

        containerFooterCell = createElementWithAttrs('div', {className: CB_PREFIX + 'footer-cell'})

        const linkContainer = createElementWithAttrs('div', {className: CB_PREFIX + 'links'})
        footerLinks.forEach(link => {
            createElementWithAttrs('a', {
                className: CB_PREFIX + '-link',
                innerText: link.title,
                href: link.url
            }, linkContainer)
        })
        createElementWithAttrs('div', {className: CB_PREFIX + 'footer-cell'})
        const identificationContainer = createElementWithAttrs('div', {className: CB_PREFIX + 'uid', innerText: `User-ID: ${this.getUserUid()}`,})

        containerFooterCell.appendChild(linkContainer)

        containerFooter.appendChild(containerFooterCell)
        containerFooter.appendChild(identificationContainer)
        return containerFooter;
    }

    this.attachSyncToggles = () => {
        console.log('attachSyncToggles');
        const formPreferencesContainer = this.bannerMain.querySelector(`.${CB_PREFIX}preferences`)

        Array.from(formPreferencesContainer.querySelectorAll(`.${CB_PREFIX}component input:not(:disabled)`)).forEach(input => {

            input.addEventListener('change', () => {
                const groupId = input.dataset.groupId
                const siblingComponents = Array.from(
                    formPreferencesContainer.querySelectorAll(`.${CB_PREFIX}component input`)
                ).filter(el => el.dataset.groupId === groupId).map(el => el.checked)

                const group = document.querySelector(`.${CB_PREFIX}group input[name="${CB_GROUP_PREFIX + groupId}"]`)

                group.indeterminate = false
                if (!siblingComponents.includes(true))
                    group.checked = false
                else if (!siblingComponents.includes(false))
                    group.checked = true
                else
                    group.indeterminate = true
            })
        })

        Array.from(formPreferencesContainer.querySelectorAll(`.${CB_PREFIX}group input[name^=${CB_GROUP_PREFIX}]:not(:disabled)`)).forEach(input => {

            input.addEventListener('change', () => {
                const groupId = input.name.replace(CB_GROUP_PREFIX, '')
                Array.from(
                    formPreferencesContainer.querySelectorAll(`.${CB_PREFIX}component input`)
                )
                    .filter(el => el.dataset.groupId === groupId)
                    .forEach(component => component.checked = input.checked)
            })
        })
    }

    this.savePreferences = () => {
        console.log('savePreferences');
    }

    this.getLastPreferences = () => {
        console.log('getLastPreferences');
        let lastPreferences;
        lastPreferences = window.localStorage.getItem(LAST_PREFERENCES_NAME)
        if (lastPreferences){
            lastPreferences = JSON.parse(lastPreferences);
        }else {
            lastPreferences = {
                uid: '',
                hash: ''
            };
        }
        return lastPreferences;
    }

    this.isPreferencesCookie = () => {
        console.log('isPreferencesCookie');
        return Object.keys(this.cookiePreferences).length !== 0;
    }

    this.isPreferencesLocalStorage = () => {
        console.log('isPreferencesLocalStorage');
        return Object.keys(this.localPreferences).length !== 0;
    }
    /**
     * uuid is the generate random hash
     * @return {string}
     */
    this.getUserUid = () => {
        const lastPreferences = this.getLastPreferences();
        let uuid = lastPreferences.uuid;
        /**
         * @ToDo
         * Condition Einstellung Log Aktiviert oder Deaktiviert
         */
        if (!uuid) {
            uuid = generateUserUid();
            lastPreferences.uuid = uuid;
            window.localStorage.setItem(LAST_PREFERENCES_NAME, JSON.stringify(lastPreferences))
        }
        return uuid
    }
    /**
     * hash is the generate browser fingerprint hash
     * @return {string}
     */
    this.getUserHash = () => {
        const lastPreferences = this.getLastPreferences();
        let hash = lastPreferences.hash;
        if (!hash) {
            hash = generateUserHash();
            console.log(hash === "2e116086-dd85b-9a997-f2469-17937f5ac");
            lastPreferences.hash = hash;
            window.localStorage.setItem(LAST_PREFERENCES_NAME, JSON.stringify(lastPreferences))
        }
        return hash
    }

    // this.getUserIdentificationKey = async () => {
    //
    // }


    this.saveConsentLog = async () => {
        const url = "/consent/save"

        const consent = {
            version: 1,
            services: { analytics: false, youtube: false }
        };

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(consent)
        });

        if (!response.ok) throw new Error(`Network response was not ok. Status: ${response.status}`);

        try {
            const data = await response.json();
            if (!data.success) {
                console.error('Error saving field:', data.message);
            }
        } catch (error) {
            console.error(error.message);
        }
    }

    this.collectData = () => {
        const formPreferencesContainer = this.bannerMain.querySelector(`.${CB_PREFIX}preferences`)
        return Object.fromEntries(
            Array.from(
                formPreferencesContainer.querySelectorAll(`.${CB_PREFIX}component input[data-group-id]`)
            ).map(el => {
                return [el.name, {'title': el.dataset.componentTitle, 'consent' : el.checked}]
            })
        )
    }

    this.collectAndModifyData = (value) => {
        const data = this.collectData()
        for (let key of Object.keys(data)) data[key].consent = value

        return data
    }

}

new CbManager().init();
