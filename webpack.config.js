const path = require('path')
const webpackConfig = require('@nextcloud/webpack-vue-config')

webpackConfig.entry = {
	'ereader-main': path.join(__dirname, 'src', 'main.js'),
	fileaction: path.join(__dirname, 'src', 'fileaction.js'),
	'pdf.worker': path.join(__dirname, 'node_modules', 'pdfjs-dist', 'build', 'pdf.worker.mjs'),
}

webpackConfig.output = {
	...webpackConfig.output,
	filename: '[name].js',
}

// epub.js vendor chunk is ~337 KiB; allow it without warning
webpackConfig.performance = {
	...webpackConfig.performance,
	maxAssetSize: 400 * 1024,
}

module.exports = webpackConfig
