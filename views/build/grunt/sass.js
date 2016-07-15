module.exports = function (grunt) {
    'use strict';

    var sass = grunt.config('sass') || {};
    var watch = grunt.config('watch') || {};
    var notify = grunt.config('notify') || {};
    var root = grunt.option('root') + '/taoClientRestrict/views/';

    sass.taoclientrestrict = {};
    sass.taoclientrestrict.files = {};
    sass.taoclientrestrict.files[root + 'css/web-browsers-form.css'] = root + 'scss/web-browsers-form.scss';

    watch.taoclientrestrictsass = {
        files: [root + 'scss/**/*.scss'],
        tasks: ['sass:taoclientrestrict', 'notify:taoclientrestrictsass'],
        options: {
            debounceDelay: 1000
        }
    };

    notify.taoclientrestrictsass = {
        options: {
            title: 'Grunt SASS',
            message: 'SASS files compiled to CSS'
        }
    };

    grunt.config('sass', sass);
    grunt.config('watch', watch);
    grunt.config('notify', notify);

    //register an alias for main build
    grunt.registerTask('taoclientrestrictsass', ['sass:taoclientrestrict']);
};