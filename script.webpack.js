const path = require('path');
const fileHelper = require('./fileHelper');
const webpack = require('webpack');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');
const TerserPlugin = require("terser-webpack-plugin");

const isDevelopment = process.env.npm_lifecycle_event === 'script:dev';
const productionMode = process.env.NODE_ENV === 'production';

const config = {
    target: ['web', 'es5'],
    entry: fileHelper.getEntries(
        [
            './Resources/Public/Assets/Src/JavaScript/BaseCookieBanner.js',
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
            return chunkData.chunk.name === 'BaseCookieBanner' ? 'JavaScript/[name].js' : 'JavaScript/Module/[name].js'
        },
        /**
         * We need to provide an absolute path to the root of our project and
         * thats exactly what this line is doing
         */
        path: path.resolve(__dirname + '/Resources/Public/Dist/'),
        //publicPath: '/typo3conf/ext/cookiebanner/Resources/Public/Dist/',
        library: ["BbModule", "[name]"],
        libraryTarget: "umd"
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /(node_modules|bower_components)/,
                use: {
                    loader: 'babel-loader'
                }
            },
            {
                test: /\.(svg|png|jpe?g|gif)$/i,
                loader: 'file-loader',
                options: {
                    name: '[name].[ext]',
                    outputPath: 'Images',
                },
            },
            {
                test: /\.(woff|woff2|eot|ttf|otf)$/,
                loader : 'file-loader',
                options: {
                    name: '[name].[ext]',
                    outputPath: 'Fonts',
                },

            },
        ],
    },
    resolve: {
        extensions: [".js", ".css", ".scss"]
    },
    performance: {
        hints: false,
        assetFilter: function (assetFilename) {
            // Function predicate that provides asset filenames
            return assetFilename.endsWith('.css') || assetFilename.endsWith('.js');
        }
    },
    optimization: {
        moduleIds: 'named',
        chunkIds: 'named',
        mergeDuplicateChunks: false,
        minimize: (process.env.npm_lifecycle_event === 'script'),
        minimizer: [
            new TerserPlugin({
                test: /\.js(\?.*)?$/i,
                extractComments: false,
                terserOptions: {
                    compress: productionMode, // only if `--mode production` was passed
                    format: {
                        comments: false,
                    },
                    ie8: true,
                },
            }),
        ],
    },
    plugins: [
        new webpack.ProgressPlugin(),
        new CleanWebpackPlugin({
            cleanOnceBeforeBuildPatterns: ['**/BaseCookieBanner.js.map'],
            cleanAfterEveryBuildPatterns: []
        })
    ],

};

module.exports = (env, argv) => {
    if (argv.mode === 'development') {
        config.devtool = false;
    }

    if (argv.mode === 'production') {
        config.mode = 'production';
    }
    return config;
};
