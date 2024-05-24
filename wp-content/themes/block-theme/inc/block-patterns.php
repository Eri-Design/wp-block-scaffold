<?php
/**
 * EriScaffold Theme: Block Patterns
 *
 * @package EriScaffoldTheme
 */

if ( ! function_exists( 'eri_scaffold_register_block_patterns' ) ) :
	/**
	 * Register block patterns.
	 */
	function eri_scaffold_register_block_patterns() {

		if ( function_exists( 'register_block_pattern_category' ) ) {
			register_block_pattern_category(
				'eri-scaffold',
				array( 'label' => __( 'EriScaffold', 'eri-scaffold-block-theme' ) )
			);
		}

		if ( function_exists( 'register_block_pattern' ) ) {
			$block_patterns = array(
				'social-sharing',
			);

			foreach ( $block_patterns as $block_pattern ) {
				register_block_pattern(
					'eri-scaffold/' . $block_pattern,
					require __DIR__ . '/patterns/' . $block_pattern . '.php'
				);
			}
		}
	}
	add_action( 'init', 'eri_scaffold_register_block_patterns', 9 );
endif;

/**
 * Register block styles.
 */
function eri_scaffold_register_block_styles() {
	// Check if function exists to prevent errors in older versions of WordPress.
	if ( function_exists( 'register_block_style' ) ) {
		register_block_style(
			'core/button', // Block type to target, in this case, the button block.
			array(
				'name'  => 'black', // A unique name that will be used as a class name.
				'label' => __( 'Black', 'eri-scaffold-block-theme' ), // The label shown in the editor.
			)
		);

		register_block_style(
			'core/button', // Block type to target, in this case, the button block.
			array(
				'name'  => 'white', // A unique name that will be used as a class name.
				'label' => __( 'White', 'eri-scaffold-block-theme' ), // The label shown in the editor.
			)
		);
	}
}
add_action( 'init', 'eri_scaffold_register_block_styles' );
