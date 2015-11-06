/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

var ExtractTextPlugin = require('extract-text-webpack-plugin');
var webpack           = require('webpack');

module.exports = function (environment) {
    if (-1 === ['dev', 'prod'].indexOf(environment)) {
        throw new Error('Environment must be "dev" or "prod"!');
    }

    var plugins = [new ExtractTextPlugin('[name].css')], cssQueryString;
    if ('prod' === environment) {
        plugins.push(new webpack.optimize.UglifyJsPlugin({
            comments: /^\\/
        })); // quick and dirty fix that doesn't match any comments

        cssQueryString = 'minimize&sourceMap';
    }

    cssQueryString = 'css-loader?' + cssQueryString + '!sass-loader?outputStyle=expanded&sourceMap';

    var config = {
        entry: {
            js:     './src/Frontend/App.js',
            bundle: './src/Frontend/styles/custom.scss'
        },
        module: {
            loaders: [
                {
                    test:    /\.js$/,
                    loaders: ['babel-loader?stage=0']
                },
                {
                    test:   /\.scss$/,
                    loader: ExtractTextPlugin.extract('style-loader', cssQueryString)
                }
            ]
        },
        resolve: {
            modulesDirectories: ['node_modules'],
            extensions:         ['.js', '', '.scss']
        },
        output: {
            path:       './web/build',
            filename:   'bundle.js'
        },
        externals: {
            'React': 'react'
        },
        plugins: plugins
    };

    if ('dev' === environment) {
        config.devtool = 'inline-source-map';
    }

    return config;
};
