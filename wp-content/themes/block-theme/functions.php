<?php
if ( ! function_exists( 'eri_scaffold_support' ) ) :
	function eri_scaffold_support() {
		// Make theme available for translation.
		load_theme_textdomain( 'eri-scaffold-block-theme' );
		if ( ! 'eri-scaffold-block-theme' === wp_get_theme()->get( 'TextDomain' ) ) {
			load_theme_textdomain( wp_get_theme()->get( 'TextDomain' ) );
		}

		// Alignwide and alignfull classes in the block editor.
		add_theme_support( 'align-wide' );

		// Add support for link color control.
		add_theme_support( 'link-color' );

		// Add support for responsive embedded content.
		// https://github.com/WordPress/gutenberg/issues/26901
		add_theme_support( 'responsive-embeds' );

		// Add support for editor styles.
		add_theme_support( 'editor-styles' );

		// Add support for post thumbnails.
		add_theme_support( 'post-thumbnails' );

		// Experimental support for adding blocks inside nav menus
		add_theme_support( 'block-nav-menus' );

		// Enqueue editor styles.
		add_editor_style(
			array(
				'/assets/ponyfill.css',
			)
		);

		// Register two nav menus if Gutenberg is activated (otherwise the __experimentalMenuLocation attribute isn't available)
		if ( defined( 'IS_GUTENBERG_PLUGIN' ) ) {
			register_nav_menus(
				array(
					'primary' => __( 'Primary Navigation', 'eri-scaffold-block-theme' ),
					'social'  => __( 'Social Navigation', 'eri-scaffold-block-theme' ),
				)
			);
		}

		add_filter(
			'block_editor_settings_all',
			function( $settings ) {
				$settings['defaultBlockTemplate'] = '<!-- wp:group {"layout":{"inherit":true}} --><div class="wp-block-group"><!-- wp:post-content /--></div><!-- /wp:group -->';
				return $settings;
			}
		);

		// Add support for core custom logo.
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 192,
				'width'       => 192,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);

	}
endif;
add_action( 'after_setup_theme', 'eri_scaffold_support', 9 );

/**
 *
 * Enqueue scripts and styles.
 */
function eri_scaffold_editor_styles() {
	// Add the child theme CSS if it exists.
	if ( file_exists( get_stylesheet_directory() . '/assets/theme.css' ) ) {
		add_editor_style(
			'/assets/theme.css'
		);
	}

	wp_enqueue_style( 'eri-scaffold-admin-styles', get_template_directory_uri() . '/assets/admin.css', array(), wp_get_theme()->get( 'Version' ) );
}
add_action( 'admin_init', 'eri_scaffold_editor_styles' );

/**
 * Enqueue scripts and styles.
 *
 * @return void
 */
function eri_scaffold_scripts() {
	if ( file_exists( get_stylesheet_directory() . '/assets/ponyfill.css' ) ) {
		wp_enqueue_style( 'eri-scaffold-ponyfill', get_template_directory_uri() . '/assets/ponyfill.css', array(), filemtime( get_stylesheet_directory() . '/assets/ponyfill.css' ) );
	}

	// Add the child theme CSS if it exists.
	if ( file_exists( get_stylesheet_directory() . '/assets/theme.css' ) ) {
		wp_enqueue_style( 'eri-scaffold-child-styles', get_stylesheet_directory_uri() . '/assets/theme.css', array( 'eri-scaffold-ponyfill' ), filemtime( get_stylesheet_directory() . '/assets/theme.css' ) );
	}

	if ( file_exists( get_stylesheet_directory() . '/assets/scripts.js' ) ) {
		wp_enqueue_script( 'eri-scaffold-scripts', get_stylesheet_directory_uri() . '/assets/scripts.js', array(), filemtime( get_stylesheet_directory() . '/assets/scripts.js' ) );
	}
/*
	if ( is_search() ) {
		wp_enqueue_script( 'google-cse', 'https://cse.google.com/cse.js?cx=012118865072021868827:jn8a94x0hba', array(), null, true );
	}
*/
}
add_action( 'wp_enqueue_scripts', 'eri_scaffold_scripts' );

/**
 * Enqueue block editor scripts and styles.
 *
 * @return void
 */
function eri_scaffold_editor_scripts() {

    wp_register_script(
        'editor-scripts',
        get_stylesheet_directory_uri() . '/assets/js/editor-scripts.js',
        array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor' ),
        filemtime( trailingslashit( get_template_directory() ) . 'assets/js/editor-scripts.js' ),
        array( 'in_footer' => true )
    );
    wp_enqueue_script('editor-scripts');

}
add_action( 'enqueue_block_editor_assets', 'eri_scaffold_editor_scripts' );

/**
 * Customize Global Styles
 */
if ( class_exists( 'WP_Theme_JSON_Resolver_Gutenberg' ) ) {
	require get_template_directory() . '/inc/customizer/wp-customize-colors.php';
	require get_template_directory() . '/inc/social-navigation.php';
}

require get_template_directory() . '/inc/fonts/custom-fonts.php';
require get_template_directory() . '/inc/rest-api.php';
require get_template_directory() . '/inc/post-types.php';
require get_template_directory() . '/inc/email-functions.php';
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/wp-cli.php';

// Force menus to reload
add_action(
	'customize_controls_enqueue_scripts',
	static function () {
		wp_enqueue_script(
			'wp-customize-nav-menu-refresh',
			get_template_directory_uri() . '/inc/customizer/wp-customize-nav-menu-refresh.js',
			array( 'customize-nav-menus' ),
			wp_get_theme()->get( 'Version' ),
			true
		);
	}
);

/**
 * Block Patterns.
 */
require get_template_directory() . '/inc/block-patterns.php';

// Add the child theme patterns if they exist.
if ( file_exists( get_stylesheet_directory() . '/inc/block-patterns.php' ) ) {
	require_once get_stylesheet_directory() . '/inc/block-patterns.php';
}

/**
 * Add class to body tag if not admin (for use in CSS targeting)
 *
 * @param $classes
 * @return mixed
 */
function eri_scaffold_body_class( $classes ) {

    if ( ! is_admin() )

    $classes[] = 'eri-scaffold-not-admin';
    return $classes;

}
add_filter( 'body_class', 'eri_scaffold_body_class' );

/**
 * Code to be placed after opening body tag
 *
 * @return void
 */
function eri_scaffold_wp_body_open() {
    echo '<a class="skip-to-main" href="#main">Skip to main content</a>';
}
add_action( 'wp_body_open', 'eri_scaffold_wp_body_open' );

/**
 * Register classic menus
 */
register_nav_menus( array( 'quicklinks' => esc_html__( 'QuickLinks', 'eri-scaffold-block-theme' ) ) );
register_nav_menus( array( 'actions' => esc_html__( 'Actions', 'eri-scaffold-block-theme' ) ) );
register_nav_menus( array( 'bottom_bar' => esc_html__( 'Bottom Bar', 'eri-scaffold-block-theme' ) ) );

if( function_exists('acf_add_options_page') ) {

    // Add Site Settings options page
    acf_add_options_page(array(
        'page_title' 	=> 'Site Settings',
        'menu_title'	=> 'Site Settings',
        'menu_slug' 	=> 'eri-scaffold-settings',
        'capability'	=> 'edit_posts',
        'redirect'		=> false
    ));
}

/*
Turning this off in Rankmath SEO because it's throwing a fatal error
See:
https://app.asana.com/0/1205013361806811/1206339105193214/f
*/
add_filter( 'rank_math/opengraph/content_image_cache', function( $ret ) {
    return false;
}, PHP_INT_MAX );

/*
Turns off the Private: prefix for private post titles
*/
function eri_scaffold_private_title_format() {
    return '%s';
}
add_filter('private_title_format','eri_scaffold_private_title_format');
add_filter('protected_title_format', 'eri_scaffold_private_title_format');

/**
 * Filters the image editor output format mapping.
 *
 * @param string[] $output_format {
 *     An array of mime type mappings. Maps a source mime type to a new
 *     destination mime type. Default empty array.
 *
 *     @type string ...$0 The new mime type.
 * }
 * @param string $filename  Path to the image.
 * @param string $mime_type The source image mime type.
 */
function ngi_filter_image_editor_output_format( $output_format, $filename, $mime_type ) {
	$output_format['image/jpeg'] = 'image/webp';
	$output_format['image/png'] = 'image/webp';
	return $output_format;
}
add_filter( 'image_editor_output_format', 'ngi_filter_image_editor_output_format', 10, 3 );

/**
 * Enqueue block editor and front-end styles for the theme.
 *
 * This function enqueues a stylesheet for both the block editor and the front end.
 * The stylesheet, 'style-blocks.css', should be located in the root of your theme directory.
 *
 * If you wish to enqueue styles exclusively for the editor, you can use the is_admin()
 * function to conditionally load assets.
 *
 * @since 1.0.0
 */
function eri_scaffold_enqueue_block_styles() {
    // If you want to add styles exclusively for the block editor, you can use:
    if ( is_admin() ) {
        // Register the editor stylesheet located in the theme's root directory.
        wp_register_style(
            'blocks-admin',
            get_theme_file_uri('assets/admin.css'),
            [],
            '1.0'
        );

        // Enqueue editor styles.
        wp_enqueue_style( 'blocks-admin' );
    }
}
// Hook the function to 'enqueue_block_assets' to load the stylesheet in the block editor and the front end.
add_action( 'enqueue_block_assets', 'eri_scaffold_enqueue_block_styles' );

function add_all_category_slugs_to_post_class( $classes, $class, $post_id ) {
    if ( 'post' === get_post_type() ) {
        $categories = wp_get_post_terms( $post_id, 'category' );

        foreach ( $categories as $category ) {
            $classes[] = 'category-' . $category->slug;
        }
    }

	if ( 'profiles' === get_post_type() ) {
		$post_id = get_the_ID();
		$categories = wp_get_post_terms( $post_id, 'profile-categories' );

		foreach ( $categories as $category ) {
			$classes[] = 'profile-categories-' . $category->slug;
		}
	}

    return $classes;
}
add_filter( 'post_class', 'add_all_category_slugs_to_post_class', 10, 3 );

function add_all_category_slugs_to_body_class( $classes ) {
	if ( is_front_page() ) {
		return $classes;
	}

	if ( 'post' === get_post_type() ) {
		$post_id = get_the_ID();
        $categories = wp_get_post_terms( $post_id, 'category' );

        foreach ( $categories as $category ) {
            $classes[] = 'category-' . $category->slug;
        }
    }

	if ( 'profiles' === get_post_type() ) {
		$post_id = get_the_ID();
		$categories = wp_get_post_terms( $post_id, 'profile-categories' );

		foreach ( $categories as $category ) {
			$classes[] = 'profile-categories-' . $category->slug;
		}
	}

    return $classes;
}
add_filter( 'body_class', 'add_all_category_slugs_to_body_class' );

function add_all_category_slugs_to_admin_body_class( $classes ) {
	if ( 'post' === get_post_type() ) {
		$post_id = get_the_ID();
        $categories = wp_get_post_terms( $post_id, 'category' );

        foreach ( $categories as $category ) {
            $classes .= ' category-' . $category->slug;
        }
    }

	if ( 'profiles' === get_post_type() ) {
		$post_id = get_the_ID();
		$categories = wp_get_post_terms( $post_id, 'profile-categories' );

		foreach ( $categories as $category ) {
			$classes .= ' profile-categories-' . $category->slug;
		}
	}

    return $classes;
}
add_filter( 'admin_body_class', 'add_all_category_slugs_to_admin_body_class' );

function add_category_styles_to_head() {
    // Retrieve all categories
    $categories = get_categories();

    if ( empty( $categories ) ) {
        return;
    }

	$profile_categories = get_terms( array(
		'taxonomy' => 'profile-categories',
		'hide_empty' => true,
	) );

    // Start output buffering
    ob_start();
    echo '<style>';
    foreach ( $categories as $category ) {
		$color = get_field( 'category_color', 'term_' . $category->term_id );
        echo '.category-' . esc_html( $category->slug ) . ', .category-' . esc_html( $category->slug ) . ' .editor-styles-wrapper { --eri-scaffold--color--accent: ' . esc_html( $color ) . '; }';
    }

	foreach ( $profile_categories as $category ) {
		$color = get_field( 'category_color', 'term_' . $category->term_id );
		echo '.profile-categories-' . esc_html( $category->slug ) . ', .profile-categories-' . esc_html( $category->slug ) . ' .editor-styles-wrapper { --eri-scaffold--color--accent: ' . esc_html( $color ) . '; }';
	}
    echo '</style>';
    // Output and clear buffer
    echo ob_get_clean();
}
add_action( 'wp_head', 'add_category_styles_to_head', 100 );
add_action( 'admin_head', 'add_category_styles_to_head', 100 );

function add_category_styles_to_admin_head() {
	$color = null;

	// Retrieve all categories
    if ( 'post' === get_post_type() ) {
		$post_id = get_the_ID();
        $categories = wp_get_post_terms( $post_id, 'category' );
		$category = null;

        foreach ( $categories as $cat ) {
            $category = $cat;
        }

		if ( ! empty( $category ) ) {
			$color = get_field( 'category_color', 'term_' . $category->term_id );
		}
    }

	if ( 'profile' === get_post_type() ) {
		$post_id = get_the_ID();
        $categories = wp_get_post_terms( $post_id, 'profile-categories' );
		$category = null;

        foreach ( $categories as $cat ) {
            $category = $cat;
        }

		if ( ! empty( $category ) ) {
			$color = get_field( 'category_color', 'term_' . $category->term_id );
		}
    }

    ?>
	<style><?php
		if ( ! empty( $color ) ) {
			echo 'body { --eri-scaffold--color--accent: ' . esc_html( $color ) . '; }';
		}
    ?></style>
	<?php
}
add_action( 'admin_head', 'add_category_styles_to_admin_head', 1000000 );

// Disable gravity forms default CSS
add_filter( 'gform_disable_css', '__return_true' );

// Reduce Wordpress default excerpt length
add_filter( 'excerpt_length', function( $length ) {
    return 30;
}, PHP_INT_MAX);
