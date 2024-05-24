<?php
/**
 * Custom fonts functionality.
 *
 * @package EriScaffoldTheme
 */

// Font Migration.
// require get_template_directory() . '/inc/fonts/custom-font-migration.php'; // phpcs:ignore.
add_action( 'init', 'enqueue_global_styles_fonts', 100 );
add_action( 'admin_init', 'enqueue_fse_font_styles' );
add_filter( 'pre_render_block', 'enqueue_block_fonts', 10, 2 );
add_filter( 'jetpack_google_fonts_list', 'eri_scaffold_filter_jetpack_google_fonts_list' );

$eri_scaffold_enqueued_font_slugs = array();

/**
 * Get the CSS containing font_face values for a given slug.
 *
 * @param string $slug The font slug.
 *
 * @return string String of CSS.
 */
function get_style_css( $slug ) {
	$font_face_file = get_template_directory() . '/assets/fonts/' . $slug . '/font-face.css';
	if ( ! file_exists( $font_face_file ) ) {
		return '';
	}
	$contents = file_get_contents( $font_face_file ); // phpcs:ignore
	return str_replace( 'src: url(./', 'src: url(' . get_template_directory_uri() . '/assets/fonts/' . $slug . '/', $contents );
}

/**
 * Collect fonts set in Global Styles settings.
 *
 * @return array Font faces from Global Styles settings.
 */
function collect_fonts_from_global_styles() {
	// NOTE: We have to use gutenberg_get_global_styles() here due to the potential changes to Global Styles on page load happening in font migration.
	// Since core users don't have anything to migrate we can use core get_styles.
	if ( function_exists( 'gutenberg_get_global_styles' ) ) {
		$global_styles = gutenberg_get_global_styles();
	} else {
		$global_styles = wp_get_global_styles();
	}

	$found_webfonts = array();

	// Look for fonts in block presets...
	if ( isset( $global_styles['blocks'] ) ) {
		foreach ( $global_styles['blocks'] as $setting ) {
			$font_slug = extract_font_slug_from_setting( $setting );

			if ( $font_slug ) {
				$found_webfonts[] = $font_slug;
			}
		}
	}

	// Look for fonts in HTML element presets...
	if ( isset( $global_styles['elements'] ) ) {
		foreach ( $global_styles['elements'] as $setting ) {

			// die(json_encode($global_styles));
			// NOTE: It seems that the Global Styles assignment of fonts writes the font family string rather than the
			// global styles shorthand that is assigned to the body.  This should be confirmed and reported if it isn't already.
			$font_slug = extract_font_slug_from_setting( $setting );

			if ( $font_slug ) {
				$found_webfonts[] = $font_slug;
			}
		}
	}

	// Check if a global typography setting was defined.
	$font_slug = extract_font_slug_from_setting( $global_styles );

	if ( $font_slug ) {
		$found_webfonts[] = $font_slug;
	}

	return array_unique( $found_webfonts );
}

/**
 * Extract the font family slug from a settings array.
 *
 * @param array $setting The settings object.
 *
 * @return string|null
 */
function extract_font_slug_from_setting( $setting ) {
	if ( ! isset( $setting['typography']['fontFamily'] ) ) {
		return null;
	}

	$font_family = $setting['typography']['fontFamily'];

	// Full string: var(--wp--preset--font-family--slug).
	// We do not care about the origin of the font, only its slug.
	preg_match( '/font-family--(?P<slug>.+)\)$/', $font_family, $matches );

	if ( isset( $matches['slug'] ) ) {
		return $matches['slug'];
	}

	// Full string: var:preset|font-family|slug
	// We do not care about the origin of the font, only its slug.
	preg_match( '/font-family\|(?P<slug>.+)$/', $font_family, $matches );

	if ( isset( $matches['slug'] ) ) {
		return $matches['slug'];
	}

	return $font_family;
}

/**
 * Build a list of all font slugs provided from theme.json
 *
 * @return array Collection of all font slugs defined in the theme.json file
 */
function collect_fonts_from_eri_scaffold() {
	$fonts                  = array();
	$parent_theme_json_data = json_decode( file_get_contents( get_template_directory() . '/theme.json' ), true );
	$font_families          = $parent_theme_json_data['settings']['typography']['fontFamilies'];

	foreach ( $font_families as $font ) {
		$fonts[] = $font;
	}

	return $fonts;
}

/**
 * Enqeue all of the fonts used in global styles settings.
 *
 * @return void
 */
function enqueue_global_styles_fonts() {

	global $eri_scaffold_enqueued_font_slugs;

	$font_slugs = array();
	$font_css   = '';

	$font_families = collect_fonts_from_eri_scaffold();
	foreach ( $font_families as $font_family ) {
		$font_slugs[] = $font_family['slug'];
	}

	// NOTE: We have to use gutenberg_get_global_styles() here due to the potential changes to Global Styles on page load happening in font migration.
	// Since core users don't have anything to migrate we can use core get_styles.
	if ( function_exists( 'gutenberg_get_global_styles' ) ) {
		$global_styles = gutenberg_get_global_styles();
	} else {
		$global_styles = wp_get_global_styles();
	}

	// Check if a global typography setting was defined.
	$font_slug = extract_font_slug_from_setting( $global_styles );
	if ( $font_slug ) {
		$font_slugs[] = $font_slug;
	}
	$font_slugs = array_unique( $font_slugs );

	$eri_scaffold_enqueued_font_slugs = $font_slugs;

	foreach ( $font_slugs as $font_slug ) {
		$font_css .= get_style_css( $font_slug );
	}

	// Bail out if there are no styles to enqueue.
	if ( '' === $font_css ) {
		return;
	}

	// Enqueue the stylesheet.
	wp_enqueue_style( 'eri_scaffold_font_faces' );

	// Add the styles to the stylesheet.
	wp_add_inline_style( 'eri_scaffold_font_faces', $font_css );
}

/**
 * Enqueue all of the fonts provided for FSE use.
 *
 * @param array $fonts The fonts to enqueue.
 * @return void
 */
function enqueue_fse_font_styles( $fonts ) {
	$fonts    = collect_fonts_from_eri_scaffold();
	$font_css = '';

	foreach ( $fonts as $font ) {
		$font_css .= get_style_css( $font['slug'] );
	}

	wp_enqueue_style( 'wp-block-library' );
	wp_add_inline_style( 'wp-block-library', $font_css );
}

/**
 * Add fonts that have been assigned via CSS.
 *
 * @param string $content The content.
 * @param array  $parsed_block The parsed block.
 * @return string The content.
 */
function enqueue_block_fonts( $content, $parsed_block ) {
	global $eri_scaffold_enqueued_font_slugs;
	if ( ! is_admin() && isset( $parsed_block['attrs']['fontFamily'] ) ) {
		$font_slug = $parsed_block['attrs']['fontFamily'];
		if ( ! in_array( $font_slug, $eri_scaffold_enqueued_font_slugs, true ) ) {
			$font_css = get_style_css( $font_slug );
			if ( $font_css ) {
				$eri_scaffold_enqueued_font_slugs[] = $font_slug;
				wp_add_inline_style( 'eri_scaffold_font_faces', $font_css );
			}
		}
	}
	return $content;
}

/**
 * Jetpack may attempt to register fonts for the Google Font Provider.
 * This filters out all of the fonts that are already registered.
 *
 * @param array $jetpack_fonts The fonts Jetpack is attempting to register.
 *
 * @return array The fonts that Jetpack should register.
 */
function eri_scaffold_filter_jetpack_google_fonts_list( $jetpack_fonts ) {
	$theme_fonts         = collect_fonts_from_eri_scaffold();
	$theme_font_families = array_column( $theme_fonts, 'name' );
	$filtered_list       = array();

	// If the Jetpack font isn't in theme already, let Jetpack register it.
	foreach ( $jetpack_fonts as $jetpack_font_family ) {
		if ( ! in_array( $jetpack_font_family, $theme_font_families, true ) ) {
			$filtered_list[] = $jetpack_font_family;
		}
	}
	return $filtered_list;
}
