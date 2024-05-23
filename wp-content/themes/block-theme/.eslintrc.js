/**
 * External dependencies
 */

/**
 * Internal dependencies
 */
const { version } = require( './package' );

/**
 * Regular expression string matching a SemVer string with equal major/minor to
 * the current package version. Used in identifying deprecations.
 *
 * @type {string}
 */
const majorMinorRegExp =
  version.replace( /\.\d+$/, '' ).replace( /[\\^$.*+?()[\]{}|]/g, '\\$&' ) +
  '(\\.\\d+)?';

const restrictedImports = [
	{
		name: 'framer-motion',
		message:
      'Please use the Framer Motion API through `@wordpress/components` instead.',
	},
	{
		name: 'lodash',
		message: 'Please use native functionality instead.',
	},
	{
		name: 'reakit',
		message: 'Please use Reakit API through `@wordpress/components` instead.',
	},
	{
		name: 'redux',
		importNames: [ 'combineReducers' ],
		message: 'Please use `combineReducers` from `@wordpress/data` instead.',
	},
	{
		name: 'puppeteer-testing-library',
		message: '`puppeteer-testing-library` is still experimental.',
	},
	{
		name: '@emotion/css',
		message:
      'Please use `@emotion/react` and `@emotion/styled` in order to maintain iframe support. As a replacement for the `cx` function, please use the `useCx` hook defined in `@wordpress/components` instead.',
	},
	{
		name: '@wordpress/edit-post',
		message:
      "edit-post is a WordPress top level package that shouldn't be imported into other packages",
	},
	{
		name: '@wordpress/edit-site',
		message:
      "edit-site is a WordPress top level package that shouldn't be imported into other packages",
	},
	{
		name: '@wordpress/edit-widgets',
		message:
      "edit-widgets is a WordPress top level package that shouldn't be imported into other packages",
	},
];

module.exports = {
	root: true,
	extends: [
		'plugin:@wordpress/eslint-plugin/recommended-with-formatting',
		'plugin:eslint-comments/recommended',
	],
	globals: {
		wp: 'off',
	},
	rules: {
		'jsx-a11y/anchor-is-valid': 'off',
		'react-hooks/exhaustive-deps': 'off',
		'no-console': 'off',
		'no-unused-vars': 'off',
		'jest/expect-expect': 'off',
		'@wordpress/dependency-group': 'error',
		'@wordpress/is-gutenberg-plugin': 'error',
		'@wordpress/react-no-unsafe-timeout': 'error',
		'react-hooks/rules-of-hooks': 'off',
		'@wordpress/i18n-text-domain': [
			'error',
			{
				allowedTextDomain: 'custom-blocks',
			},
		],
		'@wordpress/no-unsafe-wp-apis': 'off',
		'@wordpress/data-no-store-string-literals': 'off',
		'import/default': 'error',
		'import/named': 'error',
		'no-restricted-imports': [
			'error',
			{
				paths: restrictedImports,
			},
		],
		'no-restricted-syntax': [
			'error',
			// NOTE: We can't include the forward slash in our regex or
			// we'll get a `SyntaxError` (Invalid regular expression: \ at end of pattern)
			// here. That's why we use \\u002F in the regexes below.
			{
				selector:
          'ImportDeclaration[source.value=/^@wordpress\\u002F.+\\u002F/]',
				message: 'Path access on WordPress dependencies is not allowed.',
			},
			{
				selector:
          'CallExpression[callee.name="deprecated"] Property[key.name="version"][value.value=/' +
          majorMinorRegExp +
          '/]',
				message:
          'Deprecated functions must be removed before releasing this version.',
			},
			{
				selector:
          'CallExpression[callee.object.name="page"][callee.property.name="waitFor"]',
				message:
          'This method is deprecated. You should use the more explicit API methods available.',
			},
			{
				selector:
          'CallExpression[callee.object.name="page"][callee.property.name="waitForTimeout"]',
				message: 'Prefer page.waitForSelector instead.',
			},
			{
				// Discourage the usage of `Math.random()` as it's a code smell
				// for UUID generation, for which we already have a higher-order
				// component: `withInstanceId`.
				selector:
          'CallExpression[callee.object.name="Math"][callee.property.name="random"]',
				message:
          'Do not use Math.random() to generate unique IDs; use withInstanceId instead. (If you’re not generating unique IDs: ignore this message.)',
			},
			{
				selector:
          'CallExpression[callee.name="withDispatch"] > :function > BlockStatement > :not(VariableDeclaration,ReturnStatement)',
				message:
          'withDispatch must return an object with consistent keys. Avoid performing logic in `mapDispatchToProps`.',
			},
			{
				selector:
          'LogicalExpression[operator="&&"][left.property.name="length"][right.type="JSXElement"]',
				message:
          'Avoid truthy checks on length property rendering, as zero length is rendered verbatim.',
			},
		],
		'no-missing-end-of-source-newline': 0,
	},
};
