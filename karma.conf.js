// Karma configuration
// Generated on Thu Sep 17 2015 19:57:19 GMT+0200 (W. Europe Daylight Time)

module.exports = function(config) {
  config.set({
    basePath: '',
    frameworks: ['jasmine'],
    files: [
      'node_modules/phantomjs-polyfill/bind-polyfill.js',
      'src/Frontend/__tests__/*/*Spec.js'
    ],
    preprocessors: {
      'src/Frontend/__tests__/**/*Spec.js': ['webpack', 'sourcemap']
    },
    reporters: ['dots'],
    port: 9876,
    colors: true,
    logLevel: config.LOG_INFO,
    autoWatch: true,
    browsers: ['PhantomJS'],
    singleRun: false,
    webpackMiddleware: {
      noInfo: true
    },
    webpack: require('./webpack.config'),
    plugins: [
      'karma-webpack',
      'karma-jasmine',
      'karma-sourcemap-loader',
      'karma-phantomjs-launcher'
    ]
  });
};
