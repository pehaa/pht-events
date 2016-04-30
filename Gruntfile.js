module.exports = function(grunt) {
 
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        pluginfiles : [
            '**.php',
            '!secret.php',
            'admin/css/**',
            'admin/js/**',
            'admin/**.php',
            'includes/**',
            'public/**',
            '!public/scss/**',
            'languages/**',
            '**.txt',
            '**.md',
            'plugin-update-checker/**'
        ],
        wp_readme_to_markdown: {
            dist: {
                files: {
                  'readme.md': 'readme.txt'
                },
            },
        },
        makepot: {
            target: {
                options: {
                    include: [],
                    type: 'wp-plugin',
                    potHeaders: { 
                        'report-msgid-bugs-to': 'info@pehaa.com' 
                    }
                }
            }
        },
        sass: {
            dist: {
                options: {
                    style: 'compressed'
                },
                files: {                         // Dictionary of files
                    'public/css/pht-events-public.css':'public/scss/style.scss'
                }
            },
            dev: {
                options: {
                    style: 'expanded'
                },
            files: {                         // Dictionary of files
                    'public/css/pht-events-public.dev.css':'public/scss/style.scss'
                  }
            }
        },
        jshint: {
            files: [
                'admin/js/pht-events-admin.js', 'public/js/pht-events-public.js'
            ],
            options: {
                expr: true,
                globals: {
                    jQuery: true,
                    console: true,
                    module: true,
                    document: true
                }
            }
        },
        uglify: {
            dist: {
                options: {
                    banner: '/*! <%= pkg.name %> <%= pkg.version %> */\n',
                },
                files: {
                    'admin/js/pht-events-admin.min.js' : [
                        'admin/js/datetimepicker.js',
                        'admin/js/pht-events-admin.js'
                    ],
                    'public/js/pht-events-public.min.js' : [
                        'public/js/pht-events-public.js'
                    ]
                }
            },
            publicdist: {
                options: {
                    banner: '/*! <%= pkg.name %> <%= pkg.version %> all.min.js */\n',
                },
                files: {
                }
            },
        },
        compress: {
            main: {
                options: {
                  archive: '<%= pkg.name %>.zip',
                },
                files: [
                  {src: '<%= pluginfiles %>', dest: '' }
                ]
            }
        },
        zip: {
            src: [
                '**.php',
                'admin/css/**',
                'admin/js/**',
                'admin/**.php',
                'includes/**',
                'public/**',
                '!public/scss/**',
                'languages/**',
                '**.txt',
                '**.md',
                'plugin-update-checker/**'
            ],
            dest : 'pht-events.zip',
            compression: 'DEFLATE'
        },
        watch: {
            css: {
                files: [ 'admin/scss/*.scss', 'public/scss/*.scss' ],
                tasks: ['sass:dev', 'sass:dist']
            },
            jsjshint: {
                files: [ 'admin/js/pht-events-admin.js', 'public/js/pht-events-public.js' ],
                tasks: ['jshint', 'jshint']
            },
            js: {
                files: [ 'admin/js/pht-events-admin.js','public/js/pht-events-public.js' ],
                tasks: ['uglify:dist', 'uglify:dist']
            }
        },
        "json-replace": {
            "options": {
                "space" : "\t",
                "replace" : {
                    "name" : '<%= pkg.plugin_name %>',
                    "slug" : '<%= pkg.name %>',
                    "version" : '<%= pkg.version %>',
                    "download_url" : '<%= pkg.download_url %>',
                    "sections" : {
                        "description" : '<%= pkg.description %>',
                        "changelog" : '<%= pkg.changelog %>',
                    },
                    "homepage" : '<%= pkg.repository.url %>',
                    "tested" : '<%= pkg.tested %>',
                    "author" : '<%= pkg.author %>',
                    "author_homepage" : '<%= pkg.author_url %>',
                }
            },
            "metadata": {
                "files" : [{
                    "src" : "metadata.json",
                    "dest" : "metadata.json"
                }]
            },
        },
    });
 

    //grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-curl');
    grunt.loadNpmTasks('grunt-phpdocumentor');
    grunt.loadNpmTasks('grunt-wp-i18n');
    grunt.loadNpmTasks( 'grunt-zip' );
    grunt.loadNpmTasks( 'grunt-contrib-watch' );
    grunt.loadNpmTasks('grunt-wp-readme-to-markdown');
    grunt.loadNpmTasks('grunt-json-replace');
    grunt.loadNpmTasks('grunt-contrib-compress');
 
    grunt.registerTask('default', [
        'makepot',
        'wp_readme_to_markdown',
        'sass:dist',
        'sass:dev',
        'jshint',
        'uglify:dist',
        'uglify:publicdist'
    ]);

    // Serve presentation locally
    grunt.registerTask( 'serve', ['watch'] );
 
};