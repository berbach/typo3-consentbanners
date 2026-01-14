import './Lib/PublicPath';
import Debug from './Lib/debug';
import BrowserFingerprint from "../../JavaScript/Lib/fingerprint-generator";

window.DEVMODE = process.env.NODE_ENV !== 'production' ?? false;

Debug.setDevMode(DEVMODE);

const CbModulesManager = function ()  {

    this.basePath = window.__BASE_PUBLIC_PATH__ || './';

    this.init = () => this.getModuleNodes();


    this.getModuleNodes = async () => {
        const fingerprint = new BrowserFingerprint();
        const [hash] = await Promise.all([fingerprint.generateHash()]);
        console.log(hash);
        console.log(hash === 'e6771f56478b3bdaabb184da49cd07d3d794464d83cc2f39eac75cbd7f6b559f')
        const eMod = document.querySelectorAll('[data-module-cb]');
        eMod.forEach(el => {
            if (!(el instanceof HTMLElement)) return;

            const modules = el.dataset.moduleCb?.split(' ') || [];
            const options = el.dataset.options || '';

            modules.forEach(moduleName => {
                if (!moduleName) return;
                this.loadModule(moduleName, el, options);
            });
        });
    }

    this.loadModule = (moduleName, el, options) => {
        if (!this.supportsStaticImport()) {
            this.loadScript(`${this.basePath}JavaScript/Module/${moduleName}.js`, () => {
                window.CbModule[moduleName].init(el, options);
                Debug.log('Module loaded with script', { ModuleName: moduleName, Element: el, options });
            });
        } else {
            import(/*webpackIgnore: true*/ `${this.basePath}JavaScript/Module/${moduleName}.js`)
                .then(m => {
                    window.CbModule[moduleName].init(el, options);
                    Debug.log('Module loaded with import()', { ModuleName: moduleName, Element: el, options });
                })
                .catch(err => console.log('Module ' + moduleName + ' Error: '+ err.stack));

        }
    }

    this.toAbsoluteURL = function (url) {
        const a = document.createElement("a");
        a.setAttribute("href", url); // <a href="hoge.html">
        return a.cloneNode(false).href; // -> "http://example.com/hoge.html"
    }

    this.loadScript = function (url, cb) {
        const script = document.createElement('script');
        let loaded;
        script.src = this.toAbsoluteURL(url);
        if (cb) {
            script.onload = script.onreadystatechange = function () {
                if (!loaded) cb();
                loaded = true;
            };
        }
        document.head.appendChild(script);
    }

    this.supportsStaticImport = function() {
        const script = document.createElement('script');
        return 'noModule' in script;
    }
};

new CbModulesManager().init();