( function() {
	document.addEventListener( 'DOMContentLoaded', function() {
		const socialSharing = document.querySelector( '.mm__social-share' );

		if ( ! socialSharing ) {
			return;
		}

		const top = socialSharing.offsetTop;

		const observer = new window.IntersectionObserver( function( entries ) {
			entries.forEach( function( entry ) {
				// Check if the element is out of view
				if ( ! entry.isIntersecting ) {
					// Element has gone out of view, add the class
					if ( window.scrollY > top ) {
						entry.target.classList.add( 'mm__social-share--fixed' );
					}
				}
			} );
		},
		{
			root: null, // observing the element relative to the viewport
			threshold: 0.1, // Trigger when less than 10% of the element is in view
		} );

		observer.observe( socialSharing );

		window.addEventListener( 'scroll', function() {
			if ( window.scrollY < top ) {
				socialSharing.classList.remove( 'mm__social-share--fixed' );
			}
		} );
	} );
}() );
