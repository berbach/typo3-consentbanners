import {createToggle, createElementWithAttrs} from "./utils";

const CB_PREFIX = 'bb-';
const CB_PREFIX_NAME = CB_PREFIX + 'consentbanner';
const CB_NAME = CB_PREFIX_NAME + '-';

class CookieInformation {

    constructor(data) {
        this.data = data;
        this.overlay = null;
        this.closeButton = null;
    }

    /**
     * @property {string} closeInfo
     * @return {void}
     */
    createOverlay() {
        // Logik zum Erstellen des HTML für das Overlay
        console.log("Creating overlay with data:", this.data);
        this.overlay = createElementWithAttrs('div', {
            className: [CB_PREFIX_NAME, CB_PREFIX + 'cookie-info', 'bb-cb-overlay'].join(' '),
            tabindex: "-1",
            "data-nosnippet": "true"
        });

        let overlayBody = createElementWithAttrs('div', {className: [CB_PREFIX + 'cookie-info-body'].join(' ')})

        overlayBody.appendChild(this.createOverlayHeader())
        overlayBody.appendChild(this.createOverlayContent())
        overlayBody.appendChild(this.createOverlayFooter())

        this.overlay.innerHTML = `
            <!--googleoff: index-->
            ${overlayBody.outerHTML}
            <!--googleon: index-->
        `;

        document.querySelector('body').insertAdjacentElement('afterbegin', this.overlay);
        this.attachEventListeners();
    }

    /**
     *
     * @return {*}
     */
    createOverlayHeader() {
        let overlayHeader = createElementWithAttrs('div', {className: [CB_PREFIX + 'cookie-info-header'].join(' ')})
        overlayHeader.innerHTML = `<h2>Cookie Information</h2>`
        return overlayHeader;
    }

    /**
     *
     * @return {*}
     */
    createOverlayContent() {
        let overlayContent = createElementWithAttrs('div', {className: [CB_PREFIX + 'cookie-info-content'].join(' ')})
        overlayContent.innerHTML = `<p>Hier werden die Cookie-Informationen angezeigt.</p>`;
        return overlayContent;
    }

    /**
     *
     * @return {*}
     */
    createOverlayFooter() {
        let overlayFooter = createElementWithAttrs('div', {className: [CB_PREFIX + 'cookie-info-footer'].join(' ')}),
            btn= createElementWithAttrs("button", {
                className: [CB_NAME + "button", CB_PREFIX + "cookie-info-close"].join(' '),
                type: 'button',
                innerText: this.data?.displayTexts?.buttons?.closeInfo
            });
        overlayFooter.appendChild(btn);
        return overlayFooter;
    }

    attachEventListeners() {
        this.overlay?.querySelector(`.${CB_PREFIX}cookie-info-close`).addEventListener('click', () => this.hide());
    }

    show() {
        if (!this.overlay) {
            this.createOverlay();
        }
        this.overlay.style.zIndex = '100';
        this.overlay.classList.add('visible')
    }

    hide() {
        if (this.overlay) {
            this.overlay.classList.remove('visible')
            this.overlay.remove();
            //this.overlay.style.display = 'none';
        }
    }
}

export default CookieInformation;
