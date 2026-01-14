const path = require('node:path');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const webpack = require('webpack');
const file = require('./fileHelper');

const isDevelopment = process.env.npm_lifecycle_event.includes('dev');

const ROOT = path.resolve(__dirname + '/Resources/Public/');
const SRC = path.join(ROOT, 'Assets');
const ScssDir = path.join(SRC, 'Scss');

const OutputDir = path.resolve(__dirname + '/Resources/Public/', 'Dist');

const config = {
    target: 'web',
    stats: {
        warnings: false,
        //errorDetails: false,
        //loggingDebug: ["sass-loader"],
    },
    entry: file.getScssEntries(ScssDir),
    /**
     * The "output" property is what our build files will be named and where the
     * build file will be placed
     */
    output: {
        filename: 'Css/[name].js',
        /**
         * We need to provide an absolute path to the root of our project and
         * that's exactly what this line is doing
         */
        path: OutputDir,
    },
    cache: {
        type: 'filesystem',
    },
    module: {
        rules: [
            {
                test: /\.s[ac]ss$/,
                exclude: /node_modules/,
                use: [{
                    loader: MiniCssExtractPlugin.loader,
                }, {
                    loader: 'css-loader',
                    options: {
                        importLoaders: 1,
                        sourceMap: isDevelopment,
                    }
                }, {
                    loader: 'postcss-loader',
                    options: {
                        sourceMap: isDevelopment,
                        postcssOptions: {
                            config: path.resolve(__dirname, "postcss.config.js"),
                        }
                    }
                }, {
                    loader: 'sass-loader',
                    options: {
                        sourceMap: isDevelopment,
                        sassOptions: {
                            outputStyle: isDevelopment ? "expanded" : "expanded",
                            quietDeps: true
                        },
                    }
                }]
            },
            {
                test: /\.(svg|png|jpe?g|gif)$/i,
                type: 'asset/resource',
                generator: {
                    filename: 'Images/[name][ext]',
                },
            },
            {
                test: /\.(woff|woff2|eot|ttf|otf)$/,
                type: 'asset/resource',
                generator: {
                    filename: 'Fonts/[name][ext]',
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
                parallel: true,
                minify: CssMinimizerPlugin.cssnanoMinify,
                minimizerOptions: {
                    preset: [
                        "default",
                        {
                            discardUnused: true,
                            mergeIdents: true,
                            reduceIdents: true,
                            zindex: false,
                            discardComments: { removeAll: true },
                        },
                    ],
                },
            }),
        ]
    },
    resolve: {
        extensions: ['.css', '.scss', '.sass']
    },
    performance: {
        hints: false
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'Css/[name].css',
            chunkFilename: 'Css/[id].css',
        }),
        new CleanWebpackPlugin({
            // needed to delete the js files
            protectWebpackAssets: false,
            cleanOnceBeforeBuildPatterns: ['Css/**/*.css', 'Css/**/*.css.map'],
            cleanAfterEveryBuildPatterns: ['Css/**/Style.js.map', 'Css/**/Style.js', 'Css/**/Rte.js.map', 'Css/**/Rte.js', 'Css/**/Print.js.map', 'Css/**/Print.js', 'Css/**/*.js.map', 'Css/**/*.js']
        }),
        new webpack.ProgressPlugin(),
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
