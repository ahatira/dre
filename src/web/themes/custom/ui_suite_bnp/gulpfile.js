/* eslint-disable import/no-unresolved */

const {execFileSync} = require('child_process');
const fs = require('fs');
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

const sassOptions = {
  outputStyle: mode.production() ? 'compressed' : 'expanded',
  silenceDeprecations: ['legacy-js-api'],
};

const paths = {
  work: {
    customFonts: 'work/fonts/custom/**/*.{woff,woff2}',
    js: 'work/scripts/**/*.js',
    scss: ['work/styles/scss/**/*.scss', '!work/styles/scss/legacy/**/*.scss'],
  },
  vendor: {
    bootstrapCss: 'node_modules/bootstrap/dist/css/bootstrap.min.css',
    bootstrapJs: 'node_modules/bootstrap/dist/js/bootstrap.bundle.min.js',
    bootstrapIcons: 'node_modules/bootstrap-icons/icons/**/*.svg',
    openSansFonts: [
      'node_modules/@fontsource/open-sans/files/open-sans-latin-{400,600,700}-normal.woff',
      'node_modules/@fontsource/open-sans/files/open-sans-latin-{400,600,700}-normal.woff2',
      'node_modules/@fontsource/open-sans/files/open-sans-latin-ext-{400,600,700}-normal.woff',
      'node_modules/@fontsource/open-sans/files/open-sans-latin-ext-{400,600,700}-normal.woff2',
    ],
  },
  dest: {
    css: 'assets/css',
    fonts: 'assets/fonts',
    customFonts: 'assets/fonts/custom',
    openSansFonts: 'assets/fonts/open-sans',
    js: 'assets/js',
    frameworkCss: 'assets/css/framework',
    vendorBootstrap: 'assets/vendor/bootstrap',
    vendorBootstrapIcons: 'assets/vendor/bootstrap-icons/icons',
  },
};

function ensureDirs(done) {
  [
    paths.dest.css,
    paths.dest.fonts,
    paths.dest.customFonts,
    paths.dest.openSansFonts,
    paths.dest.js,
    paths.dest.frameworkCss,
    paths.dest.vendorBootstrap,
    paths.dest.vendorBootstrapIcons,
  ].forEach((dirPath) => {
    fs.mkdirSync(dirPath, {recursive: true});
  });
  done();
}

function compileScss() {
  return gulp
    .src(paths.work.scss, {allowEmpty: true})
    .pipe(mode.development(sourcemaps.init()))
    .pipe(sassGlob())
    .pipe(sass(sassOptions).on('error', sass.logError))
    .pipe(autoprefixer())
    .pipe(mode.development(sourcemaps.write('.')))
    .pipe(gulp.dest(paths.dest.css));
}

function copyJs() {
  return gulp
    .src(paths.work.js, {allowEmpty: true})
    .pipe(gulp.dest(paths.dest.js));
}

function copyCustomFonts() {
  return gulp
    .src(paths.work.customFonts, {allowEmpty: true, base: 'work/fonts/custom', encoding: false})
    .pipe(gulp.dest(paths.dest.customFonts));
}

function buildCustomFontScss(done) {
  execFileSync(process.execPath, ['work/tools/build-custom-fonts-scss.mjs'], {stdio: 'inherit'});
  done();
}

function copyVendorBootstrapCss() {
  return gulp
    .src(paths.vendor.bootstrapCss, {allowEmpty: false})
    .pipe(gulp.dest(paths.dest.vendorBootstrap));
}

function copyVendorBootstrapJs() {
  return gulp
    .src(paths.vendor.bootstrapJs, {allowEmpty: false})
    .pipe(gulp.dest(paths.dest.vendorBootstrap));
}

function copyVendorBootstrapIcons() {
  return gulp
    .src(paths.vendor.bootstrapIcons, {allowEmpty: false})
    .pipe(gulp.dest(paths.dest.vendorBootstrapIcons));
}

function copyVendorOpenSansFonts() {
  return gulp
    .src(paths.vendor.openSansFonts, {allowEmpty: false, base: 'node_modules/@fontsource/open-sans/files', encoding: false})
    .pipe(gulp.dest(paths.dest.openSansFonts));
}

const build = gulp.series(
  ensureDirs,
  buildCustomFontScss,
  gulp.parallel(
    compileScss,
    copyJs,
    copyCustomFonts,
    copyVendorBootstrapCss,
    copyVendorBootstrapJs,
    copyVendorBootstrapIcons,
    copyVendorOpenSansFonts,
  ),
);

const fonts = gulp.series(ensureDirs, buildCustomFontScss, gulp.parallel(copyCustomFonts, copyVendorOpenSansFonts));

gulp.task('build', build);
gulp.task('fonts', fonts);

gulp.task('watch', (done) => {
  if (mode.production()) {
    done();
    return;
  }

  gulp.watch(['work/styles/scss/**/*.scss'], compileScss);
  gulp.watch('work/fonts/custom/**/*.{woff,woff2}', gulp.series(buildCustomFontScss, copyCustomFonts, compileScss));
  gulp.watch(paths.work.js, copyJs);
  gulp.watch('node_modules/bootstrap/dist/css/bootstrap.min.css', copyVendorBootstrapCss);
  gulp.watch('node_modules/bootstrap/dist/js/bootstrap.bundle.min.js', copyVendorBootstrapJs);
  gulp.watch('node_modules/bootstrap-icons/icons/**/*.svg', copyVendorBootstrapIcons);
  gulp.watch(paths.vendor.openSansFonts, copyVendorOpenSansFonts);

  done();
});

gulp.task('default', gulp.series(build, 'watch'));
