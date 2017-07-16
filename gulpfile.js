var gulp     = require('gulp'),
concat       = require('gulp-concat'), // 文件合并
header       = require('gulp-header'), // 添加头注释
fileinclude  = require('gulp-file-include'), // 文件插入
rename       = require('gulp-rename'), // 更名
clean        = require('gulp-clean'), // 清理文件或文件夹
replace      = require('gulp-replace'), // 文本正则替换
bytediff     = require('gulp-bytediff'), // 文件大小对比
plumber      = require('gulp-plumber'), // 防止报错退出
sourcemaps   = require('gulp-sourcemaps'), //map相关
htmlmin      = require('gulp-htmlmin'), //压缩html
csso         = require('gulp-csso'), // 压缩css
sass         = require('gulp-sass'), // 编译sass
autoprefixer = require('gulp-autoprefixer'), // 自动给css添加浏览器前缀
jshint       = require('gulp-jshint'), // js检查
uglify       = require('gulp-uglify'), // js压缩
babel        = require('gulp-babel'); // es2015 js编译

var browserSync = require('browser-sync');

var src = { // 源文件路径
	'css'    : './Public/sass/style.scss',
	'sass'   : ['./Public/sass/**/*.sass','./Public/sass/**/*.scss'],
	'js'     : ['./Public/js/lib/jquery-3.1.1.min.js','./Public/js/lib/jquery.jedate.min.js','./Public/js/base/**/*.js'],
	'libjs'  : './src/js/lib/**/*.js'
};
var dist = { // 这里输出为本地测试页面用
	'css'    : './Public/css',
	'js'     : './Public/js',
	'libjs'  : './dist/js'
};

// 合并，生成，压缩sass文件
var pkg = require('./package.json');
var template = ['/**',
	' Theme Name: <%= pkg.name %>',
	' Author: <%= pkg.author %>',
	' Description: <%= pkg.description %>',
	' */',
	''].join('\n');
function css() {
	return gulp.src(src.css)
	// return sass(src.css, { sourcemap: true, style: 'nested' })
		.pipe(plumber())
		.pipe(sass().on('error', sass.logError))
		// .pipe(sourcemaps.init())
		.pipe(bytediff.start())
		.pipe(csso())
		.pipe(autoprefixer())
		.pipe(header(template, { pkg : pkg } ))
		.pipe(bytediff.stop())
		// .pipe(sourcemaps.write())
		.pipe(gulp.dest(dist.css))
		.pipe(browserSync.reload({stream:true}));
}
css.description = "输出Sass合并编译后的css文件.";
gulp.task(css);

// 合并，压缩js文件
function js() {
	return gulp.src(src.js)
		.pipe(plumber())
		// .pipe(sourcemaps.init())
		.pipe(concat('index.js'))
		.pipe(bytediff.start())
		// .pipe(sourcemaps.write())
		.pipe(bytediff.stop())
		.pipe(gulp.dest(dist.js))
		.pipe( browserSync.stream() );
}
js.description = "输出合并后的index.js文件.";
gulp.task(js);

function lib_js() {
	return gulp.src(src.libjs)
		.pipe(plumber())
		.pipe(bytediff.start())
		.pipe(uglify())
		.pipe(bytediff.stop())
		.pipe(concat('lib.js'))
		.pipe(gulp.dest(dist.libjs))
		.pipe( browserSync.stream() );
}
lib_js.description = "输出合并后的lib.js文件.";
gulp.task(lib_js);



// 监听文件
function watch() {
	gulp.watch(src.sass, gulp.parallel('css'));
	gulp.watch(src.js, gulp.parallel('js'));
	gulp.watch(src.libjs, gulp.parallel('lib_js'));
}

//dep_js,lib_js,
gulp.task('default', gulp.series( gulp.parallel(js,lib_js,css), gulp.parallel(watch) ));
gulp.task('default').description = "一键快速开启开发环境";