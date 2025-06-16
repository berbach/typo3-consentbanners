const Debug = require('./Lib/debug');

let CbModulesManager = function () {
    this.bodyNode = document.querySelector('body');

    this.init = () => {
        this.getModuleNodes();
    };

    this.getModuleNodes = () => {
        let eMod = document.querySelectorAll('[data-module-cb]');

        for (let index in eMod) {
            const el = eMod[index]
            if (!(el instanceof HTMLElement)) continue
            let module = el.dataset.moduleCb

            if (module !== 'undefined') {
                let moduleArr = module.split(" ");
                moduleArr.forEach((value) => {

                    if (!supportsStaticImport()) {
                        this.loadScript(`./Module/${value}.js`, () => {
                            BbCb[value].init(el);
                        });
                    } else {
                        this.importModule(`./Module/${value}.js`)
                            .then((m) => {
                                Debug.log(BbCb[value]);
                                BbCb[value].init(el);
                                Debug.log("Module", value, "initialized");
                            })
                            .catch(error => Debug.log("Module", value, "ERROR", error));
                    }
                })
            } else {
                Debug.log("Module name not found, called by", el);
            }
        }
    }

    this.importModule = (url) => {
        try {
            return import(/*webpackIgnore: true*/`${url}`);
        } catch (err) {}
    }

    this.toAbsoluteURL = function (url) {
        const a = document.createElement("a");
        a.setAttribute("href", url); // <a href="hoge.html">
        return a.cloneNode(false).href; // -> "http://example.com/hoge.html"
    }

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
    }
    // https://gist.github.com/ebidel/3201b36f59f26525eb606663f7b487d0
    const supportsStaticImport = function() {
        const script = document.createElement('script');
        return 'noModule' in script;
    }
};

let Node = new CbModulesManager();
Node.init();