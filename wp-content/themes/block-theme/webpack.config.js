const path = require('path');

module.exports = {
	entry: './js/scripts.js',
	output: {
		path: path.resolve(__dirname, 'assets'),
		filename: 'scripts.js',
	},
	mode: "production",
};
