(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory();
	else if(typeof define === 'function' && define.amd)
		define([], factory);
	else if(typeof exports === 'object')
		exports["CbModule"] = factory();
	else
		root["CbModule"] = root["CbModule"] || {}, root["CbModule"]["ConsentBanner"] = factory();
})(self, () => {
return /******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./Resources/Public/Assets/JavaScript/Lib/cookie.js"
/*!**********************************************************!*\
  !*** ./Resources/Public/Assets/JavaScript/Lib/cookie.js ***!
  \**********************************************************/
(module) {

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

/***/ }

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
/******/ 		// Check if module exists (development only)
/******/ 		if (__webpack_modules__[moduleId] === undefined) {
/******/ 			var e = new Error("Cannot find module '" + moduleId + "'");
/******/ 			e.code = 'MODULE_NOT_FOUND';
/******/ 			throw e;
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
/*!********************************************************************!*\
  !*** ./Resources/Public/Assets/JavaScript/Module/ConsentBanner.js ***!
  \********************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: () => (/* binding */ init)
/* harmony export */ });
const cookieUtils = __webpack_require__(/*! ../Lib/cookie */ "./Resources/Public/Assets/JavaScript/Lib/cookie.js");
const cbPrefix = 'bb-consentbanner-';
const categoryPrefix = 'bb-consentbanner-';

// IE has no Array.from :((
// DON'T USE! BAD POLYFILL
if (!('from' in Array)) Array.from = function (entries) {
  const array = [];
  for (let i = 0; i < entries.length; i++) array.push(entries[i]);
  return array;
};

// IE has no Array.prototype.includes :((
if (!('includes' in Array.prototype)) Array.prototype.includes = function (searchElement, fromIndex) {
  return this.indexOf(searchElement, fromIndex) !== -1;
};

// IE has no Object.fromEntries :((
// DON'T USE! BAD POLYFILL
if (!('fromEntries' in Object)) Object.fromEntries = function (entries) {
  const obj = {};
  Array.from(entries).forEach(entry => {
    obj[entry[0]] = entry[1];
  });
  return obj;
};

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
 * @property {ConsentBannerButtonsDisplayNames} buttonsDisplayNames
 * @property {ConsentBannerCategoryData[]} categories
 * @property {Object[]} modules
 * */

/**
 * @const bbConsentBanner
 * @type {ConsentBannerData}
 * */

// needed for the types to work properly
// bbConsentBanner = bbConsentBanner || null
let bbConsentBannerCookieName = '';
function ConsentBanner(node) {
  console.log('ConsentBanner JavaScript loaded');

  // Data
  this.jsonData = JSON.parse(document.getElementById('bbBannerData').innerHTML);
  // this.bbConsentBanner = typeofIsAndValueIsNot(bbConsentBanner, 'object', '') ? bbConsentBanner : null;
  // this.cookieName = typeofIsAndValueIsNot(this.bbConsentBanner.cName, 'string', '') ? bbConsentBanner.cName : 'BbConsentPreference';
  // bbConsentBannerCookieName = this.cookieName
  // this.confirmDuration = typeofIsAndValueIsNot(this.bbConsentBanner.confirmDuration, 'number', 0) ? this.bbConsentBanner.confirmDuration : 20;
  // this.categories = typeof bbConsentBanner.categories === 'object' && bbConsentBanner.categories.length !== 0 ? bbConsentBanner.categories : null;
  // this.modules = typeof bbConsentBanner.modules === 'object' && bbConsentBanner.modules.length !== 0 ? bbConsentBanner.modules : null;
  // this.isBottomLayout = !typeofIsAndValueIsNot(this.bbConsentBanner.layoutType, 'string', 'bb-cb-bottom');
  // // Elements
  // this.banner = null
  // this.form = null
  // this.acceptButton = null
  // this.saveButton = null
  // this.moreButton = null
  // this.confirmButton = null
  // this.rejectButton = null

  this.preferences = JSON.parse(cookieUtils.get(this.cookieName));
  this.init = () => {
    console.log('ConsentBanner init');
    console.log(this.jsonData);
    return;
    // removed by dead control flow

    // removed by dead control flow

    // removed by dead control flow

    // removed by dead control flow

    // removed by dead control flow

  };
  this.attachBannerEventListeners = () => {
    this.form?.addEventListener('submit', e => {
      e.preventDefault();
    });

    // saves cookie preferences as a cookie
    this.saveButton?.addEventListener('click', () => setCookieAndReload(collectData()));
    this.confirmButton?.addEventListener('click', () => setCookieAndReload(collectData()));

    // saves cookie preferences (sets all to true) as a cookie
    this.acceptButton?.addEventListener('click', () => setCookieAndReload(collectAndModifyData(true)));

    // saves cookie preferences (sets all to false) as a cookie
    this.rejectButton?.addEventListener('click', () => setCookieAndReload(collectAndModifyData(false)));

    // expands the cookie banner to show the toggles
    this.moreButton?.addEventListener('click', () => {
      // remove unneeded buttons
      this.moreButton.remove();
      this.confirmButton?.remove();
      // show save button
      this.saveButton.classList.remove('hidden');

      // force overlay layout
      this.banner.classList.remove('bb-cb-bottom');
      this.banner.classList.add('bb-cb-overlay');

      // convert one-click buttons to secondary
      const convertToSecondary = button => {
        button?.classList.remove('bb-btn--typeP');
        button?.classList.add('bb-btn--typeS');
      };
      convertToSecondary(this.acceptButton);
      convertToSecondary(this.rejectButton);

      // make content scrollable
      this.form.querySelector(`.${cbPrefix}content`).classList.remove('bb-type-dynamic');
      this.form.querySelector(`.${cbPrefix}content`).classList.add('bb-type-scroll');

      // show options
      Array.from(this.form.querySelectorAll(`.${cbPrefix}category-modules.hidden`)).forEach(modules => {
        modules.classList.remove('hidden');
      });
      this.form.querySelector(`.${cbPrefix}categories`).classList.remove('hidden');
      this.form.querySelector(`.${cbPrefix}buttons`).classList.remove('not-categories');
      this.form.querySelector(`.${cbPrefix}buttons`).classList.add('is-categories');
    });

    // closes the banner
    this.form.querySelector(`.${cbPrefix}close`)?.addEventListener('click', () => {
      if (Object.keys(this.preferences).length === 0) setCookieAndReload(collectAndModifyData(false));else this.form.parentElement.classList.remove('visible');
    });
    const collectData = () => Object.fromEntries(Array.from(this.form.querySelectorAll(`.${cbPrefix}module input:not(:disabled)`)).map(el => {
      return [el.name, el.checked];
    }));
    const collectAndModifyData = value => {
      const data = collectData();
      for (let key of Object.keys(data)) data[key] = value;
      setCookieAndReload(data);
      return data;
    };
    const setCookieAndReload = data => {
      cookieUtils.set(this.cookieName, JSON.stringify(data) + ';secure;samesite=lax', this.confirmDuration);
      window.location.reload();
    };

    // syncs the category toggle if one of its module toggles gets changed
    Array.from(this.form.querySelectorAll(`.${cbPrefix}module input:not(:disabled)`)).forEach(input => {
      // document.documentMode === 11 := special case for IE11
      input.addEventListener(document.documentMode === 11 ? 'change' : 'input', () => {
        const categoryID = input.dataset.category;
        const siblingStates = Array.from(document.querySelectorAll(`.${cbPrefix}module input`)).filter(el => el.dataset.category === categoryID).map(el => el.checked);
        const category = document.querySelector(`.${cbPrefix}category input[name="${categoryPrefix + categoryID}"]`);
        category.indeterminate = false;
        if (!siblingStates.includes(true)) category.checked = false;else if (!siblingStates.includes(false)) category.checked = true;else category.indeterminate = true;
      });
    });

    // syncs all module toggles of a category if the category toggle gets changed
    Array.from(this.form.querySelectorAll(`.${cbPrefix}category input[name^=${categoryPrefix}]:not(:disabled)`)).forEach(input => {
      // document.documentMode === 11 := special case for IE11
      input.addEventListener(document.documentMode === 11 ? 'change' : 'input', () => {
        const categoryID = input.name.replace(categoryPrefix, '');
        Array.from(document.querySelectorAll(`.${cbPrefix}module input`)).filter(el => el.dataset.category === categoryID).forEach(module => module.checked = input.checked);
      });
    });
  };
  this.generateBanner = () => {
    if (Object.keys(this.preferences).length !== 0 && this.bbConsentBanner.isTextLink === false) {
      this.widget.insertAdjacentElement('beforebegin', node);
    } else if (Object.keys(this.preferences).length !== 0 && this.bbConsentBanner.isTextLink === true) {
      document.querySelector('main + footer').insertAdjacentElement('afterend', node);
    }
    const _el = createElementWithAttrs;
    this.form = _el('form', {
      className: [cbPrefix + 'body'].join(' ')
    });
    const formHeader = _el('div', {
      className: cbPrefix + 'header'
    });
    _el('button', {
      className: cbPrefix + 'close',
      title: this.bbConsentBanner.closeBtn
    }, formHeader);
    if (this.bbConsentBanner.title !== '') {
      _el('h3', {
        className: cbPrefix + '-heading',
        innerText: this.bbConsentBanner.title
      }, formHeader);
    }
    this.form.appendChild(formHeader);
    const formContent = _el('div', {
      className: [cbPrefix + 'content', !this.isBottomLayout ? 'bb-type-scroll' : 'bb-type-dynamic', this.bbConsentBanner.showCategories ? 'is-categories' : undefined].join(' ')
    });
    if (this.bbConsentBanner.description !== '') {
      _el('p', {
        className: cbPrefix + '-text',
        innerHTML: this.bbConsentBanner.description // innerHTML to decode html entities
      }, formContent);
    }
    if (typeof this.bbConsentBanner.categories === "object" && this.bbConsentBanner.categories.length > 0) {
      const contentCategories = _el('div', {
        className: [cbPrefix + 'categories', this.bbConsentBanner.showCategories ? undefined : 'hidden'].join(' ')
      });
      this.bbConsentBanner.categories?.forEach(category => {
        const categoryModules = _el('div', {
          className: [cbPrefix + 'category-modules', 'hidden'].join(' ')
        });
        const modules = this.bbConsentBanner.modules?.filter(module => module.category.uid === category.uid);
        modules?.forEach(module => {
          categoryModules.appendChild(createToggle(false, module.name, module.uid, module.description, {
            'data-category': module.category.uid,
            checked: !!category.lockedAndActive,
            disabled: !!category.lockedAndActive
          }));
        });
        contentCategories.appendChild(createToggle(true, category.name, categoryPrefix + category.uid, category.description, {
          checked: !!category.lockedAndActive,
          disabled: !!category.lockedAndActive
        }, typeof modules === "object" && categoryModules.children.length > 0 ? categoryModules : null));
      });
      formContent.appendChild(contentCategories);
    }
    this.form.appendChild(formContent);
    const formFooter = _el('div', {
      className: cbPrefix + 'footer'
    });
    const buttonContainer = _el('div', {
      className: [cbPrefix + 'buttons', this.bbConsentBanner.showCategories ? 'is-categories' : 'not-categories'].join(' ')
    });
    const displayNames = this.bbConsentBanner.buttonsDisplayNames;

    // always render accept-button
    this.acceptButton = _el('button', {
      className: ['bb-button', 'bb-btn--typeP'].join(' '),
      type: 'submit',
      innerText: displayNames.acceptAll
    }, buttonContainer);

    // always render save-button; hide at first, show in advanced settings
    this.saveButton = _el('button', {
      className: ['bb-button', 'bb-btn--typeP', 'hidden'].join(' '),
      type: 'submit',
      innerText: displayNames.saveAndClose
    }, buttonContainer);

    // show more-BUTTON only when not in bottom layout, otherwise link (see below)
    if (!this.isBottomLayout) {
      this.moreButton = _el('button', {
        className: ['bb-button', 'bb-btn--typeS'].join(' '),
        type: 'button',
        innerText: displayNames.advancedSettings
      }, buttonContainer);
    }
    // show confirm-button in bottom layout (save button with different label)
    if (this.isBottomLayout && this.bbConsentBanner.showCategories) {
      this.confirmButton = _el('button', {
        className: ['bb-button', 'bb-btn--typeS'].join(' '),
        type: 'submit',
        innerText: displayNames.confirmSelection
      }, buttonContainer);
    }
    // show reject-button only when no options are visible at first
    if (!this.bbConsentBanner.showCategories) {
      this.rejectButton = _el('button', {
        className: ['bb-button', 'bb-btn--typeP'].join(' '),
        type: 'button',
        innerText: displayNames.reject
      }, buttonContainer);
    }
    formFooter.appendChild(buttonContainer);

    // show more-LINK only in bottom layout, otherwise button (see above)
    if (this.isBottomLayout) {
      const linkContainer = _el('div', {
        className: cbPrefix + 'links'
      });
      this.moreButton = _el('button', {
        className: [cbPrefix + '-link'].join(' '),
        type: 'button',
        innerText: displayNames.advancedSettings
      }, linkContainer);
      _el('a', {
        className: cbPrefix + '-link',
        innerText: this.bbConsentBanner.privacyPage.label,
        href: this.bbConsentBanner.privacyPage.uri
      }, linkContainer);
      formFooter.appendChild(linkContainer);
    }
    this.form.appendChild(formFooter);
    this.attachBannerEventListeners();
    if (document.querySelector('.bb-consentbanner-body') == null) {
      document.querySelector('.bb-consentbanner').appendChild(this.form);
      document.querySelector('.bb-consentbanner').classList.add('visible');
    }
  };
}
let initiated = false;
function init(node) {
  let CB = new ConsentBanner(node);
  CB.init();
  if (initiated) return;
  initiated = true;
  document.querySelectorAll('[data-cookiebanner-module]').forEach(toggleBox => {
    toggleBox.querySelector('input').addEventListener('click', () => {
      // also toggle the other toggles with this module id
      document.querySelectorAll(`[data-cookiebanner-module="${toggleBox.dataset.cookiebannerModule}"] input`).forEach(input => input.checked = true);

      // update preferences cookie
      const preferences = JSON.parse(cookieUtils.get(bbConsentBannerCookieName));
      preferences[toggleBox.dataset.cookiebannerModule] = true;
      cookieUtils.set(bbConsentBannerCookieName, JSON.stringify(preferences) + ';secure;samesite=lax', this.confirmDuration);

      // reload page to apply preferences
      setTimeout(() => window.location.reload(), 100);
    });
  });
}
function createElementWithAttrs(tag, attrs, appendTo) {
  const el = document.createElement(tag);
  for (const key in attrs) {
    if (!attrs.hasOwnProperty(key)) continue;
    if (key === 'innerText') el.innerText = attrs[key];else if (key === 'innerHTML') el.innerHTML = attrs[key];else {
      if (key in el) el[key] = attrs[key];else el.setAttribute(key === 'className' ? 'class' : key, attrs[key]);
    }
  }
  if (appendTo) appendTo.appendChild(el);
  return el;
}
function createToggle(isCategory, label, inputName, description, inputAttributes, appendModules) {
  isCategory = isCategory ?? false;
  label = label ?? '';
  inputName = inputName ?? '';
  description = description ?? '';
  inputAttributes = inputAttributes ?? {};
  appendModules = appendModules ?? false;
  const _el = createElementWithAttrs;
  const main = _el('div', {
    className: [cbPrefix + (isCategory ? 'category' : 'module')].join(' ')
  });
  const labelEl = _el('label', {
    className: 'bb-control-checkbox',
    'aria-label': label
  });
  labelEl.appendChild(_el('span', {
    className: ['bb-control-label', isCategory ? 'bb-label-category' : 'bb-label-module'].join(' '),
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
  if (appendModules) main.appendChild(appendModules);
  return main;
}
})();

/******/ 	return __webpack_exports__;
/******/ })()
;
});
//# sourceMappingURL=ConsentBanner.js.map