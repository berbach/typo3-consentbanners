const path = require('path');
const fg = require('fast-glob');

let fileHelper = {};

fileHelper.getEntries = (data) => {
    //Get all files under the globPath path
    let entries;
    entries = {};
    //loop entries
    data.forEach( (value) => {
        const files = fg.sync(`${value}`, {
            dot: false,
            unique: true
        });

        files.forEach((file) => {
            let dirname = path.dirname(file);//The name of the folder where the return path is located
            let extname = path.extname(file);//Returns the extended name of the specified file name
            /**
             * path.basename(p, [ext])
             * Returns the specified filename, which excludes the [ext] suffix string
             * path.basename('/foo/bar/baz/asdf/quux.html', '.html')=>quux
             */
            let basename = path.basename(file, extname);
            let pathname = path.join(dirname, basename);//Path merging

            if(basename === 'Styles' && extname === '.scss'){
                entries['CookieBanner'] = file;
            }else{
                entries[basename] = file;
            }

        });
    });

    return entries;




}

module.exports = fileHelper;

