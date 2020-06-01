'use strict';
var gulp = require('gulp');
var less = require('gulp-less');
var notify = require("gulp-notify");
var LessPluginCleanCSS = require('less-plugin-clean-css');
var uglify = require('gulp-uglify');
var rename = require("gulp-rename");
var jshint = require('gulp-jshint');
var stylish = require('jshint-stylish');
var phpcs = require('gulp-phpcs');
var phpunit = require('gulp-phpunit');
var _ = require('lodash');
var concat = require('gulp-concat');



/**************
 *    PHP     *
 **************/

gulp.task('php_cs', function() {
	return gulp.src(['src/**/*.php'])
		// Validate files using PHP Code Sniffer
		.pipe(phpcs({
			bin: 'C:/xampp/htdocs/_personal/ether/vendor/bin/phpcs',
			standard: 'PSR2',
			warningSeverity: 0
		}))
		// Log all problems that was found
		.pipe(phpcs.reporter('log'));
});

function testNotification(status, pluginName, override) {
    var options = {
        title:   ( status === 'pass' ) ? 'Tests Passed' : 'Tests Failed',
        message: ( status === 'pass' ) ? '\n\nAll tests have passed!\n\n' : '\n\nOne or more tests failed...\n\n',
        icon:    __dirname + '/node_modules/gulp-' + pluginName +'/assets/test-' + status + '.png'
    };
    options = _.merge(options, override);
    return options;
}

gulp.task('php_unit', function() {
    gulp.src('phpunit.xml')
        .pipe(phpunit('', {notify: true}))
        .on('error', notify.onError(testNotification('fail', 'phpunit')))
        .pipe(notify(testNotification('pass', 'php_unit')));
});



/**************
 * Javascript *
 **************/
var jsVendorFiles = [
    'vendor/flesler/jquery.scrollto/jquery.scrollTo.js',
    'vendor/twbs/bootstrap/dist/js/bootstrap.js',
];
var jsSrcFiles = [
    'webroot/js/script.js',
    'webroot/js/comment.js',
    'webroot/js/flash-message.js',
    'webroot/js/messages.js',
    'webroot/js/profile.js',
    'webroot/js/recent.js',
    'webroot/js/registration.js',
    'webroot/js/scroll.js',
    'webroot/js/search.js',
    'webroot/js/suggested.js',
    'webroot/js/thought.js',
    'webroot/js/thoughtword-index.js',
    'webroot/js/user-index.js'
];
var allJsFiles = jsVendorFiles.concat(jsSrcFiles);

gulp.task('js_lint', function () {
	return gulp.src('webroot/js/*.js')
    	.pipe(jshint())
        .pipe(jshint.reporter(stylish))
        .pipe(notify('JS linted'));
});

// Builds all JS files into a single combined, minified file
gulp.task('js_build', function () {
    // Concatenate files
    gulp.src(allJsFiles)
		.pipe(concat('script.concat.js'))
		.pipe(gulp.dest('webroot/js/'))
		.pipe(notify('JS concatenated'));

    // Minify concatenated file
	gulp.src('webroot/js/script.concat.js')
        .pipe(uglify())
        .pipe(rename({extname: '.min.js'}))
        .pipe(gulp.dest('webroot/js'))
        .pipe(notify('JS minified'));
});



/**************
 *    LESS    *
 **************/

gulp.task('less', function () {
	var cleanCSSPlugin = new LessPluginCleanCSS({advanced: true});
	gulp.src('webroot/css/style.less')
		.pipe(less({plugins: [cleanCSSPlugin]}))
        .pipe(gulp.dest('webroot/css'))
        .pipe(notify('LESS compiled'));
});



/**************
 *    SASS    *
 **************/

const sass = require('gulp-sass');
sass.compiler = require('node-sass');

gulp.task('sass', function () {
    return gulp.src('./webroot/css/style.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest('./webroot/css/'));
});

gulp.task('sass:watch', function () {
    gulp.watch('./sass/**/*.scss', ['sass']);
});
