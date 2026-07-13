/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./Resources/Public/Assets/JavaScript/Lib/cookie.js"
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

/***/ },

/***/ "./Resources/Public/Assets/JavaScript/Lib/debug.js"
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

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

/***/ },

/***/ "./Resources/Public/Assets/JavaScript/Lib/fingerprint-generator.js"
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

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

/***/ },

/***/ "./Resources/Public/Assets/JavaScript/Lib/utils.js"
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createElementWithAttrs: () => (/* binding */ createElementWithAttrs),
/* harmony export */   createToggle: () => (/* binding */ createToggle),
/* harmony export */   generateUserHash: () => (/* binding */ generateUserHash),
/* harmony export */   generateUserUid: () => (/* binding */ generateUserUid),
/* harmony export */   hasDaysPassed: () => (/* binding */ hasDaysPassed),
/* harmony export */   isBotAgent: () => (/* binding */ isBotAgent),
/* harmony export */   typeofIsAndValueIsNot: () => (/* binding */ typeofIsAndValueIsNot)
/* harmony export */ });
/* harmony import */ var _fingerprint_generator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("./Resources/Public/Assets/JavaScript/Lib/fingerprint-generator.js");

const BOT_AGENT_REGEX = new RegExp(['Mozilla/5.0 \\(Linux; Android 11; moto g power \\(2022\\)\\) AppleWebKit/537\\.36 \\(KHTML, like Gecko\\) Chrome/119\\.0\\.0\\.0 Mobile Safari/537\\.36', 'Mozilla/5.0 \\(Macintosh; Intel Mac OS X 10_15_7\\) AppleWebKit/537\\.36 \\(KHTML, like Gecko\\) Chrome/119\\.0\\.0\\.0 Safari/537\\.36', '(?:Googlebot|Bingbot|Baiduspider|YandexBot|DuckDuckBot|Slackbot|Facebookbot|Twitterbot|LinkedInbot|Pinterest|WhatsApp|TelegramBot|Slurp|Sogou|Exabot|ia_archiver|msnbot|YandexMobileBot|AdsBot-Google-Mobile|Googlebot-Image|Googlebot-News|Googlebot-Video|Mediapartners-Google|AdsBot-Google|FeedFetcher-Google|Google-Read-Aloud|Google-Adwords-Instant|Yahoo! Slurp China|Yahoo! Slurp|Y!J-BRW|Y!J-SRD|Y!J-MBS|Y!J-MR2|Y!J-PSCS|Y!J-BSC|Y!J-GECC|Y!J-DSC|Y!J-DBS|Y!J-SRB|Y!J-RTS|Y!J-BEP|Y!J-BRP|Y!J-BSP|Y!J-SRS|Y!J-SRE|Y!J-SRT|Y!J-BRV|Y!J-BSV|Y!J-SBC|Y!J-BRL|Y!J-TRG|Y!J-BRD|Y!J-BRG|Y!J-SRQ|Y!J-BRW|Y!J-BRW|Google PageSpeed)'].join('|'), 'i');
/**
 *
 * @param tag
 * @param attrs
 * @param appendTo
 * @return {*}
 */
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
/**
 *
 * @param isGroup
 * @param cbPrefix
 * @param label
 * @param inputName
 * @param description
 * @param inputAttributes
 * @param appendComponents
 * @return {*}
 */
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
const hasDaysPassed = (timestamp, days) => {
  if (!timestamp) return true;
  const msPerDay = 24 * 60 * 60 * 1000;
  return Math.floor(Date.now() / 1000) - timestamp >= days * msPerDay;
};

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
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
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
/******/ 	/* webpack/runtime/ensure chunk */
/******/ 	(() => {
/******/ 		__webpack_require__.f = {};
/******/ 		// This file contains only the entry chunk.
/******/ 		// The chunk loading function for additional chunks
/******/ 		__webpack_require__.e = (chunkId) => {
/******/ 			return Promise.all(Object.keys(__webpack_require__.f).reduce((promises, key) => {
/******/ 				__webpack_require__.f[key](chunkId, promises);
/******/ 				return promises;
/******/ 			}, []));
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/get javascript chunk filename */
/******/ 	(() => {
/******/ 		// This function allow to reference async chunks
/******/ 		__webpack_require__.u = (chunkId) => {
/******/ 			// return url for filenames not based on template
/******/ 			if (chunkId === "Resources_Public_Assets_JavaScript_Lib_CookieInformation_js") return "JavaScript/Chunks/Lib.CookieInformation.js";
/******/ 			// return url for filenames based on template
/******/ 			return undefined;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/global */
/******/ 	(() => {
/******/ 		__webpack_require__.g = (function() {
/******/ 			if (typeof globalThis === 'object') return globalThis;
/******/ 			try {
/******/ 				return this || new Function('return this')();
/******/ 			} catch (e) {
/******/ 				if (typeof window === 'object') return window;
/******/ 			}
/******/ 		})();
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/load script */
/******/ 	(() => {
/******/ 		var inProgress = {};
/******/ 		var dataWebpackPrefix = "consent_banner:";
/******/ 		// loadScript function to load a script via script tag
/******/ 		__webpack_require__.l = (url, done, key, chunkId) => {
/******/ 			if(inProgress[url]) { inProgress[url].push(done); return; }
/******/ 			var script, needAttach;
/******/ 			if(key !== undefined) {
/******/ 				var scripts = document.getElementsByTagName("script");
/******/ 				for(var i = 0; i < scripts.length; i++) {
/******/ 					var s = scripts[i];
/******/ 					if(s.getAttribute("src") == url || s.getAttribute("data-webpack") == dataWebpackPrefix + key) { script = s; break; }
/******/ 				}
/******/ 			}
/******/ 			if(!script) {
/******/ 				needAttach = true;
/******/ 				script = document.createElement('script');
/******/ 		
/******/ 				script.charset = 'utf-8';
/******/ 				if (__webpack_require__.nc) {
/******/ 					script.setAttribute("nonce", __webpack_require__.nc);
/******/ 				}
/******/ 				script.setAttribute("data-webpack", dataWebpackPrefix + key);
/******/ 		
/******/ 				script.src = url;
/******/ 			}
/******/ 			inProgress[url] = [done];
/******/ 			var onScriptComplete = (prev, event) => {
/******/ 				// avoid mem leaks in IE.
/******/ 				script.onerror = script.onload = null;
/******/ 				clearTimeout(timeout);
/******/ 				var doneFns = inProgress[url];
/******/ 				delete inProgress[url];
/******/ 				script.parentNode && script.parentNode.removeChild(script);
/******/ 				doneFns && doneFns.forEach((fn) => (fn(event)));
/******/ 				if(prev) return prev(event);
/******/ 			}
/******/ 			var timeout = setTimeout(onScriptComplete.bind(null, undefined, { type: 'timeout', target: script }), 120000);
/******/ 			script.onerror = onScriptComplete.bind(null, script.onerror);
/******/ 			script.onload = onScriptComplete.bind(null, script.onload);
/******/ 			needAttach && document.head.appendChild(script);
/******/ 		};
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
/******/ 	/* webpack/runtime/publicPath */
/******/ 	(() => {
/******/ 		var scriptUrl;
/******/ 		if (__webpack_require__.g.importScripts) scriptUrl = __webpack_require__.g.location + "";
/******/ 		var document = __webpack_require__.g.document;
/******/ 		if (!scriptUrl && document) {
/******/ 			if (document.currentScript && document.currentScript.tagName.toUpperCase() === 'SCRIPT')
/******/ 				scriptUrl = document.currentScript.src;
/******/ 			if (!scriptUrl) {
/******/ 				var scripts = document.getElementsByTagName("script");
/******/ 				if(scripts.length) {
/******/ 					var i = scripts.length - 1;
/******/ 					while (i > -1 && (!scriptUrl || !/^http(s?):/.test(scriptUrl))) scriptUrl = scripts[i--].src;
/******/ 				}
/******/ 			}
/******/ 		}
/******/ 		// When supporting browsers where an automatic publicPath is not supported you must specify an output.publicPath manually via configuration
/******/ 		// or pass an empty string ("") and set the __webpack_public_path__ variable from your code to use your own logic.
/******/ 		if (!scriptUrl) throw new Error("Automatic publicPath is not supported in this browser");
/******/ 		scriptUrl = scriptUrl.replace(/^blob:/, "").replace(/#.*$/, "").replace(/\?.*$/, "").replace(/\/[^\/]+$/, "/");
/******/ 		__webpack_require__.p = scriptUrl + "../";
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"CbLoader": 0
/******/ 		};
/******/ 		
/******/ 		__webpack_require__.f.j = (chunkId, promises) => {
/******/ 				// JSONP chunk loading for javascript
/******/ 				var installedChunkData = __webpack_require__.o(installedChunks, chunkId) ? installedChunks[chunkId] : undefined;
/******/ 				if(installedChunkData !== 0) { // 0 means "already installed".
/******/ 		
/******/ 					// a Promise means "currently loading".
/******/ 					if(installedChunkData) {
/******/ 						promises.push(installedChunkData[2]);
/******/ 					} else {
/******/ 						if(true) { // all chunks have JS
/******/ 							// setup Promise in chunk cache
/******/ 							var promise = new Promise((resolve, reject) => (installedChunkData = installedChunks[chunkId] = [resolve, reject]));
/******/ 							promises.push(installedChunkData[2] = promise);
/******/ 		
/******/ 							// start chunk loading
/******/ 							var url = __webpack_require__.p + __webpack_require__.u(chunkId);
/******/ 							// create error before stack unwound to get useful stacktrace later
/******/ 							var error = new Error();
/******/ 							var loadingEnded = (event) => {
/******/ 								if(__webpack_require__.o(installedChunks, chunkId)) {
/******/ 									installedChunkData = installedChunks[chunkId];
/******/ 									if(installedChunkData !== 0) installedChunks[chunkId] = undefined;
/******/ 									if(installedChunkData) {
/******/ 										var errorType = event && (event.type === 'load' ? 'missing' : event.type);
/******/ 										var realSrc = event && event.target && event.target.src;
/******/ 										error.message = 'Loading chunk ' + chunkId + ' failed.\n(' + errorType + ': ' + realSrc + ')';
/******/ 										error.name = 'ChunkLoadError';
/******/ 										error.type = errorType;
/******/ 										error.request = realSrc;
/******/ 										installedChunkData[1](error);
/******/ 									}
/******/ 								}
/******/ 							};
/******/ 							__webpack_require__.l(url, loadingEnded, "chunk-" + chunkId, chunkId);
/******/ 						}
/******/ 					}
/******/ 				}
/******/ 		};
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		// no on chunks loaded
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 		
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunkconsent_banner"] = self["webpackChunkconsent_banner"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
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
/**
 * @property {string} NODE_ENV
 */
window.DEVMODE = "development" !== 'production' ?? 0;
_Lib_debug__WEBPACK_IMPORTED_MODULE_1__["default"].setDevMode(DEVMODE);

/**
 * @constructor
 */
const CbManager = function () {
  const CB_NAME = 'bb-consentbanner';
  const CB_PREFIX = CB_NAME + '-';
  const CB_GROUP_PREFIX = 'group-';
  const LAST_PREFERENCES_NAME = this.bannerPreferences?.cName ?? 'BbConsentPreferences';
  this.bannerPreferences = JSON.parse(document.getElementById('bbBannerData').innerHTML);
  this.isBottomLayout = !(0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.typeofIsAndValueIsNot)(this.bannerPreferences?.layout, 'string', 'cb-bottom');
  this.cookiePreferences = JSON.parse(cookieUtils.get(LAST_PREFERENCES_NAME));
  this.localPreferences = {};
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
  /**
   *
   */
  this.init = () => {
    console.log('CBManager JavaScript initialize');
    if ((0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.isBotAgent)()) {
      console.log('Set Essential Cookies');
    }
    if (this.shouldCreateBanner()) {
      this.openerChangePreferences();
      this.createWrapperBanner();
      this.createBanner();
    } else {
      this.openerChangePreferences();
    }
  };
  this.attachBannerEventListeners = () => {
    this.bannerMain?.addEventListener('submit', e => {
      e.preventDefault();
    });
    this.acceptAllButton?.addEventListener('click', () => {
      console.log('acceptAllButton');
      this.savePreferences(this.collectAndModifyData(true));
    });
    this.saveAndCloseButton?.addEventListener('click', () => {
      console.log('saveAndCloseButton');
      this.savePreferences(this.collectData());
    });
    this.confirmSelectionButton?.addEventListener('click', () => {
      console.log('confirmSelectionButton');
      this.savePreferences(this.collectData());
    });
    this.advancedSettingsButton?.addEventListener('click', () => {
      console.log('advancedSettingsButton');
      this.createBannerOverlay();
    });
    this.attachSyncToggles();
  };
  /**
   * @property {number} openerVariant
   * @property {Object} openerData
   * @property {string} targetFooterNavigation
   * @property {string} textLinkText
   * @property {string} textLinkPosition
   * @property {string} buttonWidgetPosition
   * @property {string} buttonWidgetText
   */
  this.openerChangePreferences = () => {
    const openerType = this.bannerPreferences.openerVariant ?? 10;
    if (openerType === 10) {
      const targetWrapper = document.querySelector(this.bannerPreferences?.openerData?.targetFooterNavigation);
      const cloneFirstElementChild = targetWrapper.firstElementChild.cloneNode(true);
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
      const localPreferences = this.getLastPreferences();
      this.createWrapperBanner(true);
      this.createBanner(true);
      Object.keys(localPreferences?.services).forEach(component => {
        const componentToggle = this.bannerMain.querySelector(`.${CB_PREFIX}component input[name="${component}"]`);
        if (localPreferences?.services[component].consent !== componentToggle.checked) componentToggle.click();
      });
    });
  };
  this.closeAndRemoveBanner = () => {
    let existBanner = document.querySelector(`div.${CB_NAME}`);
    if (!!existBanner) existBanner.remove();
  };
  this.createWrapperBanner = (isOverlay = false) => {
    console.log('CreateWrapperBanner');
    this.closeAndRemoveBanner();
    this.banner = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('div', {
      className: [CB_NAME, isOverlay ? 'bb-cb-overlay' : `bb-${this.bannerPreferences?.layout ?? 'cb-bottom'}`].join(" "),
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
    if (document.querySelector('.' + CB_PREFIX + 'stage') == null) {
      document.querySelector('.' + CB_PREFIX + 'body').appendChild(this.bannerStage);
      document.querySelector('.' + CB_NAME).classList.add('visible');
    }
  };
  this.createBannerOverlay = () => {
    console.log('createBannerOverlay');
    this.banner.classList.remove("bb-cb-bottom", 'visible');
    this.banner.classList.add('bb-cb-overlay', 'visible');
  };
  this.createPreferences = () => {
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
              'data-group-name': group?.title,
              'data-component-title': component?.title,
              checked: !!group.lockedAndActive,
              disabled: !!group.lockedAndActive
            }));
          }
        }
        if ((groupComponents.children.length > 0 || group.lockedAndActive) && (groupComponents.children.length > 0 || !group.lockedAndActive)) {
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
  /**
   * @property {Object} displayTexts
   * @param {boolean} isOverlay
   * @return {*}
   */
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
      type: 'button',
      innerText: buttonLabels?.saveAndClose
    }, containerButtons);
    this.acceptAllButton = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('button', {
      className: ['bb-button', 'bb-btn--typeS', 'bb-show-all'].join(' '),
      type: 'button',
      innerText: buttonLabels?.acceptAll
    }, containerButtons);
    this.confirmSelectionButton = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.createElementWithAttrs)('button', {
      className: ['bb-button', 'bb-btn--typeS', 'bb-show-bottom'].join(' '),
      type: 'button',
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
  /**
   * @property {Object} footerNavigation
   * @property {string} showInfo
   * @return {*}
   */
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
    this.showCookieInfoButton?.addEventListener('click', async () => {
      console.log('click show cookie information');
      const {
        default: CookieInformation
      } = await __webpack_require__.e(/* import() */ "Resources_Public_Assets_JavaScript_Lib_CookieInformation_js").then(__webpack_require__.bind(__webpack_require__, "./Resources/Public/Assets/JavaScript/Lib/CookieInformation.js"));
      const cookieInfo = new CookieInformation(this.bannerPreferences);
      cookieInfo.show();
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
      innerText: `User-ID: ${this.getUserHash()}`
    });
    containerFooterCell.appendChild(linkContainer);
    containerFooter.appendChild(containerFooterCell);
    containerFooter.appendChild(identificationContainer);
    return containerFooter;
  };
  /**
   * @return {void}
   */
  this.handlePlaceholderElements = () => {
    console.log('handlePlaceholderElements');
    const placeholderContentElements = document.querySelectorAll('div[data-placeholder]');
    //data-type=iframe
  };
  /**
   * @return {void}
   */
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
  /**
   * @param {Object} consentServiceData
   * @return {void}
   */
  this.savePreferences = consentServiceData => {
    console.log('savePreferences');
    const lastPreferences = this.getLastPreferences();
    const userConsentLogData = {
      hash: "",
      services: {},
      version: "",
      timestamp: ""
    };
    userConsentLogData.hash = lastPreferences.hash;
    userConsentLogData.services = consentServiceData;
    lastPreferences.services = Object.fromEntries(Object.entries(consentServiceData).map(service => {
      return [service[0], {
        'title': service[1]?.title,
        'consent': service[1]?.consent
      }];
    }));
    lastPreferences.timestamp = userConsentLogData.timestamp = Math.floor(Date.now() / 1000);
    lastPreferences.version = userConsentLogData.version = this.bannerPreferences?.banner?.version;
    this.localPreferences = lastPreferences;
    this.cookiePreferences = Object.fromEntries(Object.entries(consentServiceData).map(service => {
      return [service[0], service[1]?.consent];
    }));
    this.setUserConsentCookieServices();
    this.setLocalStorageData();
    this.saveLogUserConsent(userConsentLogData);
    this.closeAndRemoveBanner();
    this.handlePlaceholderElements();
  };
  /**
   *
   * @return {*|{}}
   */
  this.getLastPreferences = () => {
    console.log('getLastPreferences');
    return localStorage.getItem(LAST_PREFERENCES_NAME) ? JSON.parse(localStorage.getItem(LAST_PREFERENCES_NAME)) : {};
  };
  /**
   *
   * @return {boolean}
   */
  this.isPreferencesCookie = () => {
    console.log('isPreferencesCookie');
    return Object.keys(this.cookiePreferences).length !== 0;
  };
  /**
   *
   * @return {boolean}
   */
  this.isPreferencesLocalStorage = () => {
    console.log('isPreferencesLocalStorage');
    return Object.keys(this.localPreferences).length !== 0;
  };
  /**
   * hash is the generate browser fingerprint hash
   * @return {string}
   */
  this.getUserHash = () => {
    const lastPreferences = this.getLastPreferences();
    let hash = lastPreferences?.hash;
    if (!hash) {
      hash = (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.generateUserHash)();
      lastPreferences.hash = hash;
      window.localStorage.setItem(LAST_PREFERENCES_NAME, JSON.stringify(lastPreferences));
    }
    return hash;
  };
  /**
   * @param {Object} userConsentLogData
   * @return {Promise<void>}
   */
  this.saveLogUserConsent = async userConsentLogData => {
    console.log('saveLogUserConsent');
    const url = "/api/consent/save";
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(userConsentLogData)
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
  /**
   * @property {string} componentTitle
   * @return {Object}
   */
  this.collectData = () => {
    const formPreferencesContainer = this.bannerMain.querySelector(`.${CB_PREFIX}preferences`);
    return Object.fromEntries(Array.from(formPreferencesContainer.querySelectorAll(`.${CB_PREFIX}component input[data-group-id]`)).map(el => {
      return [el.name, {
        'title': el.dataset.componentTitle,
        'consent': el.checked,
        'groupId': el.dataset.groupId,
        'groupName': el.dataset.groupName
      }];
    }));
  };
  /**
   *
   * @param {boolean} value
   * @return {*}
   */
  this.collectAndModifyData = value => {
    const data = this.collectData();
    for (let key of Object.keys(data)) data[key].consent = value;
    return data;
  };
  /**
   * @return {void}
   */
  this.setUserConsentCookieServices = () => {
    cookieUtils.set(LAST_PREFERENCES_NAME, JSON.stringify(this.cookiePreferences) + ';secure;samesite=lax', this.bannerPreferences?.lifetimes?.userConsent);
  };
  /**
   * @return {void}
   */
  this.setLocalStorageData = () => {
    window.localStorage.setItem(LAST_PREFERENCES_NAME, JSON.stringify(this.localPreferences));
  };
  /**
   * checks if the banner needs to be created
   * @property {Object} lifetimes
   * @return {boolean}
   */
  this.shouldCreateBanner = () => {
    const localStoragePreferences = this.getLastPreferences();
    return Object.keys(this.cookiePreferences).length === 0 || Object.keys(localStoragePreferences?.services ?? {}).length === 0 || localStoragePreferences?.version !== this.bannerPreferences?.banner?.version || localStoragePreferences?.hash !== this.getUserHash() || (0,_Lib_utils__WEBPACK_IMPORTED_MODULE_0__.hasDaysPassed)(localStoragePreferences?.timestamp, this.bannerPreferences?.lifetimes?.banner);
  };
};
new CbManager().init();
})();

/******/ })()
;
//# sourceMappingURL=CbLoader.js.map