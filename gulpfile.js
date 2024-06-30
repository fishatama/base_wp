import { src, dest, watch, series, parallel } from "gulp";
import plumber from "gulp-plumber";
import notify from "gulp-notify";
import uglify from "gulp-uglify";
import browserSync from "browser-sync";
import { deleteAsync as del } from "del";
import gulpSass from "gulp-sass";
import * as dartSass from 'sass';

// import imagemin, {mozjpeg, svgo} from 'gulp-imagemin';

import imagemin from 'gulp-imagemin';
import mozjpeg from 'imagemin-mozjpeg';
import changed from 'gulp-changed';
import pngquant from 'imagemin-pngquant';
import imageminGif from 'imagemin-gifsicle';
import svgo from 'imagemin-svgo';
// import imageminWebp  from 'imagemin-webp';

const sass = gulpSass(dartSass);
const reload = browserSync.reload;

const paths = {
  src: {
    html: "src/**/*",
    sass: "src/scss/**/*.scss",
    js: "src/js/**/*.js",
    images: "src/images/**/*"
  },
};
const destPaths = {
  dist: {
    html: "dist",
    css: "dist/css",
    js: "dist/js",
    images: "dist/images"
  },
  demo: {
    html: "demo",
    css: "demo/css",
    js: "mode/js",
    images: "demo/images"
  }
}

// HTMLファイルをdistにコピー
const copyHtml = () => {
  const destinations = Object.values(destPaths).map(destPath => destPath.html);
  let stream = src([paths.src.html, '!src/images/**', '!src/scss/**'])
    .pipe(plumber({ errorHandler: notify.onError("Error: <%= error.message %>") }));
  for (const destination of destinations) {
    stream = stream.pipe(dest(destination, { sourcemaps: "." }));
  }
  return stream;
};

// Sassをコンパイルして圧縮
const compileSass = () => {
  const destinations = Object.values(destPaths).map(destPath => destPath.css);
  let stream = src(paths.src.sass, { sourcemaps: true })
    .pipe(plumber({ errorHandler: notify.onError("Error: <%= error.message %>") }))
    .pipe(sass({ outputStyle: "compressed" }));
  for (const destination of destinations) {
    stream = stream.pipe(dest(destination, { sourcemaps: "." }));
  }
  return stream;
};

// JavaScriptを圧縮
const minifyJs = () => {
  const destinations = Object.values(destPaths).map(destPath => destPath.js);
  let stream = src(paths.src.js, { sourcemaps: true })
    .pipe(uglify());
  for (const destination of destinations) {
    stream = stream.pipe(dest(destination, { sourcemaps: "." }));
  }
  return stream;
};

// 画像をコピー＆圧縮
const copyImages = () => {
  const destinations = Object.values(destPaths).map(destPath => destPath.images);
  let stream = src(paths.src.images,{encoding: false})
    .pipe(changed(paths.src.images))
    .pipe(imagemin([
        mozjpeg({quality: 85, progressive: true}),
        pngquant({quality: [0.8, 0.95], speed: 1}),
        imageminGif({interlaced: false, optimizationLevel: 3, colors: 180}),
        // svgo({ 
        //   plugins: [{
        //       name: 'removeViewBox',
        //       active: true
        //     },{
        //       name: 'cleanupIDs',
        //       active: false
        //     }]
        // }),{
        //   verbose: true
        // }
    ]));
  for (const destination of destinations) {
    stream = stream.pipe(dest(destination));
  }
  return stream;
};

// distディレクトリの削除
const clean = (file) => {
  return del(file + "/*")
};

const destinations = Object.values(destPaths).map(destPath => destPath.html);
for (const destination of destinations) {
  clean(destination);
}

// ファイル監視とブラウザの自動リロード
const watchFiles = () => {
  watch(paths.src.html, copyHtml).on("change", reload);
  watch(paths.src.sass, compileSass).on("change", reload);
  watch(paths.src.js, minifyJs).on("change", reload);
  watch(paths.src.images, copyImages).on("change", reload);

  const destinations = Object.values(destPaths).map(destPath => destPath.html);
  browserSync.init({
    server: {
      baseDir: destinations,
    },
  });
};

export default series(clean, parallel(copyHtml, compileSass, minifyJs, copyImages), watchFiles);
export const build = series(clean, parallel(copyHtml, compileSass, minifyJs, copyImages));
