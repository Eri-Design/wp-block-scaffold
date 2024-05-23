document.addEventListener( 'DOMContentLoaded', ( event ) => {
    const article = document.getElementById( 'main' );
	const single = document.querySelector( '.single' );
	const singleIssue = document.querySelector( '.single-issues-post' );

	if ( ! article || ! single || singleIssue ) {
		return;
	}

	const { progressBar, progressBarContainer } = createProgressBar();

	window.addEventListener( 'scroll', () => {
		// Calculate the scroll distance
		const scrollDistance = window.scrollY;
		// Get the total height of the article
		const articleHeight = article.clientHeight - window.innerHeight;
		// Calculate the scroll percentage
		const scrollPercentage = ( scrollDistance / articleHeight ) * 100;
		// Update the progress bar width
		progressBar.style.width = scrollPercentage + '%';

		// Hide progress bar when scrolled to the top, otherwise show it
		if ( scrollDistance === 0 ) {
			progressBarContainer.style.visibility = 'hidden';
		} else {
			progressBarContainer.style.visibility = 'visible';
		}
	} );
} );

function createProgressBar() {
	const progressBar = document.createElement( 'div' );
	progressBar.id = 'progressBar';
	progressBar.classList.add( 'progress-bar' );
	
	progressBar.style.height = '6px';
	progressBar.style.width = '0%';

	const progressBarContainer = document.createElement( 'div' );
	progressBarContainer.id = 'progressBarContainer';
	progressBarContainer.classList.add( 'progress-bar-container' );

	progressBarContainer.style.width = '100%';
	progressBarContainer.style.height = '6px';

	document.body.appendChild( progressBar );
	document.body.appendChild( progressBarContainer );

	return { progressBar, progressBarContainer };
}