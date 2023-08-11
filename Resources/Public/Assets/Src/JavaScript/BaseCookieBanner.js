// import jQuery from "jquery";
// window.$ = window.jQuery = jQuery;

const Debug = require('./Lib/debug');
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

    try { // if dynamic import is supported, don't bother with the stuff below
        return (new Function(`return import("${url}")`))();
    } catch (err) {
    }

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
}

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
        const el = eMod[index]
        if (!(el instanceof HTMLElement)) continue
        let module = el.dataset.moduleCb,
            mOptions = el.dataset.options || '';

        if (module !== 'undefined') {
            let moduleArr = module.split(" ");
            moduleArr.forEach((value) => {
                if (!!window.MSInputMethodContext && !!document.DOCUMENT_NODE) {
                    self.loadScript(`./Module/${value}.js`, () => {
                        BbModule[value].init(el, mOptions);
                    });
                } else {
                    self.importModule(`./Module/${value}.js`)
                        .then(() => {
                            BbModule[value].init(el, mOptions);
                        });
                }
            })
        } else {
            Debug.log("Module name not found, called by", el);
        }
    }
};

let Node = new ModulesManager();
Node.init();
