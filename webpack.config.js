const path = require('path')
const webpackConfig = require('@nextcloud/webpack-vue-config')

webpackConfig.entry = {
	'ereader-main': path.join(__dirname, 'src', 'main.js'),
	fileaction: path.join(__dirname, 'src', 'fileaction.js'),
}

webpackConfig.output = {
	...webpackConfig.output,
	filename: '[name].js',
}

module.exports = webpackConfig
