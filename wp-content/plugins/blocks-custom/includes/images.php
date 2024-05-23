<?php
/**
 * Functions and hooks for images used in the plugin.
 *
 * @package eri-scaffold-blocks
 */

/**
 * Add new custom image sizes.
 */
function eri_scaffold_blocks_add_image_sizes() {
	add_image_size( 'hero-slider-image', 1680, 9999, false );
	add_image_size( 'faculty-hero', 300, 420, true );
	add_image_size( 'grid-block', 600, 9999, false );
	add_image_size( 'carousel-card', 420, 270, true );
	add_image_size( 'carousel-card-contain', 400, 9999, true );
	add_image_size( 'faculty-card', 420, 420, true );
	add_image_size( 'program-card', 420, 420, true );
	add_image_size( 'audience-based-carousel-card', 360, 240, true );
	add_image_size( 'testimonial-carousel-card', 190, 190, true );
	add_image_size( 'video-carousel-card', 450, 450, true );
	add_image_size( 'tabbed-slider-image', 1680, 9999, false );
	add_image_size( 'news-hero', 9999, 485, false );
	add_image_size( 'news-card', 420, 270, true );
	add_image_size( 'news-card-small', 150, 96, true );
	add_image_size( 'news-card-medium', 434, 279, true );
	add_image_size( 'news-card-large', 790, 506, true );
}
add_action( 'plugins_loaded', 'eri_scaffold_blocks_add_image_sizes' );

/**
 * Add custom image sizes to the Gutenberg image block size selector.
 *
 * @param array $sizes An array of image sizes and their labels.
 * @return array An updated array of image sizes.
 */
function eri_scaffold_blocks_add_image_sizes_to_editor( $sizes ) {
	$custom_sizes = array(
		'hero-slider-image'         => __( 'Hero Slider Image (1680px wide)', 'eri-scaffold-blocks' ),
		'grid-block'                => __( 'Grid Block (600px wide)', 'eri-scaffold-blocks' ),
		'carousel-card'             => __( 'Carousel Card (420x270px)', 'eri-scaffold-blocks' ),
		'testimonial-carousel-card' => __( 'Testimonial Carousel Card (190x190px)', 'eri-scaffold-blocks' ),
		'video-carousel-card'       => __( 'Video Carousel Card (450x450px)', 'eri-scaffold-blocks' ),
		'tabbed-slider-image'       => __( 'Tabbed Slider Image', 'eri-scaffold-blocks' ),
	);

	$sizes = array_merge( $sizes, $custom_sizes );

	return $sizes;
}
add_filter( 'image_size_names_choose', 'eri_scaffold_blocks_add_image_sizes_to_editor' );

/**
 * Filter to allow SVGs in content filtered by wp_kses.
 *
 * @param array $tags   An array of tags and their allowed html attributes.
 */
function eri_scaffold_blocks_allow_svg_html( $tags ) {
	$tags['svg'] = array(
		'xmlns'       => array(),
		'fill'        => array(),
		'viewbox'     => array(),
		'role'        => array(),
		'aria-hidden' => array(),
		'focusable'   => array(),
	);

	$tags['path'] = array(
		'd'    => array(),
		'fill' => array(),
	);

	return $tags;
}
add_filter( 'wp_kses_allowed_html', 'eri_scaffold_blocks_allow_svg_html' );
