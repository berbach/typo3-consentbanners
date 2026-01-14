const scripts = document.getElementsByTagName('script');
const baseScript = Array.from(scripts).find(s => s.src && s.src.includes('/JavaScript/ConsentBanner.js'));
if (baseScript) {
    const path = new URL(baseScript.src).pathname;
    __webpack_public_path__ = path.substring(0, path.indexOf('/JavaScript/ConsentBanner.js')) + '/';
}