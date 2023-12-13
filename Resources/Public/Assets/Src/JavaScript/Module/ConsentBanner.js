const cookieUtils = require('../Lib/cookie')

const cbPrefix = 'bb-consentbanner-';
const categoryPrefix = 'bb-consentbanner-';

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
 * @property {string} cName
 * @property {number} confirmDuration
 * @property {string} title
 * @property {string} description
 * @property {Object} privacyPage
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
    // Elements
    this.form = null
    this.acceptButton = null
    this.saveButton = null
    this.moreButton = null
    this.confirmButton = null
    this.rejectButton = null
    if (node.classList.contains('bb-widget')) {
        this.widget = node
        node = createElementWithAttrs('div', {
            className: ['bb-consentbanner', `${this.bbConsentBanner.layoutType}`].join(' ')
        })
    } else
        this.widget = createElementWithAttrs('div', {
            className: ['bb-widget', cbPrefix + 'button'].join(' ')
        })

    this.preferences = JSON.parse(cookieUtils.get(this.cookieName));

    this.isBottomLayout = node.classList.contains('bb-cb-bottom')

    this.init = () => {
        if (this.bbConsentBanner === null || this.categories === null || this.modules === null) {
            let warn = '';
            if (this.bbConsentBanner === null) warn += 'Consent banner, '
            if (this.categories === null) warn += 'Categories '
            if (this.modules === null) warn += 'and Modules '
            warn += 'data empty, Consent Banner not initialised!';
            console.warn(warn);
            return false
        }

        this.widget.addEventListener('click', () => {
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
        else
            node.insertAdjacentElement('afterend', this.widget)
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
            node.classList.remove('bb-cb-bottom')
            node.classList.add('bb-cb-overlay')

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
        })

        // closes the banner
        this.form.querySelector(`.${cbPrefix}close`)?.addEventListener('click', () => {
            if (Object.keys(this.preferences).length === 0)
                setCookieAndReload(collectAndModifyData(false))
            else
                this.form.parentElement.classList.remove('visible')
        })

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
            cookieUtils.set(this.cookieName, JSON.stringify(data) + ';secure;samesite=strict', this.confirmDuration)
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
        this.widget.insertAdjacentElement('beforebegin', node)

        const _el = createElementWithAttrs
        this.form = _el('form', {className: [cbPrefix + 'body'].join(' ')})

        const formHeader = _el('div', {className: cbPrefix + 'header'})
        _el('button', {className: cbPrefix + 'close'}, formHeader)

        if (this.bbConsentBanner.title !== '') {
            _el('h3', {
                className: cbPrefix + '-heading',
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

        if (this.bbConsentBanner.description !== '') {
            _el('p', {
                className: cbPrefix + '-text',
                innerHTML: this.bbConsentBanner.description // innerHTML to decode html entities
            }, formContent)
        }

        if (typeof this.bbConsentBanner.categories === "object" && this.bbConsentBanner.categories.length > 0) {
            const contentCategories = _el('div', {
                className: [cbPrefix + 'categories', this.bbConsentBanner.showCategories ? undefined : 'hidden'].join(' ')
            })

            this.bbConsentBanner.categories?.forEach(category => {
                const categoryModules = _el('div', {
                    className: [cbPrefix + 'category-modules', 'hidden'].join(' ')
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
            title: displayNames.acceptAll,
            innerText: displayNames.acceptAll,
        }, buttonContainer)

        // always render save-button; hide at first, show in advanced settings
        this.saveButton = _el('button', {
            className: ['bb-button', 'bb-btn--typeP', 'hidden'].join(' '),
            type: 'submit',
            title: displayNames.saveAndClose,
            innerText: displayNames.saveAndClose,
        }, buttonContainer)

        // show more-BUTTON only when not in bottom layout, otherwise link (see below)
        if (!this.isBottomLayout)
            this.moreButton = _el('button', {
                className: ['bb-button', 'bb-btn--typeS'].join(' '),
                type: 'button',
                title: displayNames.advancedSettings,
                innerText: displayNames.advancedSettings,
            }, buttonContainer)

        // show confirm-button in bottom layout (save button with different label)
        if (this.isBottomLayout && this.bbConsentBanner.showCategories)
            this.confirmButton = _el('button', {
                className: ['bb-button', 'bb-btn--typeS'].join(' '),
                type: 'submit',
                title: displayNames.confirmSelection,
                innerText: displayNames.confirmSelection,
            }, buttonContainer)

        // show reject-button only when no options are visible at first
        if (!this.bbConsentBanner.showCategories)
            this.rejectButton = _el('button', {
                className: ['bb-button', 'bb-btn--typeP'].join(' '),
                type: 'button',
                title: displayNames.reject,
                innerText: displayNames.reject,
            }, buttonContainer)

        formFooter.appendChild(buttonContainer)

        // show more-LINK only in bottom layout, otherwise button (see above)
        const linkContainer = _el('div', {className: cbPrefix + 'links'})
        if (this.isBottomLayout) {
            this.moreButton = _el('button', {
                className: [cbPrefix + '-link'].join(' '),
                type: 'button',
                title: displayNames.advancedSettings,
                innerText: displayNames.advancedSettings,
            }, linkContainer)
        }

        _el('a', {
            className: cbPrefix + '-link',
            title: this.bbConsentBanner.privacyPage.label,
            innerText: this.bbConsentBanner.privacyPage.label,
            href: this.bbConsentBanner.privacyPage.uri
        }, linkContainer)

        formFooter.appendChild(linkContainer)

        this.form.appendChild(formFooter)

        this.attachBannerEventListeners()
        document.querySelector('.bb-consentbanner').appendChild(this.form)
        document.querySelector('.bb-consentbanner').classList.add('visible')
    }
}

let initiated = false;

export function init(node/*, options*/) {
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
            cookieUtils.set(bbConsentBannerCookieName, JSON.stringify(preferences) + ';secure;samesite=strict', this.confirmDuration)

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

    labelEl.appendChild(_el('span', {
        className: ['bb-control-label', (isCategory ? 'bb-label-category' : 'bb-label-module')].join(' '),
        innerText: label
    }))
    labelEl.appendChild(_el('input', {...inputAttributes, type: 'checkbox', name: inputName}))
    labelEl.appendChild(_el('span', {className: 'bb-toggle'}))
    main.appendChild(labelEl)

    if (description)
        main.appendChild(_el('p', {className: cbPrefix + 'description', innerText: description}))

    if (appendModules)
        main.appendChild(appendModules);

    return main
}
