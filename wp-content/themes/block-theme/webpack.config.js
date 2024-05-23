/**
 * External dependencies
 */
const path = require( 'path' );
const ESLintPlugin = require( 'eslint-webpack-plugin' );

module.exports = {
	entry: './js/scripts.js',
	output: {
		path: path.resolve( __dirname, 'assets' ),
		filename: 'scripts.js',
	},
	mode: 'production',
	plugins: [
		new ESLintPlugin( {
			extensions: [ 'js' ],
			overrideConfigFile: path.resolve( __dirname, '.eslintrc.js' ),
			failOnError: false,
		} ),
	],
};
