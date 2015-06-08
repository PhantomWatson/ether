var gulp = require('gulp');
var less = require('gulp-less');
var watchLess = require('gulp-watch-less');
var plumber = require('gulp-plumber');
var notify = require("gulp-notify");

gulp.task('default', ['less']);

gulp.task('less', function () {
	gulp.src('webroot/css/style.less')
		.pipe(less())
        .pipe(gulp.dest('webroot/css'))
        .pipe(notify('LESS compiled'));
	
	watchLess('webroot/css/style.less')
		.pipe(less())
        .pipe(gulp.dest('webroot/css'))
        .pipe(notify('LESS compiled'));
});