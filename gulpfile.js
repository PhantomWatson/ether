var gulp = require('gulp');
var less = require('gulp-less');
var watchLess = require('gulp-watch-less');
var plumber = require('gulp-plumber');
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
var runSequence = require('run-sequence');

gulp.task('default', ['less', 'js', 'phpcs', 'phpunit', 'watch']);

gulp.task('less', function () {
	var cleanCSSPlugin = new LessPluginCleanCSS({advanced: true});
	gulp.src('webroot/css/style.less')
		.pipe(less({plugins: [cleanCSSPlugin]}))
        .pipe(gulp.dest('webroot/css'))
        .pipe(notify('LESS compiled'));
});

gulp.task('phpcs', function() {
	return gulp.src(['src/**/*.php'])
		// Validate files using PHP Code Sniffer
		.pipe(phpcs({
			bin: 'C:/xampp/htdocs/ether3/vendor/bin/phpcs',
			standard: 'PSR2',
			warningSeverity: 0
		}))
		// Log all problems that was found
		.pipe(phpcs.reporter('log'));
});

gulp.task('phpunit', function() {
    gulp.src('phpunit.xml')
        .pipe(phpunit('', {notify: true}))
        .on('error', notify.onError(testNotification('fail', 'phpunit')))
        .pipe(notify(testNotification('pass', 'phpunit')));
});

function testNotification(status, pluginName, override) {
    var options = {
        title:   ( status == 'pass' ) ? 'Tests Passed' : 'Tests Failed',
        message: ( status == 'pass' ) ? '\n\nAll tests have passed!\n\n' : '\n\nOne or more tests failed...\n\n',
        icon:    __dirname + '/node_modules/gulp-' + pluginName +'/assets/test-' + status + '.png'
    };
    options = _.merge(options, override);
    return options;
}

gulp.task('watch', function() {
	var cleanCSSPlugin = new LessPluginCleanCSS({advanced: true});
	watchLess('webroot/css/style.less', ['less'])
    	.pipe(less({plugins: [cleanCSSPlugin]}))
        .pipe(gulp.dest('webroot/css'))
        .pipe(notify('LESS compiled'));
	gulp.watch(jsFiles, ['js']);
	gulp.watch('src/**/*.php', ['phpcs', 'phpunit']);
});

var jsFiles = [
    'vendor/flesler/jquery.scrollto/jquery.scrollTo.js', 
    'webroot/bootstrap/dist/js/bootstrap.js', 
    'webroot/script.js'
];
var jsMinFiles = [
    'vendor/flesler/jquery.scrollto/jquery.scrollTo.min.js', 
    'webroot/bootstrap/dist/js/bootstrap.min.js', 
    'webroot/script.min.js'
];

gulp.task('js', function(callback) {
	return runSequence('js_lint', 'js_minify', 'js_concat', callback);
});

gulp.task('js_lint', function () {
	return gulp.src('webroot/js/script.js')
    	.pipe(jshint())
        .pipe(jshint.reporter(stylish))
        .pipe(notify('JS linted'));
});

gulp.task('js_minify', function () {
	return gulp.src('webroot/js/script.js')
    	.pipe(uglify())
    	.pipe(rename({
    		extname: '.min.js'
    	}))
    	.pipe(gulp.dest('webroot/js'))
    	.pipe(notify('JS minified'));
});

gulp.task('js_concat', function () {
	gulp.src(jsFiles)
		.pipe(concat('script.concat.js'))
		.pipe(gulp.dest('webroot/js/'));
	gulp.src(jsMinFiles)
		.pipe(concat('script.concat.min.js'))
		.pipe(gulp.dest('webroot/js/'));
});