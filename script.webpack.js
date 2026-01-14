const path = require('node:path');
const {CleanWebpackPlugin}  = require('clean-webpack-plugin');
const TerserPlugin = require("terser-webpack-plugin");
const webpack = require('webpack');
const file = require('./fileHelper');

const isDevelopment = process.env.npm_lifecycle_event.includes('dev');
const ROOT = path.resolve(__dirname + '/Resources/Public/');
const SRC = path.join(ROOT, 'Assets');
const JavaScriptDir = path.join(SRC, 'JavaScript');
const ModulesDir = path.join(JavaScriptDir, 'Module');

const OutputDir = path.resolve(__dirname + '/Resources/Public/', 'Dist');

const umdConfig = {
    target: ['web'],
    entry: file.getJsModuleEntries(ModulesDir),
    output: {
        filename: (chunkData) => {
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
            name: ["CbModule", "[name]"],
            type: 'umd'
        },
        iife: true,
        path: OutputDir,
    },
    plugins: [
        new CleanWebpackPlugin({
            protectWebpackAssets: false,
            cleanOnceBeforeBuildPatterns: ['JavaScript/Module/*.js', 'JavaScript/Module/*.js.map']
        }),
        new webpack.ProgressPlugin()
    ],
};

const esmConfig = {
    target: ['web'],
    entry: file.getJsEntries(JavaScriptDir),
    output: {
        filename: 'JavaScript/[name].js',
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
        // library: {
        //     //name: ["CbModule", "[name]"],
        //     type: 'var'
        // },
        iife: true,
        pathinfo: false,
        path: OutputDir,
    },
    plugins: [
        new CleanWebpackPlugin({
            protectWebpackAssets: false,
            cleanOnceBeforeBuildPatterns: ['JavaScript/*.js', 'JavaScript/*.js.map']
        }),
        new webpack.ProgressPlugin()
    ],
};

const globalScriptConfig = {
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

}

const config = {};

module.exports = (env, argv) => {
    if (argv.mode === 'development') {
        config.mode = 'development';
        config.devtool = 'source-map';
    }

    if (argv.mode === 'production') {
        config.mode = 'production';
        config.devtool = false
    }
    return [{...esmConfig, ...globalScriptConfig, ...config}, {...umdConfig, ...globalScriptConfig, ...config}];
};