// Karma configuration
// Generated on Thu Sep 17 2015 19:57:19 GMT+0200 (W. Europe Daylight Time)

module.exports = function(config) {
  config.set({
    basePath: '',
    frameworks: ['browserify', 'jasmine'],
    files: [
      'node_modules/phantomjs-polyfill/bind-polyfill.js',
      'src/Frontend/__tests__/*/*Spec.js'
    ],
    preprocessors: {
      'src/Frontend/__tests__/**/*Spec.js': ['browserify']
    },
    reporters: ['dots'],
    port: 9876,
    colors: true,
    logLevel: config.LOG_INFO,
    autoWatch: false,
    browsers: ['PhantomJS'],
    singleRun: true,
    browserify: {
      debug: true,
      transform: ['babelify', 'reactify'],
      webstorm: true
    }
  });
};
