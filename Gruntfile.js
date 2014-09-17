'use strict';

module.exports = function(grunt) {

	// Load multiple grunt tasks using globbing patterns
	//require('load-grunt-tasks')(grunt);

	// Load NPM tasks to be used here
	grunt.loadNpmTasks( 'load-grunt-tasks' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );
	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-contrib-clean' );
	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-checktextdomain' );

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		// Setting directory of the boilerplate to fetch files
		plugin_dir: { 
			main: 'woocommerce-payment-gateway-boilerplate',
		},

		// Setting the assets folders
		dirs: {
			css: '<%= plugin_dir.main %>/assets/css',
			js: '<%= plugin_dir.main %>/assets/js',
			lang: '<%= plugin_dir.main %>/languages',
		},

		// Minify all .js files.
		uglify: {
			options: {
				banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n',
				preserveComments: 'some'
			},
			admin: {
				files: [{
					expand: true,
					cwd: '<%= dirs.js %>/admin/',
					src: [
						'*.js',
						'!*.min.js',
						'!Gruntfile.js',
					],
					dest: '<%= dirs.js %>/admin/',
					ext: '.min.js'
				}]
			},
			frontend: {
				files: [{
					expand: true,
					cwd: '<%= dirs.js %>/',
					src: [
						'*.js',
						'!*.min.js'
					],
					dest: '<%= dirs.js %>/',
					ext: '.min.js'
				}]
			},
		},

		// Minify all .css files.
		cssmin: {
			minify: {
				expand: true,
				cwd: '<%= dirs.css %>/',
				src: ['*.css', '!*.min.css'],
				dest: '<%= dirs.css %>/',
				ext: '.min.css'
			}
		},

		// Watch changes in the assets
		watch: {
			js: {
				files: [
					'<%= dirs.js %>/admin/*js',
					'<%= dirs.js %>/*js',
					'!<%= dirs.js %>/admin/*.min.js',
					'!<%= dirs.js %>/*.min.js'
				],
				tasks: ['uglify']
			},
			css: {
				files: [
					'<%= dirs.css %>/admin/*css',
					'<%= dirs.css %>/*css',
					'!<%= dirs.css %>/admin/*.min.css',
					'!<%= dirs.css %>/*.min.css'
				],
				tasks: ['cssmin']
			}
		},

		makepot: {
			options: {
				domainPath: '<%= plugin_dir.main %>/languages/',    // Where to save the POT file.
				exclude: ['build/.*'],
				mainFile: '<%= plugin_dir.main %>/woocommerce-payment-gateway-boilerplate.php',    // Main project file.
				potComments: 'WooCommerce Payment Gateway Boilerplate Copyright (c) {{year}}',      // The copyright at the beginning of the POT file.
				potFilename: 'woocommerce-payment-gateway-boilerplate.pot',    // Name of the POT file.
				type: 'wp-plugin',    // Type of project.
				updateTimestamp: true,    // Whether the POT-Creation-Date should be updated without other changes.
				processPot: function( pot, options ) {
					pot.headers['report-msgid-bugs-to'] = 'https://github.com/seb86/WooCommerce-Payment-Gateway-Boilerplate/issues\n';
					pot.headers['plural-forms'] = 'nplurals=2; plural=n != 1;\n';
					pot.headers['last-translator'] = 'WooCommerce Payment Gateway Boilerplate <mailme@sebastiendumont.com>\n';
					pot.headers['language-team'] = 'WP-Translations <wpt@wp-translations.org>\n';
					pot.headers['x-poedit-basepath'] = '..\n';
					pot.headers['x-poedit-language'] = 'English\n';
					pot.headers['x-poedit-country'] = 'UNITED STATES\n';
					pot.headers['x-poedit-sourcecharset'] = 'utf-8\n';
					pot.headers['x-poedit-searchpath-0'] = '.\n';
					pot.headers['x-poedit-keywordslist'] = '__;_e;__ngettext:1,2;_n:1,2;__ngettext_noop:1,2;_n_noop:1,2;_c;_nc:4c,1,2;_x:1,2c;_ex:1,2c;_nx:4c,1,2;_nx_noop:4c,1,2;\n';
					pot.headers['x-textdomain-support'] = 'yes\n';
					return pot;
				}
			}
		},

		checktextdomain: {
			options:{
				text_domain: 'woocommerce-payment-gateway-boilerplate',
				keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
				]
			},
			files: {
				src:  [
					'**/*.php', // Include all files
					'!node_modules/**' // Exclude node_modules/
				],
				expand: true
			}
		},

		exec: {
			npmUpdate: {
				command: 'npm update'
			},
			txpull: { // Pull Transifex translation - grunt exec:txpull
				cmd: 'tx pull -a --minimum-perc=60' // Change the percentage with --minimum-perc=yourvalue
			},
			txpush_s: { // Push pot to Transifex - grunt exec:txpush_s
				cmd: 'tx push -s'
			},
		},

		potomo: {
			dist: {
				options: {
					poDel: false // Set to true if you want to erase the .po
				},
				files: [{
					expand: true,
					cwd: '<%= dirs.lang %>',
					src: ['*.po'],
					dest: '<%= dirs.lang %>',
					ext: '.mo',
					nonull: true
				}]
			}
		},

		// Copy the plugin into the build directory
		copy: {
			main: {
				src:  [
					'**',
					'!.*',
					'!.*/**',
					'!node_modules/**',
					'!build/**',
					'!.git/**',
					'!Gruntfile.js',
					'!package.json',
					'!.gitignore',
					'!.gitmodules',
					'!.gitattributes',
					'!.editorconfig',
					'!.tx/**',
					'!**/Gruntfile.js',
					'!**/package.json',
					'!**/README.md',
					'!**/CHANGELOG.md',
					'!**/CONTRIBUTING.md',
					'!**/composer.json',
					'!**/*~'
				],
				dest: 'build/<%= pkg.name %>/',
				expand: true,
				dot: true
			}
		},

		// Clean up build directory
		clean: {
			main: ['build/<%= pkg.name %>']
		},

		// Compress build directory into <name>.zip and <name>-<version>.zip
		compress: {
			main: {
				options: {
					mode: 'zip',
					archive: './build/<%= pkg.name %>.zip'
				},
				expand: true,
				cwd: 'build/<%= pkg.name %>/',
				src: ['**/*'],
				dest: '<%= pkg.name %>/'
			}
		},

	});

	// Register Tasks
	grunt.registerTask( 'default', ['cssmin', 'uglify']);

	grunt.registerTask( 'dev', ['default', 'makepot']);

	// Makepot and push it on Transifex task(s).
	grunt.registerTask( 'makandpush', [ 'makepot', 'exec:txpush_s' ] );

	// Pull from Transifex and create .mo task(s).
	grunt.registerTask( 'tx', [ 'exec:txpull', 'potomo' ] );

	// Build task(s).
	grunt.registerTask( 'build', [ 'clean', 'copy', 'compress' ] );
};