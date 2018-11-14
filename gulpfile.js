const path   = require('path');
const gulp   = require('gulp');
const concat = require('gulp-concat');

const appPublicDir   = path.join(__dirname, 'assets');
const nodeModulesDir = path.join(__dirname,'node_modules');

const appTask = function() {

    // Control the order
    gulp.src([
            //appPublicDir + '/css/reset.css',
            appPublicDir + '/css/common.css',
            //appPublicDir + '/css/fieldset.css',
            appPublicDir + '/css/schedule.css',
            appPublicDir + '/css/ng.css',
            appPublicDir + '/css/app.css',
            appPublicDir + '/css/bs_custom.css'
        ])
        .pipe(concat("zayso.css"))
        .pipe(gulp.dest('public/css'));

    // Javascripts
    gulp.src([
            appPublicDir + '/js/cerad-checkbox-all.js',
            appPublicDir + '/js/ie10-viewport-bug-workaround.js',
            appPublicDir + '/js/cerad-select-bgcolor.js',
            appPublicDir + '/js/cerad-file-input.js'
        ])
        .pipe(concat("zayso.js"))
        .pipe(gulp.dest('public/js'));
        
    // images
    gulp.src([
            appPublicDir + '/images/*.png',
            appPublicDir + '/images/*.ico'
            
        ])
        .pipe(gulp.dest('public/images'));

    // PDFs
    gulp.src([
        appPublicDir + '/docs/*.pdf'

    ])
        .pipe(gulp.dest('public/pdf'));
};
gulp.task('app',appTask);

const nodeModulesTask = function() {

    gulp.src([
    //        path.join(nodeModulesDir,'normalize.css/normalize.css'),
    //        path.join(nodeModulesDir,'bootstrap/dist/css/bootstrap.min.css'),
    //        path.join(nodeModulesDir,'bootstrap-fileinput/css/fileinput.min.css')            
            path.join(nodeModulesDir,'bootstrap-vertical-tabs/bootstrap.vertical-tabs.min.css')            
        ])
        .pipe(gulp.dest('public/css'));
    //
    //gulp.src([
    //        path.join(nodeModulesDir,'jquery/dist/jquery.min.js'),
    //        path.join(nodeModulesDir,'bootstrap/dist/js/bootstrap.min.js'),
    //        path.join(nodeModulesDir,'bootstrap-fileinput/js/fileinput.min.js')
    //    ])
    //    .pipe(gulp.dest('public/js'));
};
gulp.task('node_modules',nodeModulesTask);

const buildTask = function(done)
{
    appTask();
    nodeModulesTask();

    done();
};
gulp.task('build',buildTask);

const watchTask = function(done)
{
    buildTask();

    // Why the warnings, seems to work fine
    gulp.watch([
        appPublicDir + '/css/*.css',
        appPublicDir + '/js/*.js',
        appPublicDir + '/images/*.png',
        appPublicDir + '/images/*.ico'
    ],  ['app']);

    done();
};
gulp.task('watch',watchTask);
