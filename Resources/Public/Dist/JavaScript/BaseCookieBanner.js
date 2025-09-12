(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory();
	else if(typeof define === 'function' && define.amd)
		define([], factory);
	else if(typeof exports === 'object')
		exports["BbCb"] = factory();
	else
		root["BbCb"] = root["BbCb"] || {}, root["BbCb"]["BaseCookieBanner"] = factory();
})(self, function() {
return /******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./Resources/Public/Assets/Src/JavaScript/Lib/debug.js":
/*!*************************************************************!*\
  !*** ./Resources/Public/Assets/Src/JavaScript/Lib/debug.js ***!
  \*************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
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
/* harmony default export */ __webpack_exports__["default"] = (Debug);

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
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
!function() {
/*!********************************************************************!*\
  !*** ./Resources/Public/Assets/Src/JavaScript/BaseCookieBanner.js ***!
  \********************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Lib_debug__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Lib/debug */ "./Resources/Public/Assets/Src/JavaScript/Lib/debug.js");

window.DEVMODE = "development" !== 'production' ?? 0;
_Lib_debug__WEBPACK_IMPORTED_MODULE_0__["default"].setDevMode(DEVMODE);
let CbModulesManager = function () {
  this.bodyNode = document.querySelector('body');
  this.init = () => {
    this.getModuleNodes();
  };
  this.getModuleNodes = () => {
    let eMod = document.querySelectorAll('[data-module-cb]');
    for (let index in eMod) {
      const el = eMod[index];
      if (!(el instanceof HTMLElement)) continue;
      let module = el.dataset.moduleCb;
      if (module !== 'undefined') {
        let moduleArr = module.split(" ");
        moduleArr.forEach(value => {
          if (!supportsStaticImport()) {
            this.loadScript(`./Module/${value}.js`, () => {
              BbCb[value].init(el);
            });
          } else {
            this.importModule(`./Module/${value}.js`).then(m => {
              _Lib_debug__WEBPACK_IMPORTED_MODULE_0__["default"].log(BbCb[value]);
              BbCb[value].init(el);
              _Lib_debug__WEBPACK_IMPORTED_MODULE_0__["default"].log("Module", value, "initialized");
            });
            // .catch(error => Debug.log("Module", value, "ERROR", error));
          }
        });
      } else {
        _Lib_debug__WEBPACK_IMPORTED_MODULE_0__["default"].log("Module name not found, called by", el);
      }
    }
  };
  this.importModule = url => {
    try {
      return import(/*webpackIgnore: true*/`${url}`);
    } catch (err) {}
  };
  this.toAbsoluteURL = function (url) {
    const a = document.createElement("a");
    a.setAttribute("href", url); // <a href="hoge.html">
    return a.cloneNode(false).href; // -> "http://example.com/hoge.html"
  };
  this.loadScript = function (url, cb) {
    let script = document.createElement('script'),
      loaded;
    script.setAttribute('src', this.toAbsoluteURL(url));
    if (cb) {
      script.onreadystatechange = script.onload = function () {
        if (!loaded) {
          cb();
        }
        loaded = true;
      };
    }
    document.getElementsByTagName('head')[0].appendChild(script);
  };
  // https://gist.github.com/ebidel/3201b36f59f26525eb606663f7b487d0
  const supportsStaticImport = function () {
    const script = document.createElement('script');
    return 'noModule' in script;
  };
};
let Node = new CbModulesManager();
Node.init();
}();
/******/ 	return __webpack_exports__;
/******/ })()
;
});
//# sourceMappingURL=BaseCookieBanner.js.map