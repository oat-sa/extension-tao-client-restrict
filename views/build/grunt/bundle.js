module.exports = function (grunt) {
    'use strict';

    var requirejs = grunt.config('requirejs') || {};
    var clean = grunt.config('clean') || {};
    var copy = grunt.config('copy') || {};
    var uglify = grunt.config('uglify') || {};

    var root = grunt.option('root');
    var libs = grunt.option('mainlibs');
    var ext = require(root + '/tao/views/build/tasks/helpers/extensions')(grunt, root);
    var out = 'output';

    /**
     * Remove bundled and bundling files
     */
    clean.taoclientrestrictbundle = [out];

    /**
     * Compile tao files into a bundle
     */
    requirejs.taoclientrestrictbundle = {
        options: {
            baseUrl: '../js',
            dir: out,
            mainConfigFile: './config/requirejs.build.js',
            paths: {'taoClientRestrict': root + '/taoClientRestrict/views/js'},
            modules: [{
                name: 'taoClientRestrict/controller/routes',
                include: ext.getExtensionsControllers(['taoClientRestrict']),
                exclude: ['mathJax'].concat(libs)
            }]
        }
    };

    /**
     * copy the bundles to the right place
     */
    copy.taoclientrestrictbundle = {
        files: [
            {
                src: [out + '/taoClientRestrict/controller/routes.js'],
                dest: root + '/taoClientRestrict/views/js/controllers.min.js'
            },
            {
                src: [out + '/taoClientRestrict/controller/routes.js.map'],
                dest: root + '/taoClientRestrict/views/js/controllers.min.js.map'
            }
        ]
    };

    grunt.config('clean', clean);
    grunt.config('requirejs', requirejs);
    grunt.config('copy', copy);

    // bundle task
    grunt.registerTask('taoclientrestrictbundle', ['clean:taoclientrestrictbundle', 'requirejs:taoclientrestrictbundle', 'copy:taoclientrestrictbundle']);
};
