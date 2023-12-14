const path = require('path');
const webpack = require('webpack');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");
const fileHelper = require('./fileHelper');

const productionMode = process.env.NODE_ENV === 'production';
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
                            // importLoaders: 1,
                            sourceMap: !productionMode,
                            //publicPath: '../Css'
                        }
                    },
                    {
                        loader: "postcss-loader",
                        options: {
                            sourceMap: !productionMode,
                        }
                    },
                    {
                        loader: "sass-loader",
                        options: {
                            sourceMap: !productionMode,
                            // sassOptions: {
                            //     outputStyle: 'compressed',
                            // },
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
        minimize: productionMode,
        minimizer: [
            new CssMinimizerPlugin({
                test: /\.css$/i,
                minimizerOptions: {
                    level: {
                        1: {
                            roundingPrecision: "all=3,px=5",
                        },
                    },
                    preset: [
                        "default",
                        {
                            discardComments: { removeAll: true },
                        },
                    ],
                },
                minify: CssMinimizerPlugin.cleanCssMinify,
            }),
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
            cleanOnceBeforeBuildPatterns: [
                path.join(__dirname + '/Resources/Public/Dist/', './Css/*.css'),
                path.join(__dirname + '/Resources/Public/Dist/', './Css/*.css.map')
            ],
            cleanAfterEveryBuildPatterns: [
                path.join(__dirname + '/Resources/Public/Dist/', './Css/CookieBanner.js'),
                path.join(__dirname + '/Resources/Public/Dist/', './Css/CookieBanner.js.map')
            ]
        }),
    ],

};
module.exports = (env, argv) => {

    if (argv.mode === 'development') {
        config.devtool = 'source-map';
    }

    if (argv.mode === 'production') {
        config.mode = 'production';
    }
    return config;
};
