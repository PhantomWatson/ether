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

gulp.task('default', ['less', 'js', 'jswatch']);

gulp.task('less', function () {
	var cleanCSSPlugin = new LessPluginCleanCSS({advanced: true});
	var lessConfig = {
		plugins: [cleanCSSPlugin]
	};
	gulp.src('webroot/css/style.less')
		.pipe(less(lessConfig))
        .pipe(gulp.dest('webroot/css'))
        .pipe(notify('LESS compiled'));
	
	watchLess('webroot/css/style.less')
		.pipe(less(lessConfig))
        .pipe(gulp.dest('webroot/css'))
        .pipe(notify('LESS compiled'));
});

gulp.task('js', function() {
	gulp.src('webroot/js/script.js')
		.pipe(jshint())
	    .pipe(jshint.reporter(stylish))
		.pipe(uglify())
		.pipe(rename({
			extname: '.min.js'
		}))
		.pipe(gulp.dest('webroot/js'))
		.pipe(notify('JS linted and minified'));
});

gulp.task('jswatch', function() {
	return gulp.watch('webroot/js/script.js', ['js']);
});