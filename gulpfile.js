/**
 * gulpfile for docviewer project
 *
 * @author    Matthias Witte <m.witte@techdivision.com>
 * @copyright 2017 TechDivision GmbH <core@techdivision.com>
 */


var gulp        = require('gulp'),
    gutil       = require('gulp-util');
// define dependencies
var build = require("td-neos-build");

if(!process.env.DOCKER_CONTAINER_NAME) {
    gutil.log("You have to provide the env var " + gutil.colors.red("DOCKER_CONTAINER_NAME"));
    gutil.log("Example: " + gutil.colors.green("DOCKER_CONTAINER_NAME=my_container_name gulp deploy:docker"));
    process.exit()
}

gulp.task('release', ['cms:build'], function() {
    return gulp.src('dist/Resources/Public/Styles/*', { dot: true, followSymlinks: false })
        .pipe(gulp.dest('./Resources/Public/Styles/'));
});