document.addEventListener( 'DOMContentLoaded', function( event ) {
	function setViewportWidth() {
		document.body.style.setProperty('--viewportWidth', `${document.body.clientWidth}px`);
	}

	window.addEventListener('load', setViewportWidth);
	window.addEventListener('resize', setViewportWidth);
});
