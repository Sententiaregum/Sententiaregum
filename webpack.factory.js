/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
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
