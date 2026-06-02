/* eslint-disable import/no-unresolved */

/// ////////////////////////////////////////////////////////////////////////////
// Gulp initialization.
/// ////////////////////////////////////////////////////////////////////////////

const gulp = require('gulp');
const autoprefixer = require('gulp-autoprefixer');
const mode = require('gulp-mode')({
  modes: ['production', 'development'],
  default: 'development',
  verbose: false,
});
const sass = require('gulp-sass')(require('sass'));
const sassGlob = require('gulp-sass-glob');
const sourcemaps = require('gulp-sourcemaps');

/// ////////////////////////////////////////////////////////////////////////////
// Options and variables.
/// ////////////////////////////////////////////////////////////////////////////

const sassOptions = {
  outputStyle: mode.production() ? 'compressed' : 'expanded',
};

const stylesPaths = {
  src: './assets/scss/**/*.{scss,sass}',
  dest: './assets/css',
};
const componentsStylesPaths = {
  src: './components/**/styles/*.{scss,sass}',
};

/// ////////////////////////////////////////////////////////////////////////////
// Functions.
/// ////////////////////////////////////////////////////////////////////////////

function compileSass(dir) {
  return gulp
    .src(dir.src)
    .pipe(mode.development(sourcemaps.init({})))
    .pipe(sassGlob())
    .pipe(mode.production(sass(sassOptions)))
    .pipe(mode.development(sass(sassOptions).on('error', sass.logError)))
    .pipe(autoprefixer())
    .pipe(mode.development(sourcemaps.write('.')))
    .pipe(gulp.dest(dir.dest ? dir.dest : (file) => file.base));
}

/// ////////////////////////////////////////////////////////////////////////////
// Task definitions.
/// ////////////////////////////////////////////////////////////////////////////

gulp.task('styles', () => compileSass(stylesPaths));
gulp.task('components_styles', () => compileSass(componentsStylesPaths));

gulp.task('watch', (done) => {
  if (mode.production()) {
    return done();
  }

  gulp.watch(stylesPaths.src, gulp.series('styles'));
  gulp.watch(componentsStylesPaths.src, gulp.series('components_styles'));
});

gulp.task('default', gulp.series('styles', 'components_styles', 'watch'));
