const path = require('node:path');
const fs = require('node:fs');

module.exports = {
    getScssEntries: (ScssDir) => {
        return fs.existsSync(ScssDir)
            ? Object.fromEntries(
                fs.readdirSync(ScssDir)
                    .filter(f => f.endsWith('.scss') && !f.startsWith('_'))
                    .map(f => [f.replace(/\.scss$/, '') === 'Styles' ? 'ConsentBanner' : f.replace(/\.scss$/, ''), path.join(ScssDir, f)])
            )
            : {}
    },
    getJsEntries: (JavaScriptDir) => {
        return fs.existsSync(JavaScriptDir)
            ? Object.fromEntries(
                fs.readdirSync(JavaScriptDir)
                    .filter(f => f.endsWith('.js') && !f.startsWith('_'))
                    .map(f => [f.replace(/\.js$/, ''), path.join(JavaScriptDir, f)])
            )
            : {}
    },
    getJsModuleEntries: (modulesDir) => {
        return fs.existsSync(modulesDir)
            ? Object.fromEntries(
                fs.readdirSync(modulesDir)
                    .filter(f => f.endsWith('.js') && !f.startsWith('_'))
                    .map(f => [f.replace(/\.js$/, ''), path.join(modulesDir, f)])
            )
            : {}
    },
}

