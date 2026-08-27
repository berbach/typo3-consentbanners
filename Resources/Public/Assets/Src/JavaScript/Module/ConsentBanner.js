const cookieUtils = require('../Lib/cookie')

const cbPrefix = 'bb-consentbanner-';
const categoryPrefix = 'bb-consentbanner-';

// Elemente, die per Tastatur erreichbar sind (fuer Fokus-Falle und Fokussteuerung)
const focusableSelector = [
    'a[href]',
    'button:not([disabled])',
    'input:not([disabled])',
    'select:not([disabled])',
    'textarea:not([disabled])',
    '[tabindex]:not([tabindex="-1"])'
].join(', ')

// laufende Nummer fuer eindeutige IDs (aria-labelledby / aria-describedby)
let elementIdCounter = 0
const nextElementId = (suffix) => `${cbPrefix}${suffix}-${++elementIdCounter}`

// IE has no Array.from :((
// DON'T USE! BAD POLYFILL
if (!('from' in Array))
    Array.from = function (entries) {
        const array = []
        for (let i = 0; i < entries.length; i++)
            array.push(entries[i])
        return array
    }

// IE has no Array.prototype.includes :((
if (!('includes' in Array.prototype))
    Array.prototype.includes = function (searchElement, fromIndex) {
        return this.indexOf(searchElement, fromIndex) !== -1;
    }

// IE has no Object.fromEntries :((
// DON'T USE! BAD POLYFILL
if (!('fromEntries' in Object))
    Object.fromEntries = function (entries) {
        const obj = {};
        Array.from(entries).forEach(entry => {
            obj[entry[0]] = entry[1]
        })
        return obj;
    }

/**
 * @typedef {Object} ConsentBannerButtonsDisplayNames
 * @property {string} name
 * @property {string} acceptAll
 * @property {string} saveAndClose
 * @property {string} confirmSelection
 * @property {string} reject
 * @property {string} advancedSettings
 * */

/**
 * @typedef {Object} ConsentBannerCategoryData
 * @property {number} uid
 * @property {string} name
 * @property {string} description
 * @property {boolean} lockedAndActive
 * */

/**
 * @typedef {Object} ConsentBannerData
 * @property {string} layoutType
 * @property {boolean} showCategories
 * @property {boolean} isTextLink
 * @property {string} cName
 * @property {number} confirmDuration
 * @property {string} title
 * @property {string} description
 * @property {Object} privacyPage
 * @property {string} closeBtn
 * @property {string} widgetBtn
 * @property {ConsentBannerButtonsDisplayNames} buttonsDisplayNames
 * @property {ConsentBannerCategoryData[]} categories
 * @property {Object[]} modules
 * */

/**
 * @const bbConsentBanner
 * @type {ConsentBannerData}
 * */

// needed for the types to work properly
bbConsentBanner = bbConsentBanner || null
let bbConsentBannerCookieName = ''

function ConsentBanner(node) {
    const typeofIsAndValueIsNot = (variable, type, value) => typeof variable === type && variable !== value
    // Data
    this.bbConsentBanner = typeofIsAndValueIsNot(bbConsentBanner, 'object', '') ? bbConsentBanner : null;
    this.cookieName = typeofIsAndValueIsNot(this.bbConsentBanner.cName, 'string', '') ? bbConsentBanner.cName : 'BbConsentPreference';
    bbConsentBannerCookieName = this.cookieName
    this.confirmDuration = typeofIsAndValueIsNot(this.bbConsentBanner.confirmDuration, 'number', 0) ? this.bbConsentBanner.confirmDuration : 20;
    this.categories = typeof bbConsentBanner.categories === 'object' && bbConsentBanner.categories.length !== 0 ? bbConsentBanner.categories : null;
    this.modules = typeof bbConsentBanner.modules === 'object' && bbConsentBanner.modules.length !== 0 ? bbConsentBanner.modules : null;
    this.isBottomLayout = !typeofIsAndValueIsNot(this.bbConsentBanner.layoutType, 'string', 'bb-cb-bottom');
    // Elements
    this.banner = null
    this.form = null
    this.acceptButton = null
    this.saveButton = null
    this.moreButton = null
    this.confirmButton = null
    this.rejectButton = null
    // Element, das den Banner geoeffnet hat - dorthin geht der Fokus beim Schliessen zurueck
    this.lastTrigger = null

    this.preferences = JSON.parse(cookieUtils.get(this.cookieName));

    /**
     * Alle sichtbaren, per Tastatur erreichbaren Elemente innerhalb des Banners.
     * @returns {HTMLElement[]}
     */
    this.getFocusableElements = () => Array.from(this.banner?.querySelectorAll(focusableSelector) ?? [])
        .filter(el => el.offsetWidth > 0 || el.offsetHeight > 0 || el.getClientRects().length > 0)

    /**
     * Der Banner blockiert die Seite nur im Overlay-Layout - nur dann darf der
     * Fokus festgehalten werden, sonst waere die Seite nicht mehr navigierbar.
     */
    this.isModal = () => this.banner?.classList.contains('bb-cb-overlay') === true

    /** Setzt den Fokus in den Banner, damit Screenreader Titel und Text vorlesen. */
    this.focusBanner = () => {
        if (!this.banner)
            return
        // Fokus erst nach dem Rendern setzen, sonst ignorieren manche Browser den Aufruf
        window.setTimeout(() => this.banner.focus(), 0)
    }

    /**
     * Haelt den Tastaturfokus im modalen Banner (Fokus-Falle) und behandelt Escape.
     * @param {KeyboardEvent} e
     */
    this.handleKeydown = (e) => {
        if (e.key === 'Escape' || e.key === 'Esc') {
            const closeButton = this.form?.querySelector(`.${cbPrefix}close`)
            // Escape spiegelt nur den sichtbaren Schliessen-Button - im Bottom-Layout gibt es keinen
            if (closeButton && closeButton.offsetParent !== null) {
                e.preventDefault()
                closeButton.click()
            }
            return
        }

        if (e.key !== 'Tab' || !this.isModal())
            return

        const focusable = this.getFocusableElements()
        if (focusable.length === 0)
            return

        const first = focusable[0]
        const last = focusable[focusable.length - 1]

        if (!e.shiftKey && e.target === last) {
            e.preventDefault()
            first.focus()
        } else if (e.shiftKey && (e.target === first || e.target === this.banner)) {
            e.preventDefault()
            last.focus()
        }
    }

    /** Macht den Banner fuer Screenreader als Dialog erkennbar. */
    this.applyDialogSemantics = (labelId, descriptionId) => {
        if (!this.banner)
            return

        this.banner.setAttribute('role', 'dialog')
        this.banner.setAttribute('tabindex', '-1')

        if (this.isModal())
            this.banner.setAttribute('aria-modal', 'true')
        else
            this.banner.removeAttribute('aria-modal')

        if (labelId)
            this.banner.setAttribute('aria-labelledby', labelId)
        if (descriptionId)
            this.banner.setAttribute('aria-describedby', descriptionId)
    }

    this.init = () => {
        if (this.bbConsentBanner.isTextLink === false && node.classList.contains("bb-text-widget")) {
            return false;
        }

        if (node.classList.contains("bb-consentbanner")){
            this.banner = node;
        }else if (node.classList.contains("bb-text-widget") && this.bbConsentBanner.isTextLink === true) {
            this.widget = node;
            if(Object.keys(this.preferences).length !== 0){
                node = createElementWithAttrs("div", {
                    className: ["bb-consentbanner", `${this.bbConsentBanner.layoutType}`].join(" ")
                });
                this.banner = node;
            }else {
                this.banner = document.querySelector('.bb-consentbanner');
            }
        }else if (node.classList.contains("bb-widget")) {
            this.widget = node;
            node = createElementWithAttrs("div", {
                className: ["bb-consentbanner", `${this.bbConsentBanner.layoutType}`].join(" ")
            });
            this.banner = node;
        } else if (this.bbConsentBanner.isTextLink === false) {
            // Der Button hat keinen Text (nur ein Hintergrundbild) und braucht
            // deshalb einen zugaenglichen Namen.
            const widgetLabel = this.bbConsentBanner.widgetBtn
            this.widget = createElementWithAttrs("button", {
                className: ["bb-widget", cbPrefix + "button"].join(" "),
                type: "button",
                ...(widgetLabel ? {title: widgetLabel, 'aria-label': widgetLabel} : {})
            });
        }

        if (this.bbConsentBanner === null || this.categories === null || this.modules === null) {
            let warn = '';
            if (this.bbConsentBanner === null) warn += 'Consent banner, '
            if (this.categories === null) warn += 'Categories '
            if (this.modules === null) warn += 'and Modules '
            warn += 'data empty, Consent Banner not initialised!';
            console.warn(warn);
            return false
        }

        this.widget?.addEventListener('click', () => {
            // Fokus geht beim Schliessen wieder auf das ausloesende Element zurueck
            this.lastTrigger = this.widget

            if (this.form === null)
                this.generateBanner()
            else
                this.form.parentElement.classList.add('visible')

            this.moreButton.click()

            Object.keys(this.preferences).forEach(module => {
                const moduleToggle = this.form.querySelector(`.${cbPrefix}module input[name="${module}"]`)
                if (this.preferences[module] !== moduleToggle.checked)
                    moduleToggle.click()
            })
        })

        if (Object.keys(this.preferences).length === 0)
            this.generateBanner();
        else if (this.bbConsentBanner.isTextLink === false && this.widget.classList.contains("bb-widget")) {
            node.insertAdjacentElement('afterend', this.widget)
        }
    }

    this.attachBannerEventListeners = () => {
        this.form?.addEventListener('submit', e => {
            e.preventDefault()
        })

        // saves cookie preferences as a cookie
        this.saveButton?.addEventListener('click', () => setCookieAndReload(collectData()))
        this.confirmButton?.addEventListener('click', () => setCookieAndReload(collectData()))

        // saves cookie preferences (sets all to true) as a cookie
        this.acceptButton?.addEventListener('click',
            () => setCookieAndReload(collectAndModifyData(true))
        )

        // saves cookie preferences (sets all to false) as a cookie
        this.rejectButton?.addEventListener('click',
            () => setCookieAndReload(collectAndModifyData(false))
        )

        // expands the cookie banner to show the toggles
        this.moreButton?.addEventListener('click', () => {
            // remove unneeded buttons
            this.moreButton.remove()
            this.confirmButton?.remove()
            // show save button
            this.saveButton.classList.remove('hidden')


            // force overlay layout
            this.banner.classList.remove('bb-cb-bottom')
            this.banner.classList.add('bb-cb-overlay')
            // ab hier blockiert der Banner die Seite -> als modaler Dialog auszeichnen
            this.banner.setAttribute('aria-modal', 'true')

            // convert one-click buttons to secondary
            const convertToSecondary = (button) => {
                button?.classList.remove('bb-btn--typeP')
                button?.classList.add('bb-btn--typeS')
            }
            convertToSecondary(this.acceptButton)
            convertToSecondary(this.rejectButton)

            // make content scrollable
            this.form.querySelector(`.${cbPrefix}content`).classList.remove('bb-type-dynamic')
            this.form.querySelector(`.${cbPrefix}content`).classList.add('bb-type-scroll')

            // show options
            Array.from(this.form.querySelectorAll(`.${cbPrefix}category-modules.hidden`)).forEach(modules => {
                modules.classList.remove('hidden')
            })
            this.form.querySelector(`.${cbPrefix}categories`).classList.remove('hidden')

            this.form.querySelector(`.${cbPrefix}buttons`).classList.remove('not-categories')
            this.form.querySelector(`.${cbPrefix}buttons`).classList.add('is-categories')

            // Der ausloesende Button wurde entfernt - der Fokus muss auf die nun
            // sichtbaren Einstellungen wandern, sonst landet er auf <body>.
            const firstToggle = this.form.querySelector(`.${cbPrefix}category input:not([disabled])`)
            ;(firstToggle ?? this.saveButton)?.focus()
        })

        // closes the banner
        this.form.querySelector(`.${cbPrefix}close`)?.addEventListener('click', () => {
            if (Object.keys(this.preferences).length === 0) {
                setCookieAndReload(collectAndModifyData(false))
            } else {
                this.form.parentElement.classList.remove('visible')
                // Fokus zurueck auf das ausloesende Element, sonst verliert er sich
                this.lastTrigger?.focus()
            }
        })

        // Fokus im modalen Banner halten und Escape behandeln
        this.banner?.addEventListener('keydown', this.handleKeydown)

        const collectData = () => Object.fromEntries(
            Array.from(
                this.form.querySelectorAll(`.${cbPrefix}module input:not(:disabled)`)
            ).map(el => {
                return [el.name, el.checked]
            })
        )

        const collectAndModifyData = (value) => {
            const data = collectData()
            for (let key of Object.keys(data)) data[key] = value
            setCookieAndReload(data)
            return data
        }

        const setCookieAndReload = (data) => {
            cookieUtils.set(this.cookieName, JSON.stringify(data) + ';secure;samesite=lax', this.confirmDuration)
            window.location.reload()
        }

        // syncs the category toggle if one of its module toggles gets changed
        Array.from(this.form.querySelectorAll(`.${cbPrefix}module input:not(:disabled)`)).forEach(input => {
            // document.documentMode === 11 := special case for IE11
            input.addEventListener(document.documentMode === 11 ? 'change' : 'input', () => {
                const categoryID = input.dataset.category
                const siblingStates = Array.from(
                    document.querySelectorAll(`.${cbPrefix}module input`)
                ).filter(el => el.dataset.category === categoryID).map(el => el.checked)

                const category = document.querySelector(`.${cbPrefix}category input[name="${categoryPrefix + categoryID}"]`)

                category.indeterminate = false
                if (!siblingStates.includes(true))
                    category.checked = false
                else if (!siblingStates.includes(false))
                    category.checked = true
                else
                    category.indeterminate = true
            })
        })

        // syncs all module toggles of a category if the category toggle gets changed
        Array.from(this.form.querySelectorAll(`.${cbPrefix}category input[name^=${categoryPrefix}]:not(:disabled)`)).forEach(input => {
            // document.documentMode === 11 := special case for IE11
            input.addEventListener(document.documentMode === 11 ? 'change' : 'input', () => {
                const categoryID = input.name.replace(categoryPrefix, '')
                Array.from(
                    document.querySelectorAll(`.${cbPrefix}module input`)
                )
                    .filter(el => el.dataset.category === categoryID)
                    .forEach(module => module.checked = input.checked)
            })
        })
    }

    this.generateBanner = () => {
        if(Object.keys(this.preferences).length !== 0 && this.bbConsentBanner.isTextLink === false) {
            this.widget.insertAdjacentElement('beforebegin', node)
        }else if(Object.keys(this.preferences).length !== 0 && this.bbConsentBanner.isTextLink === true){
            document.querySelector('main + footer').insertAdjacentElement('afterend', node)
        }
        const _el = createElementWithAttrs
        this.form = _el('form', {className: [cbPrefix + 'body'].join(' ')})

        const formHeader = _el('div', {className: cbPrefix + 'header'})
        _el('button', {
            className: cbPrefix + 'close',
            // type="button" verhindert, dass Enter das Formular abschickt
            type: 'button',
            title: this.bbConsentBanner.closeBtn,
            'aria-label': this.bbConsentBanner.closeBtn
        }, formHeader)

        let headingId = ''
        if (this.bbConsentBanner.title !== '') {
            headingId = nextElementId('heading')
            _el('h3', {
                className: cbPrefix + '-heading',
                id: headingId,
                innerText: this.bbConsentBanner.title
            }, formHeader)
        }

        this.form.appendChild(formHeader);
        const formContent = _el('div', {
            className: [
                cbPrefix + 'content',
                !this.isBottomLayout ? 'bb-type-scroll' : 'bb-type-dynamic',
                this.bbConsentBanner.showCategories ? 'is-categories' : undefined
            ].join(' ')
        })

        let descriptionId = ''
        if (this.bbConsentBanner.description !== '') {
            descriptionId = nextElementId('text')
            _el('p', {
                className: cbPrefix + '-text',
                id: descriptionId,
                innerHTML: this.bbConsentBanner.description // innerHTML to decode html entities
            }, formContent)
        }

        if (typeof this.bbConsentBanner.categories === "object" && this.bbConsentBanner.categories.length > 0) {
            const contentCategories = _el('div', {
                className: [cbPrefix + 'categories', this.bbConsentBanner.showCategories ? undefined : 'hidden'].join(' ')
            })

            this.bbConsentBanner.categories?.forEach(category => {
                const categoryModules = _el('div', {
                    className: [cbPrefix + 'category-modules', 'hidden'].join(' '),
                    role: 'group',
                    'aria-label': category.name
                })
                const modules = this.bbConsentBanner.modules?.filter(module => module.category.uid === category.uid)

                modules?.forEach(module => {
                    categoryModules.appendChild(
                        createToggle(false, module.name, module.uid, module.description,
                            {
                                'data-category': module.category.uid,
                                checked: !!category.lockedAndActive,
                                disabled: !!category.lockedAndActive
                            }
                        )
                    )
                })
                contentCategories.appendChild(
                    createToggle(
                        true, category.name, categoryPrefix + category.uid, category.description,
                        {
                            checked: !!category.lockedAndActive,
                            disabled: !!category.lockedAndActive
                        },
                        typeof modules === "object" && categoryModules.children.length > 0 ? categoryModules : null
                    )
                )
            })

            formContent.appendChild(contentCategories)
        }
        this.form.appendChild(formContent)

        const formFooter = _el('div', {className: cbPrefix + 'footer'})
        const buttonContainer = _el('div', {
            className: [
                cbPrefix + 'buttons',
                this.bbConsentBanner.showCategories ? 'is-categories' : 'not-categories'
            ].join(' ')
        })

        const displayNames = this.bbConsentBanner.buttonsDisplayNames

        // always render accept-button
        this.acceptButton = _el('button', {
            className: ['bb-button', 'bb-btn--typeP'].join(' '),
            type: 'submit',
            innerText: displayNames.acceptAll,
        }, buttonContainer)

        // always render save-button; hide at first, show in advanced settings
        this.saveButton = _el('button', {
            className: ['bb-button', 'bb-btn--typeP', 'hidden'].join(' '),
            type: 'submit',
            innerText: displayNames.saveAndClose,
        }, buttonContainer)

        // show more-BUTTON only when not in bottom layout, otherwise link (see below)
        if (!this.isBottomLayout) {
            this.moreButton = _el('button', {
                className: ['bb-button', 'bb-btn--typeS'].join(' '),
                type: 'button',
                innerText: displayNames.advancedSettings,
            }, buttonContainer)
        }
        // show confirm-button in bottom layout (save button with different label)
        if (this.isBottomLayout && this.bbConsentBanner.showCategories) {
            this.confirmButton = _el('button', {
                className: ['bb-button', 'bb-btn--typeS'].join(' '),
                type: 'submit',
                innerText: displayNames.confirmSelection,
            }, buttonContainer)
        }
        // show reject-button only when no options are visible at first
        if (!this.bbConsentBanner.showCategories) {
            this.rejectButton = _el('button', {
                className: ['bb-button', 'bb-btn--typeP'].join(' '),
                type: 'button',
                innerText: displayNames.reject,
            }, buttonContainer)
        }
        formFooter.appendChild(buttonContainer)

        // show more-LINK only in bottom layout, otherwise button (see above)
        if (this.isBottomLayout) {
            const linkContainer = _el('div', {className: cbPrefix + 'links'})

            this.moreButton = _el('button', {
                className: [cbPrefix + '-link'].join(' '),
                type: 'button',
                innerText: displayNames.advancedSettings,
            }, linkContainer)


            _el('a', {
                className: cbPrefix + '-link',
                innerText: this.bbConsentBanner.privacyPage.label,
                href: this.bbConsentBanner.privacyPage.uri
            }, linkContainer)

            formFooter.appendChild(linkContainer)
        }

        this.form.appendChild(formFooter)

        this.attachBannerEventListeners()
        if(document.querySelector('.bb-consentbanner-body') == null) {
            document.querySelector('.bb-consentbanner').appendChild(this.form)
            document.querySelector('.bb-consentbanner').classList.add('visible')
        }

        this.applyDialogSemantics(headingId, descriptionId)
        if (this.lastTrigger === null)
            this.focusBanner()
    }
}

let initiated = false;

export function init(node) {
    let CB = new ConsentBanner(node)
    CB.init();

    if (initiated)
        return
    initiated = true

    document.querySelectorAll('[data-cookiebanner-module]').forEach(toggleBox => {
        toggleBox.querySelector('input').addEventListener('click', () => {
            // also toggle the other toggles with this module id
            document.querySelectorAll(`[data-cookiebanner-module="${toggleBox.dataset.cookiebannerModule}"] input`)
                .forEach(input => input.checked = true)

            // update preferences cookie
            const preferences = JSON.parse(cookieUtils.get(bbConsentBannerCookieName))
            preferences[toggleBox.dataset.cookiebannerModule] = true
            cookieUtils.set(bbConsentBannerCookieName, JSON.stringify(preferences) + ';secure;samesite=lax', this.confirmDuration)

            // reload page to apply preferences
            setTimeout(() => window.location.reload(), 100)
        })
    })
}

function createElementWithAttrs(tag, attrs, appendTo) {
    const el = document.createElement(tag)
    for (const key in attrs) {
        if (!attrs.hasOwnProperty(key)) continue

        if (key === 'innerText')
            el.innerText = attrs[key]
        else if (key === 'innerHTML')
            el.innerHTML = attrs[key]
        else {
            if (key in el)
                el[key] = attrs[key]
            else
                el.setAttribute(
                    key === 'className' ? 'class' : key,
                    attrs[key]
                )
        }
    }
    if (appendTo)
        appendTo.appendChild(el)
    return el;
}

function createToggle(isCategory, label, inputName, description, inputAttributes, appendModules) {
    isCategory = isCategory ?? false
    label = label ?? ''
    inputName = inputName ?? ''
    description = description ?? ''
    inputAttributes = inputAttributes ?? {}
    appendModules = appendModules ?? false

    const _el = createElementWithAttrs

    const main = _el('div', {
        className: [
            cbPrefix + (isCategory ? 'category' : 'module')
        ].join(' ')
    })

    const labelEl = _el('label', {
        className: 'bb-control-checkbox',
        'aria-label': label
    })

    // Beschreibung mit der Checkbox verknuepfen, damit Screenreader sie mitlesen
    const descriptionId = description ? nextElementId('description') : ''

    labelEl.appendChild(_el('span', {
        className: ['bb-control-label', (isCategory ? 'bb-label-category' : 'bb-label-module')].join(' '),
        innerText: label
    }))
    labelEl.appendChild(_el('input', {
        ...inputAttributes,
        type: 'checkbox',
        name: inputName,
        ...(descriptionId ? {'aria-describedby': descriptionId} : {})
    }))
    labelEl.appendChild(_el('span', {className: 'bb-toggle'}))
    main.appendChild(labelEl)

    if (description)
        main.appendChild(_el('p', {
            className: cbPrefix + 'description',
            id: descriptionId,
            innerText: description
        }))

    if (appendModules)
        main.appendChild(appendModules);

    return main
}
