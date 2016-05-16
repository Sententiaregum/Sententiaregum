/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

var webpack = require('webpack');
var ExtractTextPlugin = require('extract-text-webpack-plugin');

function createConfig() {
  "use strict";

  var plugins      = [new ExtractTextPlugin('[name].css')],
    cssQueryString = 'css-loader',
    env            = process.env.NODE_ENV,
    lessQueryString;

  if ('production' === env) {
    plugins.push(new webpack.optimize.UglifyJsPlugin({
      comments: /^\\/
    })); // quick and dirty fix that doesn't match any comments

    cssQueryString += '?minimize&keepSpecialComments=0';
  } else {
    cssQueryString += '?sourceMap';
  }

  lessQueryString = cssQueryString + '!less';

  return {
    cache: true,
    entry: {
      bundle: [
        './src/Frontend/App.js',
        './src/Frontend/styles/custom.less',
        './node_modules/bootstrap/dist/css/bootstrap.min.css'
      ]
    },
    module: {
      loaders: [
        {
          test: /\.js$/,
          loaders: ['babel-loader']
        },
        {
          test: /\.less/,
          loader: ExtractTextPlugin.extract('style-loader', lessQueryString)
        },
        {
          test: /\.css$/,
          loader: ExtractTextPlugin.extract('style-loader', cssQueryString)
        },
        {
          test: /\.woff(\?v=\d+\.\d+\.\d+)?$/,
          loader: 'url?limit=10000&minetype=application/font-woff'
        },
        {
          test: /\.woff2($|\?)/,
          loader: 'url?limit=10000&minetype=application/font-woff'
        },
        {
          test: /\.ttf(\?v=\d+\.\d+\.\d+)?$/,
          loader: 'url?limit=10000&minetype=application/octet-stream'
        },
        {
          test: /\.eot(\?v=\d+\.\d+\.\d+)?$/,
          loader: 'file'
        },
        {
          test: /\.svg(\?v=\d+\.\d+\.\d+)?$/,
          loader: 'url?limit=10000&minetype=image/svg+xml'
        }
      ]
    },
    resolve: {
      modulesDirectories: ['node_modules'],
      extensions: ['.js', '', '.scss']
    },
    output: {
      path: './web/build',
      filename: 'bundle.js'
    },
    externals: {
      'React': 'react'
    },
    plugins: plugins
  };
}

module.exports = createConfig();
