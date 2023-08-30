const path = require('path');
const webpack = require('webpack');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");
const fileHelper = require('./fileHelper');

const isDevelopment = process.env.npm_lifecycle_event.includes('dev');

const config = {
    target: "web",
    entry: fileHelper.getEntries([
        './Resources/Public/Assets/Src/Scss/Styles.scss'
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
        filename: 'Css/CookieBanner.js',
        /**
         * We need to provide an absolute path to the root of our project and
         * thats exactly what this line is doing
         */
        path: path.resolve(__dirname + '/Resources/Public/', 'Dist')
    },
    stats: {
        children: true,
        assets: false
    },
    module: {
        rules: [
            {
                test: /\.(sa|sc)ss$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    {
                        loader: "css-loader",
                        options: {
                            importLoaders: 1,
                            sourceMap: isDevelopment,
                            //publicPath: '../Css'
                        }
                    },
                    {
                        loader: "postcss-loader",
                        options: {
                            sourceMap: isDevelopment,
                        }
                    },
                    {
                        loader: "sass-loader",
                        options: {
                            sourceMap: isDevelopment,
                            sassOptions: {
                                outputStyle: 'compressed',
                            },
                        }
                    }
                ]
            },
            {
                test: /\.(svg|png|jpe?g|gif)$/i,
                loader: 'file-loader',
                options: {
                    name: '[name].[ext]',
                    outputPath: 'Images',
                    publicPath: '../Images'
                },
            },
            {
                test: /\.(woff|woff2|eot|ttf|otf)$/,
                loader : 'file-loader',
                options: {
                    name: '[name].[ext]',
                    outputPath: 'Fonts',
                    publicPath: '../Fonts'
                },

            },
        ],
    },
    optimization: {
        moduleIds: 'named',
        chunkIds: 'named',
        mergeDuplicateChunks: false,
        removeEmptyChunks: false,
        minimize: !isDevelopment,
        minimizer: [
            new CssMinimizerPlugin({
                parallel: 4,
                minimizerOptions: [{
                    level: {
                        1: {
                            roundingPrecision: "all=3",
                        },
                        2: {},
                    },
                }, {
                    preset: [
                        "default",
                        {
                            discardComments: {removeAll: true}
                        }
                    ]
                }],
                minify: [
                    CssMinimizerPlugin.cleanCssMinify,
                    CssMinimizerPlugin.cssnanoMinify,
                    CssMinimizerPlugin.cssoMinify,
                ],
            })
        ]
    },
    resolve: {
        extensions: ['css', 'scss', 'sass']
    },
    performance: {
        hints: false
    },
    plugins: [
        new webpack.ProgressPlugin(),
        new MiniCssExtractPlugin({
            filename: 'Css/[name].css',
            chunkFilename: 'Css/[id].css',
            ignoreOrder: true,
        }),
        new CleanWebpackPlugin({
            protectWebpackAssets: false,
            leanOnceBeforeBuildPatterns: ['Css/**/*.css', 'Css/**/*.css.map'],
            cleanAfterEveryBuildPatterns: ['Css/**/CookieBanner.js.map', 'Css/**/CookieBanner.js']
        }),
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
