/**
 * External dependencies
 */
import EmblaCarousel from 'embla-carousel';

/**
 * Internal dependencies
 */
import createScrollProgressUtils from '../components/EmblaScrollProgressUtils';
import './drag-slider.scss';

document.addEventListener( 'DOMContentLoaded', function() {
	const sliders = document.querySelectorAll( '.drag-slider' );

	const onEmblaScroll = ( emblaApi, eventName ) => {
		let progress = emblaApi.scrollProgress();

		if ( progress < 0 ) {
			progress = 0;
		} else if ( progress > 1 ) {
			progress = 1;
		}

		if ( emblaApi.sliderControlElement && ! emblaApi.sliderControlElement.classList.contains( 'active' ) ) {
			emblaApi.sliderControlElement.value = progress * 100;

			emblaApi.rootNode().parentElement.style.setProperty( '--dragSliderProgress', progress );
		}
	};

	sliders.forEach( ( slider ) => {
		const sliderContainer = slider.querySelector( '.drag-slider__slides-container' );
		const sliderControls = slider.querySelector( '.drag-slider__controls' );

		if ( sliderContainer ) {
			const emblaApi = EmblaCarousel( sliderContainer, { align: 'start', dragFree: true, container: sliderContainer } );

			if ( sliderControls ) {
				const { scrollToProgress } = createScrollProgressUtils( emblaApi );

				emblaApi.sliderControlElement = sliderControls;

				emblaApi.on( 'scroll', onEmblaScroll );

				let timeout;

				emblaApi.sliderControlElement.addEventListener( 'input', function( event ) {
					const progress = event.currentTarget.value / 100;
					scrollToProgress( progress, false );

					emblaApi.rootNode().parentElement.style.setProperty( '--dragSliderProgress', progress );
				} );

				emblaApi.sliderControlElement.addEventListener( 'mousedown', function( event ) {
					event.currentTarget.classList.add( 'active' );
					timeout = false;
				} );

				emblaApi.sliderControlElement.addEventListener( 'mouseup', function( event ) {
					const sliderControlElement = event.currentTarget;

					timeout = setTimeout( () => {
						sliderControlElement.classList.remove( 'active' );
					}, '600' );
				} );

				document.addEventListener( 'reInitDragSlider', function( event ) {
					if ( event.detail === sliderContainer ) {
						emblaApi.scrollTo( 0 );
						emblaApi.rootNode().parentElement.style.setProperty( '--dragSliderProgress', 0 );
						emblaApi.sliderControlElement.value = 0;
					}
				} );
			}
		}
	} );
} );
