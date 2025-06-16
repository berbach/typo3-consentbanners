const path = require('path');
const fileHelper = require('./fileHelper');
const webpack = require('webpack');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');
const TerserPlugin = require("terser-webpack-plugin");

const isDevelopment = process.env.npm_lifecycle_event.includes('dev');

const config = {
    target: ['web', 'es5'],
    entry: fileHelper.getEntries(
        [
            './Resources/Public/Assets/Src/JavaScript/*.js',
            './Resources/Public/Assets/Src/JavaScript/Module/*.js',
        ]),
    /**
     * The "output" property is what our build files will be named and where the
     * build file will be placed
     */
    output: {
        /**
         * Again, the "[name]" place holder will be replaced with each key in our
         * "entry" object and will name the build file "main.js"
         */
        //filename: '[name].js',
        filename: (chunkData) => {
            if (['BaseCookieBanner'].includes(chunkData.chunk.name))
                return 'JavaScript/[name].js'

            return 'JavaScript/Module/[name].js'
        },
        chunkFilename: (chunkData) => {
            if (typeof chunkData.chunk.id !== 'string') {
                console.warn('Set optimization.chunkIds to "named" for readable chunk names.')
                return 'JavaScript/Chunks/[id].js'
            }
            const namedId = chunkData.chunk.id.replace(/_js.*?$/, '.js').split('_')
            let name = namedId.pop()
            name = namedId.pop() + '.' + name
            return 'JavaScript/Chunks/' + name
        },
        library: {
            name: ["BbCb", "[name]"],
            type: 'umd'
        },
        path: path.resolve(__dirname + '/Resources/Public/', 'Dist'),
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /(node_modules|bower_components)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        sourceType: 'unambiguous',
                        presets: ['@babel/preset-env'],
                        plugins: [
                            ['@babel/plugin-syntax-dynamic-import'],
                            ['@babel/plugin-transform-runtime']
                        ],
                    }
                }
            }
        ],
    },
    optimization: {
        moduleIds: isDevelopment ? 'named' : 'deterministic',
        chunkIds: 'named',
        mergeDuplicateChunks: false,
        minimize: !isDevelopment,
        minimizer: [
            new TerserPlugin({
                test: /\.js(\?.*)?$/i,
                //minify: TerserPlugin.uglifyJsMinify,
                extractComments: false,
                parallel: true,
                terserOptions: {
                    module: false,
                    compress: {
                        arguments: true,
                        arrows: true,
                        drop_console: true,
                        hoist_funs: true,
                        hoist_props: true,
                        keep_fargs: false,
                        unsafe: true,
                        passes: 2,
                        toplevel: true,
                    },
                    mangle: {
                        toplevel: true,
                        module: true,
                    },
                    nameCache: {},
                    parse: {},
                    //output: {},
                    toplevel: false,
                    ie8: true,
                    keep_fnames: true,
                    keep_classnames: true,
                    format: {
                        comments: false,
                    }
                },
            }),
            new TerserPlugin({minify: TerserPlugin.uglifyJsMinify})
        ],
    },
    plugins: [
        new webpack.ProgressPlugin(),
        new CleanWebpackPlugin({
            protectWebpackAssets: false,
            cleanOnceBeforeBuildPatterns: ['**/BaseCookieBanner.js', '**/BaseCookieBanner.js.map']
        })
    ],
};

module.exports = (env, argv) => {
    if (argv.mode === 'development') {
        config.mode = 'development';
        config.devtool = 'source-map';
    }

    if (argv.mode === 'production') {
        config.mode = 'production';
    }
    return config;
};
