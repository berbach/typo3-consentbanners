(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory();
	else if(typeof define === 'function' && define.amd)
		define([], factory);
	else if(typeof exports === 'object')
		exports["BbModule"] = factory();
	else
		root["BbModule"] = root["BbModule"] || {}, root["BbModule"]["BaseCookieBanner"] = factory();
})(self, function() {
return /******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./Resources/Public/Assets/Src/JavaScript/Lib/debug.js":
/*!*************************************************************!*\
  !*** ./Resources/Public/Assets/Src/JavaScript/Lib/debug.js ***!
  \*************************************************************/
/***/ (function(module) {

let _debug = {};
_debug.output = true;
_debug.getArguments = function (args) {
  var arr = [];
  for (var i = 0; i < args.length; i++) {
    arr[i] = args[i];
  }
  return arr;
};
_debug.info = function () {
  this.write("info", this.getArguments(arguments));
};
_debug.log = function () {
  this.write("log", this.getArguments(arguments));
};
_debug.warn = function () {
  this.write("warn", this.getArguments(arguments));
};
_debug.error = function () {
  this.write("error", this.getArguments(arguments));
};
_debug.debug = function () {
  this.write("debug", this.getArguments(arguments));
};
_debug.write = function (level, args) {
  if (this.output && typeof console === "object") if (typeof InstallTrigger !== 'undefined') console[level].apply(this, args);else if (Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0) window.console.log(args[0]);else window.console[level](args);
};
module.exports = _debug;

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
var __webpack_exports__ = {};
/*!********************************************************************!*\
  !*** ./Resources/Public/Assets/Src/JavaScript/BaseCookieBanner.js ***!
  \********************************************************************/
// import jQuery from "jquery";
// window.$ = window.jQuery = jQuery;

const Debug = __webpack_require__(/*! ./Lib/debug */ "./Resources/Public/Assets/Src/JavaScript/Lib/debug.js");
//const Cookie = require('./Lib/cookie');

let ModulesManager = function () {
  this.bodyNode = document.querySelector('body');
};
ModulesManager.prototype.toAbsoluteURL = function (url) {
  const a = document.createElement("a");
  a.setAttribute("href", url); // <a href="hoge.html">
  return a.cloneNode(false).href; // -> "http://example.com/hoge.html"
};

ModulesManager.prototype.importModule = function (url) {
  let _self = this;
  try {
    // if dynamic import is supported, don't bother with the stuff below
    return new Function(`return import("${url}")`)();
  } catch (err) {}
  return new Promise((resolve, reject) => {
    const vector = "$importModule$" + Math.random().toString(32).slice(2);
    const script = document.createElement("script");
    const destructor = () => {
      delete window[vector];
      script.onerror = null;
      script.onload = null;
      script.remove();
      URL.revokeObjectURL(script.src);
      script.src = "";
    };
    script.defer = "defer";
    script.type = "module";
    script.onerror = () => {
      reject(new Error(`Failed to import: ${url}`));
      destructor();
    };
    script.onload = () => {
      resolve(window[vector]);
      destructor();
    };
    const absURL = _self.toAbsoluteURL(url);
    const loader = `import * as m from "${absURL}"; window.${vector} = m;`; // export Module
    const blob = new Blob([loader], {
      type: "text/javascript"
    });
    script.src = URL.createObjectURL(blob);
    document.head.appendChild(script);
  });
};
ModulesManager.prototype.loadScript = function (url, cb) {
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
ModulesManager.prototype.init = function () {
  this.getModuleNodes();
};
ModulesManager.prototype.getModuleNodes = function () {
  let self = this,
    eMod = document.querySelectorAll('[data-module-cb]');
  for (let index in eMod) {
    const el = eMod[index];
    if (!(el instanceof HTMLElement)) continue;
    let module = el.dataset.moduleCb,
      mOptions = el.dataset.options || '';
    if (module !== 'undefined') {
      let moduleArr = module.split(" ");
      moduleArr.forEach(value => {
        if (!!window.MSInputMethodContext && !!document.DOCUMENT_NODE) {
          self.loadScript(`./Module/${value}.js`, () => {
            BbModule[value].init(el, mOptions);
          });
        } else {
          self.importModule(`./Module/${value}.js`).then(() => {
            BbModule[value].init(el, mOptions);
          });
        }
      });
    } else {
      Debug.log("Module name not found, called by", el);
    }
  }
};
let Node = new ModulesManager();
Node.init();
/******/ 	return __webpack_exports__;
/******/ })()
;
});