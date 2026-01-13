var Encore = require('@symfony/webpack-encore');

Encore.setOutputPath('assets/build')
	.setPublicPath('/')
	.enableSassLoader()
	.addStyleEntry('admin', './assets/scss/admin.scss')
	.enableSourceMaps(!Encore.isProduction())
	.cleanupOutputBeforeBuild()
	.disableSingleRuntimeChunk()
	.enableVersioning(Encore.isProduction());

var config = Encore.getWebpackConfig();

module.exports = config;
