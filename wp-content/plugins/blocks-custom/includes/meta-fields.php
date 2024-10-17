<?php
/**
 * Functions and hooks for custom meta fields.
 *
 * @package eri-scaffold-blocks
 */

/**
 * Register custom meta fields.
 */
function eri_scaffold_register_meta_fields() {
	// Example registering of meta field.
	/*
	register_post_meta(
		'post',
		'_external_link',
		array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		)
	);
	*/
}
add_action( 'init', 'eri_scaffold_register_meta_fields' );
