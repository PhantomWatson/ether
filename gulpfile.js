var gulp = require('gulp');
var less = require('gulp-less');
var watchLess = require('gulp-watch-less');
var plumber = require('gulp-plumber');
var notify = require("gulp-notify");
var LessPluginCleanCSS = require('less-plugin-clean-css');

gulp.task('default', ['less']);

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