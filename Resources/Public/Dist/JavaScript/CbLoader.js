/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./Resources/Public/Assets/JavaScript/Lib/cookie.js":
/***/ ((module) => {

let _cookie = {};
_cookie.exdays = 30;
_cookie.check = function (cname) {
  let sitename = this.get(cname);
  return sitename !== "";
};
_cookie.set = function (cname, cvalue, exdays) {
  exdays = exdays ? exdays : this.exdays;
  let d = new Date();
  d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
  let expires = "expires=" + d.toGMTString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
};
_cookie.get = function (cname) {
  let name = cname + "=",
    decodedCookie = decodeURIComponent(document.cookie),
    ca = decodedCookie.split(';');
  for (let i = 0; i < ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) === ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) === 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "{}";
};
module.exports = _cookie;

/***/ }),

/***/ "./Resources/Public/Assets/JavaScript/Lib/debug.js":
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
let Debug = {};
Debug.output = window.DEVMODE ?? true;
Debug.setDevMode = function (mode) {
  this.output = mode;
};
Debug.getArguments = function (args) {
  let arr = [];
  for (let i = 0; i < args.length; i++) {
    arr[i] = args[i];
  }
  return arr;
};
Debug.info = function () {
  this.write("info", this.getArguments(arguments));
};
Debug.log = function () {
  this.write("log", this.getArguments(arguments));
};
Debug.warn = function () {
  this.write("warn", this.getArguments(arguments));
};
Debug.error = function () {
  this.write("error", this.getArguments(arguments));
};
Debug.debug = function () {
  this.write("debug", this.getArguments(arguments));
};
Debug.write = function (level, args) {
  if (this.output && typeof console === "object") if (typeof InstallTrigger !== 'undefined') console[level].apply(this, args);else if (Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0) window.console.log(args[0]);else window.console[level](args);
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Debug);

/***/ }),

/***/ "./Resources/Public/Assets/JavaScript/Lib/fingerprint-generator.js":
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
// fingerprint-generator.js
/**
 * @class BrowserFingerprint
 * @description Generates a basic browser fingerprint based on various browser and device properties.
 * Intended for documenting cookie consent decisions, not for pervasive tracking.
 */
class BrowserFingerprint {
  constructor() {
    this.data = {};
  }

  /**
   * @private
   * @returns {string} The user agent string.
   */
  _getUserAgent() {
    return navigator.userAgent || 'unknown';
  }

  /**
   * @private
   * @returns {string} The screen resolution in 'WxH' format.
   */
  _getScreenResolution() {
    return `${screen.width}x${screen.height}`;
  }

  /**
   * @private
   * @returns {number} The color depth of the screen.
   */
  _getColorDepth() {
    return screen.colorDepth || 'unknown';
  }

  /**
   * @private
   * @returns {string} The current time zone of the user's system.
   */
  _getTimeZone() {
    try {
      return Intl.DateTimeFormat().resolvedOptions().timeZone;
    } catch (e) {
      return 'unknown';
    }
  }

  /**
   * @private
   * @returns {string} A hash generated from a canvas rendering.
   * This is a strong fingerprinting component.
   */
  _getCanvasFingerprint() {
    try {
      const canvas = document.createElement('canvas');
      const ctx = canvas.getContext('2d');
      canvas.width = 200;
      canvas.height = 20;
      ctx.textBaseline = "top";
      ctx.font = "14px 'Arial'";
      ctx.textBaseline = "alphabetic";
      ctx.fillStyle = "#f60";
      ctx.fillRect(125, 1, 62, 20);
      ctx.fillStyle = "#069";
      ctx.fillText("FP Test", 2, 15); // Use a simple, consistent text
      return canvas.toDataURL(); // Returns base64 encoded image data
    } catch (e) {
      return 'canvas_error';
    }
  }

  /**
   * @private
   * @returns {string} A hash generated from WebGL renderer information.
   * Another strong fingerprinting component.
   */
  _getWebGLFingerprint() {
    try {
      const canvas = document.createElement('canvas');
      const gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
      if (gl) {
        const debugInfo = gl.getExtension('WEBGL_debug_renderer_info');
        const vendor = gl.getParameter(debugInfo.UNMASKED_VENDOR_WEBGL) || 'unknown';
        const renderer = gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL) || 'unknown';
        return `${vendor}|${renderer}`;
      }
      return 'webgl_not_supported';
    } catch (e) {
      return 'webgl_error';
    }
  }

  /**
   * Gathers all fingerprint components.
   * @returns {object} An object containing all collected fingerprint data.
   */
  collect() {
    this.data = {
      userAgent: this._getUserAgent(),
      screenResolution: this._getScreenResolution(),
      colorDepth: this._getColorDepth(),
      timeZone: this._getTimeZone(),
      canvasHash: this._getCanvasFingerprint(),
      webGLInfo: this._getWebGLFingerprint()
    };
    return this.data;
  }

  /**
   * Simple synchronous hash function (cyrb53)
   * Modified to return a 16-char hex string (64-bit) instead of a 53-bit number
   * @private
   */
  _cyrb53(str, seed = 0) {
    let h1 = 0xdeadbeef ^ seed,
      h2 = 0x41c6ce57 ^ seed;
    for (let i = 0, ch; i < str.length; i++) {
      ch = str.charCodeAt(i);
      h1 = Math.imul(h1 ^ ch, 2654435761);
      h2 = Math.imul(h2 ^ ch, 1597334677);
    }
    h1 = Math.imul(h1 ^ h1 >>> 16, 2246822507) ^ Math.imul(h2 ^ h2 >>> 13, 3266489909);
    h2 = Math.imul(h2 ^ h2 >>> 16, 2246822507) ^ Math.imul(h1 ^ h1 >>> 13, 3266489909);

    // Combine h2 and h1 to a 64-bit hex string (16 characters)
    return (h2 >>> 0).toString(16).padStart(8, '0') + (h1 >>> 0).toString(16).padStart(8, '0');
  }

  /**
   * Generates a unique hash from the collected fingerprint data synchronously.
   * @returns {string} The hash of the fingerprint data (64 chars).
   */
  generateHash() {
    this.collect();
    const fingerprintString = JSON.stringify(this.data);

    // Generate a longer hash by concatenating multiple passes with different seeds
    // Each pass gives 16 chars. 2 passes = 32 chars
    return this._cyrb53(fingerprintString, 1) + this._cyrb53(fingerprintString, 2);
  }
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (BrowserFingerprint);

/***/ }),

/***/ "./Resources/Public/Assets/JavaScript/Lib/utils.js":
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createElementWithAttrs: () => (/* binding */ createElementWithAttrs),
/* harmony export */   createToggle: () => (/* binding */ createToggle),
/* harmony export */   generateUserHash: () => (/* binding */ generateUserHash),
/* harmony export */   generateUserUid: () => (/* binding */ generateUserUid),
/* harmony export */   isBotAgent: () => (/* binding */ isBotAgent),
/* harmony export */   typeofIsAndValueIsNot: () => (/* binding */ typeofIsAndValueIsNot)
/* harmony export */ });
/* harmony import */ var _fingerprint_generator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("./Resources/Public/Assets/JavaScript/Lib/fingerprint-generator.js");

const BOT_AGENT_REGEX = new RegExp(['Mozilla/5.0 \\(Linux; Android 11; moto g power \\(2022\\)\\) AppleWebKit/537\\.36 \\(KHTML, like Gecko\\) Chrome/119\\.0\\.0\\.0 Mobile Safari/537\\.36', 'Mozilla/5.0 \\(Macintosh; Intel Mac OS X 10_15_7\\) AppleWebKit/537\\.36 \\(KHTML, like Gecko\\) Chrome/119\\.0\\.0\\.0 Safari/537\\.36', '(?:Googlebot|Bingbot|Baiduspider|YandexBot|DuckDuckBot|Slackbot|Facebookbot|Twitterbot|LinkedInbot|Pinterest|WhatsApp|TelegramBot|Slurp|Sogou|Exabot|ia_archiver|msnbot|YandexMobileBot|AdsBot-Google-Mobile|Googlebot-Image|Googlebot-News|Googlebot-Video|Mediapartners-Google|AdsBot-Google|FeedFetcher-Google|Google-Read-Aloud|Google-Adwords-Instant|Yahoo! Slurp China|Yahoo! Slurp|Y!J-BRW|Y!J-SRD|Y!J-MBS|Y!J-MR2|Y!J-PSCS|Y!J-BSC|Y!J-GECC|Y!J-DSC|Y!J-DBS|Y!J-SRB|Y!J-RTS|Y!J-BEP|Y!J-BRP|Y!J-BSP|Y!J-SRS|Y!J-SRE|Y!J-SRT|Y!J-BRV|Y!J-BSV|Y!J-SBC|Y!J-BRL|Y!J-TRG|Y!J-BRD|Y!J-BRG|Y!J-SRQ|Y!J-BRW|Y!J-BRW|Google PageSpeed)'].join('|'), 'i');
const createElementWithAttrs = (tag, attrs, appendTo) => {
  const el = document.createElement(tag);
  for (const key in attrs) {
    if (!attrs.hasOwnProperty(key)) continue;
    if (key === 'innerText') el.innerText = attrs[key];else if (key === 'innerHTML') el.innerHTML = attrs[key];else {
      if (key in el) el[key] = attrs[key];else el.setAttribute(key === 'className' ? 'class' : key, attrs[key]);
    }
  }
  if (appendTo) appendTo.appendChild(el);
  return el;
};
const createToggle = (isGroup, cbPrefix, label, inputName, description, inputAttributes, appendComponents) => {
  isGroup = isGroup ?? false;
  cbPrefix = cbPrefix ?? 'bb-cb-';
  label = label ?? '';
  inputName = inputName ?? '';
  description = description ?? '';
  inputAttributes = inputAttributes ?? {};
  appendComponents = appendComponents ?? false;
  const _el = createElementWithAttrs;
  const main = _el('div', {
    className: [cbPrefix + (isGroup ? 'group' : 'component')].join(' ')
  });
  const labelEl = _el('label', {
    className: 'bb-control-checkbox',
    'aria-label': label
  });
  labelEl.appendChild(_el('span', {
    className: ['bb-control-label', isGroup ? 'bb-label-group' : 'bb-label-component'].join(' '),
    innerText: label
  }));
  labelEl.appendChild(_el('input', {
    ...inputAttributes,
    type: 'checkbox',
    name: inputName
  }));
  labelEl.appendChild(_el('span', {
    className: 'bb-toggle'
  }));
  main.appendChild(labelEl);
  if (description) main.appendChild(_el('p', {
    className: cbPrefix + 'description',
    innerText: description
  }));
  if (appendComponents) main.appendChild(appendComponents);
  return main;
};
const isBotAgent = () => {
  return BOT_AGENT_REGEX.test(navigator.userAgent);
};
const typeofIsAndValueIsNot = (variable, type, value) => typeof variable === type && variable !== value;
const generateUserUid = () => {
  let d = new Date().getTime();
  let d2 = performance && performance.now && performance.now() * 1000 || 0;
  return 'zzzwz2zz-zz3zz-w9zzz-wz6zz-zzzwzzzzz'.replace(/[zw]/g, function (e) {
    let r = Math.random() * 16;
    if (d > 0) {
      r = (d + r) % 16 | 0;
      d = Math.floor(d / 16);
    } else {
      r = (d2 + r) % 16 | 0;
      d2 = Math.floor(d2 / 16);
    }
    return (e === 'z' ? r : r & 0x3 | 0x8).toString(16);
  });
};
const generateUserHash = () => {
  const fingerprint = new _fingerprint_generator__WEBPACK_IMPORTED_MODULE_0__["default"]();
  let hash = fingerprint.generateHash();
  let i = 0;
  return '########-#####-#####-#####-#########'.replace(/#/g, () => hash[i++]);
};

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Lib_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("./Resources/Public/Assets/JavaScript/Lib/utils.js");
/* harmony import */ var _Lib_debug__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("./Resources/Public/Assets/JavaScript/Lib/debug.js");


const cookieUtils = __webpack_require__("./Resources/Public/Assets/JavaScript/Lib/cookie.js");
window.DEVMODE = "development" !== 'production' ?? 0;
_Lib_debug__WEBPACK_IMPORTED_MODULE_1__["default"].setDevMode(DEVMODE);

/**
 * @TODO
 * Check is user hash LocalStorage
 * Check is user hash in DB
 * @constructor
 */
const CbManager = function () {
  const CB_NAME = 'bb-consentbanner';
  const CB_PREFIX = CB_NAME + '-';
  const CB_GROUP_PREFIX = 'group-';
  const LAST_PREFERENCES_NAME = this.bannerPreferences?.cName ?? 'BbConsentPreferences';
  this.bannerPreferences = JSON.parse(document.getElementById('bbBannerData').innerHTML);
  this.userIdentificationKey = "";
  this.isBottomLayout = !(0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.typeofIsAndValueIsNot)(this.bannerPreferences?.layout, 'string', 'cb-bottom');
  this.cookiePreferences = JSON.parse(cookieUtils.get(this.bannerPreferences?.cName ?? 'BbConsentPreferences'));
  this.localPreferences = localStorage.getItem(LAST_PREFERENCES_NAME) ? JSON.parse(localStorage.getItem(LAST_PREFERENCES_NAME)) : [];
  this.changePreferences = null;
  this.acceptAllButton = null;
  this.saveAndCloseButton = null;
  this.confirmSelectionButton = null;
  this.advancedSettingsButton = null;
  this.showCookieInfoButton = null;
  this.banner = null;
  this.bannerBody = null;
  this.bannerStage = null;
  this.bannerMain = null;
  this.bannerFooter = null;
  this.init = () => {
    console.log('CBManager JavaScript loaded');
    console.log(this.getUserUid());
    console.log(this.getUserHash());
    if ((0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.isBotAgent)()) {
      console.log('Set Essential Cookies');
    }
    if (this.isPreferencesCookie() && this.isPreferencesLocalStorage()) {
      this.openerChangePreferences();
    } else {
      this.CreateWrapperBanner();
      this.createBanner();
    }
  };
  this.attachBannerEventListeners = () => {
    console.log(this.bannerMain);
    this.bannerMain?.addEventListener('submit', e => {
      e.preventDefault();
    });
    console.log(this.acceptAllButton);
    this.acceptAllButton?.addEventListener('click', () => {
      console.log('acceptAllButton');
      console.log(this.collectAndModifyData(true));
      this.saveConsentLog();
    });
    this.saveAndCloseButton?.addEventListener('click', () => {
      console.log('saveAndCloseButton');
      console.log(this.collectData());
    });
    console.log(this.confirmSelectionButton);
    this.confirmSelectionButton?.addEventListener('click', () => {
      console.log('confirmSelectionButton');
      console.log(this.collectData());
    });
    this.advancedSettingsButton?.addEventListener('click', () => {
      console.log('advancedSettingsButton');
      this.createBannerOverlay();
    });
    this.attachSyncToggles();
  };
  this.openerChangePreferences = () => {
    const openerType = this.bannerPreferences?.openerVariant ?? 10;
    if (openerType === 10) {
      const targetWrapper = document.querySelector('.bb-nav__service');
      const cloneFirstElementChild = targetWrapper.firstElementChild.cloneNode();
      cloneFirstElementChild.innerHTML = '';
      this.changePreferences = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('button', {
        className: ['bb-nav__link', 'bb-text-widget', CB_PREFIX + 'link'].join(' '),
        type: 'button',
        innerText: this.bannerPreferences?.openerData?.textLinkText ?? 'Cookie Settings'
      }, cloneFirstElementChild);
      if (this.bannerPreferences?.openerData?.textLinkPosition === 'first') {
        targetWrapper.insertBefore(cloneFirstElementChild, targetWrapper.firstElementChild);
      }
      if (this.bannerPreferences?.openerData?.textLinkPosition === 'last') {
        targetWrapper.appendChild(cloneFirstElementChild);
      }
    }
    if (openerType === 20) {
      const buttonPosition = this.bannerPreferences?.openerData?.buttonWidgetPosition ?? 'left';
      const buttonText = this.bannerPreferences?.openerData?.buttonWidgetText ?? 'Cookie Settings';
      this.changePreferences = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)("button", {
        className: ["bb-button-widget", CB_PREFIX + "button", "bb-button-widget-" + buttonPosition].join(' '),
        innerHTML: (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)("span", {
          innerText: buttonText
        }).outerHTML
      });
      //TODO Widget Position
      console.log(document.querySelector('body > div'));
      document.querySelector('body > div').insertAdjacentElement('afterend', this.changePreferences);
    }
    this.changePreferences?.addEventListener('click', () => {
      console.log('click Change Preferences');
    });
  };
  this.CreateWrapperBanner = () => {
    console.log('CreateWrapperBanner');
    this.banner = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('div', {
      className: [CB_NAME, `bb-${this.bannerPreferences?.layout ?? 'cb-bottom'}`].join(" "),
      tabindex: "-1",
      "data-nosnippet": "true"
    });
    document.querySelector('body').insertAdjacentElement('afterbegin', this.banner);
  };
  this.createBanner = (isOverlay = false) => {
    console.log('createBanner');
    this.bannerBody = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('div', {
      className: [CB_PREFIX + 'body'].join(' ')
    });
    this.bannerStage = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('div', {
      className: [CB_PREFIX + 'stage'].join(' ')
    });
    this.bannerMain = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('form', {
      className: [CB_PREFIX + 'main'].join(' ')
    });
    this.bannerFooter = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('div', {
      className: [CB_PREFIX + 'footer'].join(' ')
    });
    const bannerContent = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('div', {
      className: [CB_PREFIX + 'content', !this.isBottomLayout ? 'bb-type-scroll' : 'bb-type-dynamic'].join(' ')
    });
    const bannerHeader = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('div', {
      className: CB_PREFIX + 'header'
    });
    if (this.bannerPreferences?.banner?.title !== '') {
      (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('p', {
        className: CB_PREFIX + '-title',
        innerHTML: (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)("strong", {
          innerText: this.bannerPreferences?.banner?.title
        }).outerHTML
      }, bannerHeader);
    }
    if (this.bannerPreferences?.banner?.description !== '') {
      (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('p', {
        className: CB_PREFIX + '-description',
        innerHTML: this.bannerPreferences?.banner?.description // innerHTML to decode html entities
      }, bannerHeader);
    }
    bannerContent.appendChild(bannerHeader);
    bannerContent.appendChild(this.createPreferences(isOverlay));
    this.bannerMain.appendChild(bannerContent);
    this.bannerMain.appendChild(this.createContainerButtons(isOverlay));
    this.bannerFooter.appendChild(this.createContainerFooter());
    this.bannerStage.appendChild(this.bannerMain);
    this.bannerStage.appendChild(this.bannerFooter);
    this.banner.innerHTML = `
            <!--googleoff: index-->
            ${this.bannerBody.outerHTML}
            <!--googleon: index-->
            `;
    if (document.querySelector('.' + CB_PREFIX + 'stage') == null && isOverlay === false) {
      document.querySelector('.' + CB_PREFIX + 'body').appendChild(this.bannerStage);
      document.querySelector('.' + CB_NAME).classList.add('visible');
    }
  };
  this.createBannerOverlay = () => {
    console.log('createBannerOverlay');
    this.banner.classList.remove("bb-cb-bottom", 'visible');
    this.banner.classList.add('bb-cb-overlay', 'visible');

    // if(document.querySelector('.' + CB_PREFIX + 'stage') == null) {
    //     document.querySelector('.' + CB_PREFIX + 'body').appendChild(this.bannerStage)
    //     document.querySelector('.' + CB_NAME).classList.add('visible')
    // }
  };
  this.createPreferences = (isOverlay = false) => {
    console.log('createPreferences');
    const containerPreferences = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('div', {
      className: [CB_PREFIX + 'preferences'].join(' ')
    });
    if (typeof this.bannerPreferences?.groups === "object" && Object.keys(this.bannerPreferences?.groups).length > 0) {
      const contentGroups = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('div', {
        className: [CB_PREFIX + 'groups'].join(' ')
      });
      for (let groupKey in this.bannerPreferences?.groups) {
        let group = this.bannerPreferences.groups[groupKey];
        const groupComponents = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('div', {
          className: [CB_PREFIX + 'components'].join(' ')
        });
        if (typeof group?.components === "object" && Object.keys(group?.components).length > 0) {
          for (let componentKey in group?.components) {
            let component = group?.components[componentKey];
            groupComponents.appendChild((0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createToggle)(false, CB_PREFIX, component?.title, component?.id, component?.description, {
              'data-group-id': component?.groupId,
              'data-component-title': component?.title,
              checked: !!group.lockedAndActive,
              disabled: !!group.lockedAndActive
            }));
          }
        }
        if (groupComponents.children.length > 0 || group.lockedAndActive) {
          contentGroups.appendChild((0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createToggle)(true, CB_PREFIX, group?.title, CB_GROUP_PREFIX + group?.id, group?.description, {
            checked: !!group.lockedAndActive,
            disabled: !!group.lockedAndActive
          }, groupComponents.children.length > 0 ? groupComponents : null));
        }
      }
      containerPreferences.appendChild(contentGroups);
    }
    return containerPreferences;
  };
  this.createContainerButtons = (isOverlay = false) => {
    console.log('createButtons');
    let buttonLabels = Object.keys(this.bannerPreferences?.displayTexts?.buttons).length > 0 ? this.bannerPreferences?.displayTexts?.buttons : {};
    const containerButtons = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('div', {
      className: [CB_PREFIX + 'buttons'].join(' ')
    });
    /**
     * @TODO
     * acceptEssential Button einbauen
     * close or X Button einbauen
     */
    this.saveAndCloseButton = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('button', {
      className: ['bb-button', 'bb-btn--typeS', 'bb-show-overlay'].join(' '),
      type: 'submit',
      innerText: buttonLabels?.saveAndClose
    }, containerButtons);
    this.acceptAllButton = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('button', {
      className: ['bb-button', 'bb-btn--typeS', 'bb-show-all'].join(' '),
      type: 'submit',
      innerText: buttonLabels?.acceptAll
    }, containerButtons);
    this.confirmSelectionButton = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('button', {
      className: ['bb-button', 'bb-btn--typeS', 'bb-show-bottom'].join(' '),
      type: 'submit',
      innerText: buttonLabels?.confirmSelection
    }, containerButtons);
    this.advancedSettingsButton = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('button', {
      className: ['bb-button', 'bb-btn--typeS', 'bb-show-bottom'].join(' '),
      type: 'button',
      innerText: buttonLabels?.advancedSettings
    }, containerButtons);
    this.attachBannerEventListeners(isOverlay);
    return containerButtons;
  };
  this.createContainerFooter = () => {
    console.log('createContainerFooter');
    let buttonLabels = Object.keys(this.bannerPreferences?.displayTexts?.buttons).length > 0 ? this.bannerPreferences?.displayTexts?.buttons : {};
    let footerLinks = this.bannerPreferences?.footerNavigation.length > 0 ? this.bannerPreferences?.footerNavigation : [];
    const containerFooter = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('div', {
      className: [CB_PREFIX + 'footer-row'].join(' ')
    });
    let containerFooterCell = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('div', {
      className: CB_PREFIX + 'footer-cell'
    });
    this.showCookieInfoButton = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('button', {
      className: [CB_PREFIX + '-link', 'bb-link--cookie-info'].join(' '),
      type: 'button',
      innerText: buttonLabels?.showInfo
    }, containerFooterCell);
    containerFooter.appendChild(containerFooterCell);
    this.showCookieInfoButton?.addEventListener('click', () => {
      console.log('click show cookie infomation');
    });
    containerFooterCell = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('div', {
      className: CB_PREFIX + 'footer-cell'
    });
    const linkContainer = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('div', {
      className: CB_PREFIX + 'links'
    });
    footerLinks.forEach(link => {
      (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('a', {
        className: CB_PREFIX + '-link',
        innerText: link.title,
        href: link.url
      }, linkContainer);
    });
    (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('div', {
      className: CB_PREFIX + 'footer-cell'
    });
    const identificationContainer = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('div', {
      className: CB_PREFIX + 'uid',
      innerText: `User-ID: ${this.getUserUid()}`
    });
    containerFooterCell.appendChild(linkContainer);
    containerFooter.appendChild(containerFooterCell);
    containerFooter.appendChild(identificationContainer);
    return containerFooter;
  };
  this.attachSyncToggles = () => {
    console.log('attachSyncToggles');
    const formPreferencesContainer = this.bannerMain.querySelector(`.${CB_PREFIX}preferences`);
    Array.from(formPreferencesContainer.querySelectorAll(`.${CB_PREFIX}component input:not(:disabled)`)).forEach(input => {
      input.addEventListener('change', () => {
        const groupId = input.dataset.groupId;
        const siblingComponents = Array.from(formPreferencesContainer.querySelectorAll(`.${CB_PREFIX}component input`)).filter(el => el.dataset.groupId === groupId).map(el => el.checked);
        const group = document.querySelector(`.${CB_PREFIX}group input[name="${CB_GROUP_PREFIX + groupId}"]`);
        group.indeterminate = false;
        if (!siblingComponents.includes(true)) group.checked = false;else if (!siblingComponents.includes(false)) group.checked = true;else group.indeterminate = true;
      });
    });
    Array.from(formPreferencesContainer.querySelectorAll(`.${CB_PREFIX}group input[name^=${CB_GROUP_PREFIX}]:not(:disabled)`)).forEach(input => {
      input.addEventListener('change', () => {
        const groupId = input.name.replace(CB_GROUP_PREFIX, '');
        Array.from(formPreferencesContainer.querySelectorAll(`.${CB_PREFIX}component input`)).filter(el => el.dataset.groupId === groupId).forEach(component => component.checked = input.checked);
      });
    });
  };
  this.savePreferences = () => {
    console.log('savePreferences');
  };
  this.getLastPreferences = () => {
    console.log('getLastPreferences');
    let lastPreferences;
    lastPreferences = window.localStorage.getItem(LAST_PREFERENCES_NAME);
    if (lastPreferences) {
      lastPreferences = JSON.parse(lastPreferences);
    } else {
      lastPreferences = {
        uid: '',
        hash: ''
      };
    }
    return lastPreferences;
  };
  this.isPreferencesCookie = () => {
    console.log('isPreferencesCookie');
    return Object.keys(this.cookiePreferences).length !== 0;
  };
  this.isPreferencesLocalStorage = () => {
    console.log('isPreferencesLocalStorage');
    return Object.keys(this.localPreferences).length !== 0;
  };
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
      uuid = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.generateUserUid)();
      lastPreferences.uuid = uuid;
      window.localStorage.setItem(LAST_PREFERENCES_NAME, JSON.stringify(lastPreferences));
    }
    return uuid;
  };
  /**
   * hash is the generate browser fingerprint hash
   * @return {string}
   */
  this.getUserHash = () => {
    const lastPreferences = this.getLastPreferences();
    let hash = lastPreferences.hash;
    if (!hash) {
      hash = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.generateUserHash)();
      console.log(hash === "2e116086-dd85b-9a997-f2469-17937f5ac");
      lastPreferences.hash = hash;
      window.localStorage.setItem(LAST_PREFERENCES_NAME, JSON.stringify(lastPreferences));
    }
    return hash;
  };

  // this.getUserIdentificationKey = async () => {
  //
  // }

  this.saveConsentLog = async () => {
    const url = "/consent/save";
    const consent = {
      version: 1,
      services: {
        analytics: false,
        youtube: false
      }
    };
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        "Content-Type": "application/json"
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
  };
  this.collectData = () => {
    const formPreferencesContainer = this.bannerMain.querySelector(`.${CB_PREFIX}preferences`);
    return Object.fromEntries(Array.from(formPreferencesContainer.querySelectorAll(`.${CB_PREFIX}component input[data-group-id]`)).map(el => {
      return [el.name, {
        'title': el.dataset.componentTitle,
        'consent': el.checked
      }];
    }));
  };
  this.collectAndModifyData = value => {
    const data = this.collectData();
    for (let key of Object.keys(data)) data[key].consent = value;
    return data;
  };
};
new CbManager().init();
})();

/******/ })()
;
//# sourceMappingURL=CbLoader.js.map