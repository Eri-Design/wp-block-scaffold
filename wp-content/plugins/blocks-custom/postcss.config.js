/**
 * WordPress dependencies
 */
const { hasCssnanoConfig } = require( '@wordpress/scripts/utils' );
const config = {
	plugins: [
		require( 'autoprefixer' ),
		require( 'postcss-custom-media' ),
		/*
		require( 'postcss-pxtorem' )( {
			rootValue: 16,
			unitPrecision: 5,
			propList: [ '*', '!border*' ],
			selectorBlackList: [],
			replace: true,
			mediaQuery: true,
			minPixelValue: 0,
			exclude: /node_modules/i,
		} ),
		*/
		require( 'cssnano' )( {
			// Provide a fallback configuration if there's not
			// one explicitly available in the project.
			...( ! hasCssnanoConfig() && {
				preset: [
					'default',
					{
						discardComments: {
							removeAll: true,
						},
					},
				],
			} ),
		} ),
	],
};

module.exports = config;
