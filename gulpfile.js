var gulp = require('gulp');

var less = require('gulp-less');
var path = require('path');
gulp.task('less', function () {
  return gulp.src('./webroot/css/style.less')
    .pipe(less({
      paths: [ path.join(__dirname) ]
    }))
    .pipe(gulp.dest('./webroot/css'));
});

gulp.task('default', function() {

});