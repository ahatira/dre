/* eslint-disable import/no-unresolved */

/// /////////////////////////////////////////////////////////////////////////////
// Gulp initialization.
/// /////////////////////////////////////////////////////////////////////////////

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

/// /////////////////////////////////////////////////////////////////////////////
// Options and variables.
/// /////////////////////////////////////////////////////////////////////////////

const sassOptions = {
  outputStyle: mode.production() ? 'compressed' : 'expanded',
  // Bootstrap 5.3 still uses @import and global Sass builtins.
  // Silence until upstream migrates to @use (Dart Sass 3.0 timeline).
  silenceDeprecations: [
    'legacy-js-api',
    'import',
    'global-builtin',
    'color-functions',
    'if-function',
  ],
};

const stylesPaths = {
  src: [
    './assets/scss/**/*.{scss,sass}',
    '!./assets/scss/bootstrap-sdc-bundle.scss',
  ],
  dest: './assets/css',
};

// Orphan starterkit Bootstrap SDC wrappers — excluded until removed from components/.
const legacyBootstrapSdcDirs = [
  'accordion',
  'alert',
  'badge',
  'breadcrumb',
  'button_group',
  'card',
  'carousel',
  'close_button',
  'dropdown',
  'list_group',
  'modal',
  'nav',
  'navbar',
  'offcanvas',
  'pagination',
  'progress',
  'spinner',
  'table',
  'toast',
];

const componentsStylesPaths = {
  src: [
    './components/**/styles/*.{scss,sass}',
    ...legacyBootstrapSdcDirs.map((dir) => `!./components/${dir}/styles/*.{scss,sass}`),
  ],
};

/// /////////////////////////////////////////////////////////////////////////////
// Functions.
/// /////////////////////////////////////////////////////////////////////////////

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

/// /////////////////////////////////////////////////////////////////////////////
// Task definitions.
/// /////////////////////////////////////////////////////////////////////////////

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
