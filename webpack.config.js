/** @format */
/**
 * External dependencies
 */
const path = require('path');

// Import the original config from the @wordpress/scripts package.
const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const WooCommerceDependencyExtractionWebpackPlugin = require('@woocommerce/dependency-extraction-webpack-plugin');
const NODE_ENV = process.env.NODE_ENV || 'development';

// Export configuration.
const webpackConfig = {
    ...defaultConfig,
    mode: NODE_ENV,
    devtool: NODE_ENV != 'production' ? 'inline-source-map' : false,
    entry: {
		'dkqps-admin': [ './src/js/dkqps-admin.js', './src/css/dkqps-admin.scss' ],
    },
    output: {
		filename: '[name].js',
		path: path.resolve( __dirname, 'assets' ),
	},
    watchOptions: {
      ignored: /node_modules/,
    },
    plugins: [
        ...defaultConfig.plugins, // Preserve any existing plugins from the default config.
        new WooCommerceDependencyExtractionWebpackPlugin(),
        new MiniCssExtractPlugin( {
			filename: `[name].css`,
		} ),
     ],
};

if (webpackConfig.mode !== 'production') {
	webpackConfig.devtool = 'inline-source-map';
}
module.exports = webpackConfig;
