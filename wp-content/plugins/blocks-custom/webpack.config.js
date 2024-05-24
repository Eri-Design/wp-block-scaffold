/**
 * External dependencies
 */
require( 'dotenv' ).config();
const glob = require( 'glob' );
const path = require( 'path' );

/**
 * Internal dependencies
 */
const defaultConfig = require( './node_modules/@wordpress/scripts/config/webpack.config' );
const StylelintPlugin = require( 'stylelint-webpack-plugin' );
const ESLintPlugin = require( 'eslint-webpack-plugin' );
const BrowserSyncPlugin = require( 'browser-sync-webpack-plugin' );

const scriptEntryFiles = glob.sync( 'src/**/script.js' );
const editorEntryFiles = glob.sync( 'src/**/editor.js' );
const coreBlockEntryFiles = glob.sync( 'src/core-blocks/**/index.js' );
const coreBlockEditorEntryFiles = glob.sync( 'src/core-blocks/**/editor.js' );
const dependenciesEntryFiles = glob.sync( 'src/dependencies/*.js' );

const entry = {
	...defaultConfig.entry(),
	index: './src/index.js',
	editor: './src/editor.js',
};

scriptEntryFiles.forEach( ( file ) => {
	const entryName = path.dirname( file ).replace( 'src/', '' ) + '/script';
	entry[ entryName ] = path.resolve( __dirname, file );
} );

editorEntryFiles.forEach( ( file ) => {
	const entryName = path.dirname( file ).replace( 'src/', '' ) + '/editor';
	entry[ entryName ] = path.resolve( __dirname, file );
} );

coreBlockEntryFiles.forEach( ( file ) => {
	const entryName =
    'core-blocks/' + path.basename( path.dirname( file ) ) + '/index';
	entry[ entryName ] = path.resolve( __dirname, file );
} );

coreBlockEditorEntryFiles.forEach( ( file ) => {
	const entryName =
    'core-blocks/' + path.basename( path.dirname( file ) ) + '/editor';
	entry[ entryName ] = path.resolve( __dirname, file );
} );

dependenciesEntryFiles.forEach( ( file ) => {
	const entryName = 'dependencies/' + path.parse( file ).name;
	entry[ entryName ] = path.resolve( __dirname, file );
} );

defaultConfig.module.rules.forEach( ( rule, ruleIndex ) => {
	if ( rule.test && String( rule.test ).includes( '(sc|sa)ss' ) ) {
		if ( rule.use ) {
			rule.use.forEach( ( use, useIndex ) => {
				if ( use.loader ) {
					if ( use.loader.includes( 'sass-loader' ) ) {
						defaultConfig.module.rules[ ruleIndex ].use[
							useIndex
						].options.sassOptions = {};

						defaultConfig.module.rules[ ruleIndex ].use[
							useIndex
						].options.sassOptions.includePaths = [
							path.resolve( __dirname, 'src/imports' ),
						];
					}
				}
			} );
		}
	}

	if ( rule.test && String( rule.test ).includes( 'svg' ) ) {
		defaultConfig.module.rules[ ruleIndex ].exclude = [ /[/\\]images[/\\]/ ];
	}
} );

const plugins = [ ...defaultConfig.plugins ];

if ( 'development' === defaultConfig.mode ) {
	plugins.push(
		new StylelintPlugin( {
			files: [ '**/*.css', '**/*.scss' ],
			failOnError: false,
		} )
	);
	plugins.push(
		new ESLintPlugin( {
			extensions: [ 'js' ],
			overrideConfigFile: path.resolve( __dirname, '.eslintrc.js' ),
			failOnError: false,
		} )
	);

	if ( process.env.BROWSERSYNC ) {
		plugins.push(
			new BrowserSyncPlugin( {
				host: 'localhost',
				port: 3000,
				proxy: 'https://eri-scaffold.local/',
			} )
		);
	}
}

module.exports = {
	...defaultConfig,
	entry,
	module: {
		...defaultConfig.module,
		rules: [
			...defaultConfig.module.rules,
			{
				test: /\.svg$/,
				use: [ '@svgr/webpack', 'url-loader' ],
				exclude: [ /[/\\]images[/\\]/ ],
			},
		],
	},
	plugins,
};
