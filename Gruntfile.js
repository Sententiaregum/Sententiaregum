module.exports = function (grunt) {
    grunt.initConfig({
        sass: {
            dist: {
                options: {
                    style: 'expanded'
                },
                files: {
                    'src/Frontend/styles/custom.css': 'src/Frontend/styles/custom.scss',
                    'web/build/temp/foundation.css': 'node_modules/zurb-foundation-5/scss/foundation.scss',
                    'web/build/temp/normalize.css': 'node_modules/zurb-foundation-5/scss/normalize.scss'
                }
            }
        },
        concat: {
            options: {
                separator: ';'
            },
            dist: {
                src: [
                    'web/build/temp/foundation.css',
                    'web/build/temp/normalize.css',
                    'src/Frontend/styles/custom.css'
                ],
                dest: 'web/build/bundle.css'
            }
        },
        browserify: {
            client: {
                src: ['src/Frontend/**/*.js'],
                dest: 'web/build/bundle.js',
                options: {
                    require: ['react', 'reflux']
                }
            },
            options: {
                transform: ['babelify', 'reactify']
            }
        },
        uglify: {
            build: {
                src: 'web/build/bundle.js',
                dest: 'web/build/bundle.js'
            }
        },
        cssmin: {
            target: {
                files: {
                    'web/build/bundle.css': ['web/build/bundle.css']
                }
            }
        },
        watch: {
            js: {
                files: ['src/Frontend/**/*.js'],
                tasks: ['build_js']
            },
            css: {
                files: ['src/Frontend/styles/custom.scss'],
                tasks: ['build_css']
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-browserify');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('build', [
        'build_js',
        'build_css'
    ]);
    grunt.registerTask('build_js', [
        'browserify',
        'uglify'
    ]);
    grunt.registerTask('build_css', [
        'sass',
        'concat',
        'cssmin'
    ]);

    grunt.registerTask('default', ['build']);
};
