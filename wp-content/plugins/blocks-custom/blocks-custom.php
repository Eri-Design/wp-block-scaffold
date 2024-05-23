<?php
/**
 * Plugin Name: ERI Scaffold Blocks
 * Plugin URI: https://eridesign.com
 * Description: Adds custom blocks and post types to site.
 * Version: 1.0.0
 * Author: ERI
 * Author URI: https://eridesign.com
 * License: GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: eri-scaffold-blocks
 *
 * @package eri-scaffold-blocks
 */

defined( 'ABSPATH' ) || exit;

define( 'ERI_SCAFFOLD_BLOCKS_VERSION', '1.0.0' );
define( 'ERI_SCAFFOLD_BLOCKS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ERI_SCAFFOLD_BLOCKS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once "includes/frontend/icons.php";
require_once "includes/images.php";
require_once "includes/render-block.php";
require_once "includes/archive.php";
require_once "includes/post-types.php";
require_once "includes/meta-fields.php";

/**
 * Load translations (if any) for the plugin from the /languages/ folder.
 *
 * @link https://developer.wordpress.org/reference/functions/load_plugin_textdomain/
 */
add_action( 'init', 'eri_scaffold_blocks_load_textdomain' );

/**
 * @return void
 */
function eri_scaffold_blocks_load_textdomain() {
	load_plugin_textdomain( 'eri-scaffold-blocks', false, basename( __DIR__ ) . '/languages' );
}

/**
 * Add custom "Muhlenberg" block category
 *
 * @link https://wordpress.org/gutenberg/handbook/designers-developers/developers/filters/block-filters/#managing-block-categories
 */
add_filter( 'block_categories_all', 'eri_scaffold_blocks_block_categories_all', 10, 2);
function eri_scaffold_blocks_block_categories_all( $categories, $post ) {

    array_unshift( $categories, array(
        'slug'	=> 'eri-scaffold',
        'title' => __('ERI Scaffold', 'eri-scaffold-blocks'),
    ) );

    return $categories;
}

/**
 * Registers all block assets so that they can be enqueued through the Block Editor in
 * the corresponding context.
 *
 * @link https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/block-registration/
 */
add_action( 'init', 'eri_scaffold_blocks_register_blocks' );

function eri_scaffold_blocks_register_blocks() {

	// If Block Editor is not active, bail.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

    // Register plugin main script
	if ( file_exists( plugin_dir_path(__FILE__) . 'build/index.js' ) ) {
        wp_enqueue_script('eri-scaffold-blocks-script',
            plugins_url( 'build/index.js', __FILE__ ),
            array(),
            filemtime(plugin_dir_path(__FILE__) . 'build/index.js')
        );
    }

    // Register plugin main stylesheet
	if ( file_exists( plugin_dir_path(__FILE__) . 'build/index.css' ) ) {
        wp_enqueue_style('eri-scaffold-blocks-style',
            plugins_url( 'build/index.css', __FILE__ ),
            array(),
            filemtime(plugin_dir_path(__FILE__) . 'build/index.css')
        );
	}

    // Register core blocks styles
    $core_blocks_style_paths = glob( plugin_dir_path( __FILE__ ) . 'build/core-blocks/**/style-index.css' );

    if ( ! empty( $core_blocks_style_paths ) ) {
        foreach ( $core_blocks_style_paths as $core_blocks_style_path ) {
            $block_directory = basename( str_replace( 'style-index.css', '' , $core_blocks_style_path ) );

             if ( 'gravityforms' === $block_directory ) {
                wp_register_style('eri-scaffold-blocks-' . $block_directory,
                    plugins_url( 'build/core-blocks/' . $block_directory . '/style-index.css', __FILE__ ),
                    array(),
                    filemtime( $core_blocks_style_path )
                );
            } else {
                wp_enqueue_style('core-' . $block_directory . '-block-style',
                    plugins_url( 'build/core-blocks/' . $block_directory . '/style-index.css', __FILE__ ),
                    array(),
                    filemtime( $core_blocks_style_path )
                );
            }
        }
    }

    $blocks = array();

    $block_paths = glob( plugin_dir_path( __FILE__ ) . 'src/{,*/}block-*/', GLOB_BRACE );

    if ( ! empty( $block_paths ) ) {
        foreach ( $block_paths as $block_path ) {
            $dir = basename( $block_path );

            if ( !isset( $blocks[$dir] ) ) {
                preg_match( '/src\/(.*)\//m', $block_path, $block_directory_matches );

                if ( ! empty( $block_directory_matches ) && ! empty( $block_directory_matches[1] ) ) {
                    $blocks[ $dir ] = array( 'block_directory' => $block_directory_matches[1] );
                }
            }
        }
    }

    foreach ( $blocks as $dir => $additional_args ) {
        $args = array(
            'style_handles'         => array(), // Block type front end and editor style handles.
            'editor_style_handles'  => array(), // Block type editor only style handles.
            'script_handles'        => array(), // Block type front end and editor script handles.
            'view_script_handles'   => array(), // Block type front end only script handles.
            'editor_script_handles' => array(), // Block type editor only script handles.
        );

        $render_callback_function = str_replace( '-', '_', $dir ) . '_save';

        if ( function_exists( $render_callback_function ) ) {
            $args['render_callback'] = $render_callback_function;
        }

        if ( ! empty( $additional_args['block_directory'] ) ) {
            $block_directory = $additional_args['block_directory'];
        } else {
            $block_directory = $dir;
        }

        $dependencies_file = array();
        if ( file_exists( plugin_dir_path( __FILE__ ) . 'src/' . $block_directory . '/dependencies.php' ) ) {
            $dependencies_file = include( plugin_dir_path( __FILE__ ) . 'src/' . $block_directory . '/dependencies.php' );
        }

        if ( file_exists( plugin_dir_path( __FILE__ ) . 'build/' . $block_directory . '/style-index.css' ) ) {
            $args['style_handles'] = array( $dir . '-front-end-styles' );

            $dependencies = array();

            if ( ! empty( $dependencies_file ) &&  ! empty( $dependencies_file['styles'] ) ) {
                $dependencies = array_merge( $dependencies, $dependencies_file['styles'] );
            }

            wp_register_style(
                $dir . '-front-end-styles',										                            // label
                plugins_url( 'build/' . $block_directory . '/style-index.css', __FILE__ ),					// CSS file
                $dependencies,														                            // dependencies
                filemtime( plugin_dir_path( __FILE__ ) . 'build/' . $block_directory . '/style-index.css' )	// set version as file last modified time
            );
            wp_enqueue_style( $dir . '-front-end-styles' );
        }

        if ( file_exists( plugin_dir_path( __FILE__ ) . 'build/' . $block_directory . '/editor.css' ) ) {
            $args['editor_style_handles'] = array( $dir . '-editor-styles' );

            wp_register_style(
                $dir . '-editor-styles',									                            // label
                plugins_url( 'build/' . $block_directory . '/editor.css', __FILE__ ),					// CSS file
                array(),														                        // dependencies
                filemtime( plugin_dir_path( __FILE__ ) . 'build/' . $block_directory . '/editor.css' )	// set version as file last modified time
            );
        }

        if ( file_exists( plugin_dir_path( __FILE__ ) . 'build/' . $block_directory . '/script.js' ) ) {
            $args['script_handles'] = array( $dir . '-scripts' );

            $dependencies = array();

            if ( file_exists( plugin_dir_path( __FILE__ ) . 'build/' . $block_directory . '/script.asset.php' ) ) {
                $asset_file = include( plugin_dir_path( __FILE__ ) . 'build/' . $block_directory . '/script.asset.php' );

                if ( ! empty( $asset_file ) && ! empty( $asset_file['dependencies'] ) ) {
                    $dependencies = $asset_file['dependencies'];
                }
            }

            if ( ! empty( $dependencies_file ) &&  ! empty( $dependencies_file['scripts'] ) ) {
                $dependencies = array_merge( $dependencies, $dependencies_file['scripts'] );
            }

            wp_register_script(
                $dir . '-scripts',								                                        // label
                plugins_url( 'build/' . $block_directory . '/script.js', __FILE__ ),					// CSS file
                $dependencies,														            // dependencies
                filemtime( plugin_dir_path( __FILE__ ) . 'build/' . $block_directory . '/script.js' )	// set version as file last modified time
            );
            wp_enqueue_script( $dir . '-scripts' );
        }

        if ( file_exists( plugin_dir_path( __FILE__ ) . 'build/' . $block_directory . '/index.js' ) ) {
            $args['editor_script_handles'] = array( $dir . '-editor-scripts' );

            wp_register_script(
                $dir . '-editor-scripts',								                                // label
                plugins_url( 'build/' . $block_directory . '/index.js', __FILE__ ),					    // CSS file
                array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-i18n' ),		    // dependencies
                filemtime( plugin_dir_path( __FILE__ ) . 'build/' . $block_directory . '/index.js' )	// set version as file last modified time
            );
        }

        // Register the block
        register_block_type( __DIR__ . '/build/' . $block_directory, $args );
    }

    if ( function_exists( 'wp_set_script_translations' ) ) {
	/**
	 * Adds internationalization support.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/designers-developers/developers/internationalization/
	 * @link https://make.wordpress.org/core/2018/11/09/new-javascript-i18n-support-in-wordpress/
	 */
	wp_set_script_translations( 'eri-scaffold-blocks-editor-script', 'eri-scaffold-blocks', plugin_dir_path( __FILE__ ) . '/languages' );
	}

    // Register frontend styles
    $frontend_style_paths = glob( plugin_dir_path( __FILE__ ) . 'build/dependencies/*.css' );

    if ( ! empty( $frontend_style_paths ) ) {
        foreach ( $frontend_style_paths as $frontend_style_path ) {
            $style_file_name = basename( $frontend_style_path );

            wp_register_style( 'eri-scaffold-' . str_replace( '.css', '', $style_file_name ),
                plugins_url( 'build/dependencies/' . $style_file_name, __FILE__ ),
                array(),
                filemtime( $frontend_style_path )
            );
        }

        wp_enqueue_style( 'eri-scaffold-select-mock' );
    }

    // Register frontend scripts
    $frontend_script_paths = glob( plugin_dir_path( __FILE__ ) . 'build/dependencies/*.js' );

    if ( ! empty( $frontend_script_paths ) ) {
        foreach ( $frontend_script_paths as $frontend_script_path ) {
            $script_file_name = basename( $frontend_script_path );

            wp_register_script( 'eri-scaffold-' . str_replace( '.js', '', $script_file_name ),
                plugins_url( 'build/dependencies/' . $script_file_name, __FILE__ ),
                array(),
                filemtime( $frontend_script_path )
            );
        }

        wp_enqueue_script( 'eri-scaffold-select-mock' );
    }
}

/**
 *
 * Enqueue editor scripts and styles.
 */
function eri_scaffold_blocks_editor_assets() {
	if ( file_exists( plugin_dir_path(__FILE__) . 'build/editor.js' ) ) {
        wp_enqueue_script('eri-scaffold-blocks-editor-script',
            plugins_url( 'build/editor.js', __FILE__ ),
            array( 'wp-edit-post' ),
            filemtime(plugin_dir_path(__FILE__) . 'build/editor.js')
        );
    }

	if ( file_exists( plugin_dir_path(__FILE__) . 'build/editor.css' ) ) {
        wp_enqueue_style('eri-scaffold-blocks-editor-style',
            plugins_url( 'build/editor.css', __FILE__ ),
            array(),
            filemtime(plugin_dir_path(__FILE__) . 'build/editor.css')
        );
	}

    // Register core blocks scripts
    $core_blocks_script_paths = glob( plugin_dir_path( __FILE__ ) . 'build/core-blocks/**/index.js' );

    if ( ! empty( $core_blocks_script_paths ) ) {
        foreach ( $core_blocks_script_paths as $core_blocks_script_path ) {
            $block_directory = basename( str_replace( 'index.js', '' , $core_blocks_script_path ) );

            wp_enqueue_script('core-' . $block_directory . '-block-script',
                plugins_url( 'build/core-blocks/' . $block_directory . '/index.js', __FILE__ ),
                array(),
                filemtime( $core_blocks_script_path )
            );
        }
    }

    // Register core blocks editor styles
    $core_blocks_style_paths = glob( plugin_dir_path( __FILE__ ) . 'build/core-blocks/**/editor.css' );

    if ( ! empty( $core_blocks_style_paths ) ) {
        foreach ( $core_blocks_style_paths as $core_blocks_style_path ) {
            $block_directory = basename( str_replace( 'editor.css', '' , $core_blocks_style_path ) );

            wp_enqueue_style('core-' . $block_directory . '-block-editor-style',
                plugins_url( 'build/core-blocks/' . $block_directory . '/editor.css', __FILE__ ),
                array(),
                filemtime( $core_blocks_style_path )
            );
        }
    }

    wp_enqueue_style( 'eri-scaffold-select-mock' );
}
add_action( 'enqueue_block_editor_assets', 'eri_scaffold_blocks_editor_assets' );

function eri_scaffold_blocks_enqueue_block_assets() {
    if ( is_admin() ) {
        wp_enqueue_script( 'eri-scaffold-directory-archive-scripts' );
    }
}
add_action( 'enqueue_block_assets', 'eri_scaffold_blocks_enqueue_block_assets' );

function buildImageTagFromId($id, $defaultAlt = '', $size = 'full', $class = '', $attr = array()) {

    $alt = get_post_meta(absint($id), '_wp_attachment_image_alt', true);
    if (!$alt) {
        $alt = $defaultAlt;
    }
    $class .= ' attachment-' . $size;
    $class .= ' size-' . $size;
    $class = trim($class);

    $attr = array_merge(array('alt' => $alt, 'class' => $class, 'loading' => false), $attr);

    return wp_get_attachment_image($id, $size, false, $attr);
}