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
var webpack = require('webpack');

/**
 * Simple factory which abstracts the configuration of prod and dev environments for webpack.
 *
 * @param {string} environment
 * @returns {Object}
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
function factory(environment) {
  if (-1 === ['dev', 'prod'].indexOf(environment)) {
    throw new Error('Environment must be "dev" or "prod"!');
  }

  var plugins      = [new ExtractTextPlugin('[name].css')],
    cssQueryString = 'css-loader',
    lessQueryString;

  if ('prod' === environment) {
    plugins.push(new webpack.optimize.UglifyJsPlugin({
      comments: /^\\/
    })); // quick and dirty fix that doesn't match any comments

    cssQueryString += '?minimize&keepSpecialComments=0';
  } else {
    cssQueryString += '?sourceMap';
  }

  lessQueryString = cssQueryString + '!less';

  var config = {
    cache: true,
    entry: {
      js: './src/Frontend/App.js',
      bundle: './src/Frontend/styles/custom.less',
      bootstrap: [
        './node_modules/bootstrap/dist/css/bootstrap.min.css',
        './node_modules/bootstrap/dist/css/bootstrap-theme.min.css'
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

  if ('dev' === environment) {
    config.devtool = 'inline-source-map';
  }

  return config;
}

module.exports = factory;
