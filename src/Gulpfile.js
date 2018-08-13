var gulp = require('gulp');
var browserSync = require('browser-sync').create();
var compass = require('gulp-compass');
var plumber = require('gulp-plumber');
//var inject = require('gulp-inject');
var watch = require('gulp-watch');
var uncss = require('gulp-uncss');
var concat = require('gulp-concat');
var minifyCss = require('gulp-minify-css');
var rev = require('gulp-rev');
var revReplace = require('gulp-rev-replace');
var processhtml = require('gulp-processhtml');
var rimraf = require('rimraf');
var gulpSequence = require('gulp-sequence').use(gulp);
var jsmin = require('gulp-jsmin');
var fs = require('fs');
var makedir = require('makedir');

gulp.task('default', function () {
    console.log(1111);
});

//Public/static
gulp.task('build-static', function() {
    return gulp.src(['./web/Public/src/static/**'])
        .pipe(rev())
        .pipe(gulp.dest('./web/Public/dist/static'))
        .pipe(rev.manifest())
        .pipe(gulp.dest('./dist/rev/static'));

});

//--------------------cf----------------------------
gulp.task('build-cf-font', function() {
    return gulp.src('./web/Public/src/cf/fonts/**')
        .pipe(rev())
        .pipe(gulp.dest('./web/Public/dist/cf/fonts/'))
        .pipe(rev.manifest())
        .pipe(gulp.dest('./dist/rev/cf/fonts'));
});
gulp.task('build-cf-images', function() {
    return gulp.src(['./web/Public/src/cf/images/**'])
        .pipe(rev())
        .pipe(gulp.dest('./web/Public/dist/cf/images'))
        .pipe(rev.manifest())
        .pipe(gulp.dest('./dist/rev/cf/images'));
});
gulp.task('build-cf-css', function() {
    return gulp.src(['./web/Public/src/cf/css/**'])
        .pipe(minifyCss())
        .pipe(rev())
        .pipe(gulp.dest('./web/Public/dist/cf/css/'))
        .pipe(rev.manifest())
        .pipe(gulp.dest('./dist/rev/cf/css'));
});
gulp.task('build-cf-js', function() {
    return gulp.src('./web/Public/src/cf/js/**')
        .pipe(rev())
        .pipe(gulp.dest('./web/Public/dist/cf/js/'))
        .pipe(rev.manifest())
        .pipe(gulp.dest('./dist/rev/cf/js'));
});
gulp.task('build-replace-css-cf', function() {
    var manifest = gulp.src("./dist/rev/cf/**/*.json");
    return gulp.src(["./web/Public/dist/cf/css/*.css"])
        .pipe(revReplace({
            manifest: manifest
        }))
        .pipe(gulp.dest('./web/Public/dist/cf/css'));
});
gulp.task('build-replace-CF-lang', function() {
    var manifest = gulp.src("./dist/rev/cf/images/*.json");
    return gulp.src(["./modules/cf/languages/src/**/*.php"])
        .pipe(revReplace({
            manifest: manifest,
            replaceInExtensions:['.js', '.css', '.html', '.php']
        }))
        .pipe(gulp.dest('./modules/cf/languages'));
});

gulp.task('build-replace-font-cf', function() {
    var manifest = gulp.src("./dist/rev/cf/**/*.json");
    return gulp.src(["./web/Public/dist/cf/fonts/**/*.*"])
        .pipe(revReplace({
            manifest: manifest
        }))
        .pipe(gulp.dest('./web/Public/dist/cf/fonts'));
});
gulp.task('build-replace-views-cf', function() {
    var manifest = gulp.src("./dist/rev/cf/**/*.json");
    return gulp.src(["./modules/cf/views/src/**/*.html"])
        .pipe(revReplace({
            manifest: manifest
        }))
        .pipe(gulp.dest('./modules/cf/views/dist'));
})
gulp.task('build-cf', gulp.series('build-cf-font','build-cf-images','build-cf-css','build-cf-js'));
gulp.task('build-replace-cf', gulp.series('build-replace-font-cf','build-replace-css-cf','build-replace-views-cf','build-replace-CF-lang'));
//---------------------cf-------------------------------------




//--------------------cfhome----------------------------
gulp.task('build-cfhome-font', function() {
    return gulp.src('./web/Public/src/cfhome/fonts/**')
        .pipe(rev())
        .pipe(gulp.dest('./web/Public/dist/cfhome/fonts/'))
        .pipe(rev.manifest())
        .pipe(gulp.dest('./dist/rev/cfhome/fonts'));
});
gulp.task('build-cfhome-images', function() {
    return gulp.src(['./web/Public/src/cfhome/images/**'])
        .pipe(rev())
        .pipe(gulp.dest('./web/Public/dist/cfhome/images'))
        .pipe(rev.manifest())
        .pipe(gulp.dest('./dist/rev/cfhome/images'));
});
gulp.task('build-cfhome-css', function() {
    return gulp.src(['./web/Public/src/cfhome/css/**'])
        .pipe(minifyCss())
        .pipe(rev())
        .pipe(gulp.dest('./web/Public/dist/cfhome/css/'))
        .pipe(rev.manifest())
        .pipe(gulp.dest('./dist/rev/cfhome/css'));
});
gulp.task('build-cfhome-js', function() {
    return gulp.src('./web/Public/src/cfhome/js/**')
        .pipe(rev())
        .pipe(gulp.dest('./web/Public/dist/cfhome/js/'))
        .pipe(rev.manifest())
        .pipe(gulp.dest('./dist/rev/cfhome/js'));
});
gulp.task('build-replace-css-cfhome', function() {
    var manifest = gulp.src("./dist/rev/cfhome/**/*.json");
    return gulp.src(["./web/Public/dist/cfhome/css/**/*.css"])
        .pipe(revReplace({
            manifest: manifest
        }))
        .pipe(gulp.dest('./web/Public/dist/cfhome/css'));
});
gulp.task('build-replace-cfhome-lang', function() {
    var manifest = gulp.src("./dist/rev/cfhome/images/*.json");
    return gulp.src(["./modules/cfhome/languages/src/**/*.php"])
        .pipe(revReplace({
            manifest: manifest,
            replaceInExtensions:['.js', '.css', '.html', '.php']
        }))
        .pipe(gulp.dest('./modules/cfhome/languages'));
});

gulp.task('build-replace-font-cfhome', function() {
    var manifest = gulp.src("./dist/rev/cfhome/**/*.json");
    return gulp.src(["./web/Public/dist/cfhome/fonts/**/*.*"])
        .pipe(revReplace({
            manifest: manifest
        }))
        .pipe(gulp.dest('./web/Public/dist/cfhome/fonts'));
});
gulp.task('build-replace-views-cfhome', function() {
    var manifest = gulp.src("./dist/rev/cfhome/**/*.json");
    return gulp.src(["./modules/cfhome/views/src/**/*.html"])
        .pipe(revReplace({
            manifest: manifest
        }))
        .pipe(gulp.dest('./modules/cfhome/views/dist'));
})
gulp.task('build-cfhome', gulp.series('build-cfhome-font','build-cfhome-images','build-cfhome-css','build-cfhome-js'));
gulp.task('build-replace-cfhome', gulp.series('build-replace-font-cfhome','build-replace-css-cfhome','build-replace-views-cfhome','build-replace-cfhome-lang'));
//---------------------cfhome-------------------------------------


gulp.task('build-clean-dist', function(callback) {
    return rimraf('./dist', callback);
});

gulp.task('build-clean-rev', function(callback) {
    return rimraf('./dist/rev', callback);
});


gulp.task('build-clean-view', function(callback) {
    return rimraf('./modules/**/view/dist', callback);
});

gulp.task('build-clean-lang', function(callback) {
    return rimraf('./modules/**/languages/*.php', callback);
});

// 清空生产环境文件
gulp.task('build-clean', gulp.series('build-clean-rev', 'build-clean-dist','build-clean-view','build-clean-lang'));


gulp.task('build', gulp.series(
    'build-clean',
    //cf
    'build-cf',
    'build-replace-cf',
    //cfhome
    'build-cfhome',
    'build-replace-cfhome'
));
