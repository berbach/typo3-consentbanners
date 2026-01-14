const scripts = document.getElementsByTagName('script');
const baseLoaderScript = Array.from(scripts).find(s => s.src && s.src.includes('/JavaScript/CbLoader.js'));
if (baseLoaderScript) {
    const path = new URL(baseLoaderScript.src).pathname;
    __webpack_public_path__ = path.substring(0, path.indexOf('/JavaScript/CbLoader.js')) + '/';
    window.__BASE_PUBLIC_PATH__ = __webpack_public_path__;
}