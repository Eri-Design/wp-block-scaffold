<?php
/**
 * Functions related to the theme templates.
 *
 * @package EriScaffoldTheme
 */

/**
 * Use the page for posts content in the core/post-content block.
 *
 * @param string|null   $pre_render   The pre-rendered content. Default null.
 * @param array         $parsed_block The block being rendered.
 * @param WP_Block|null $parent_block If this is a nested block, a reference to the parent block.
 *
 * @return string|null  The updated content.
 */
function eri_scaffold_template_pre_render_block( $pre_render, $parsed_block, $parent_block ) {
    if ( ! isset( $parsed_block['blockName'] ) ) {
		return $pre_render;
	}

	if ( 'core/post-content' === $parsed_block['blockName'] ) {
		if ( is_home() ) {
			$blog_page_id = get_option( 'page_for_posts' );

			if ( ! empty( $blog_page_id)) {
				$pre_render = do_blocks( get_the_content( null, false, $blog_page_id) );
			}
		}
	}

    return $pre_render;
}
add_filter( 'pre_render_block', 'eri_scaffold_template_pre_render_block', 10, 3 );

/**
 * Modify the main query to ignore sticky posts on the blog posts page.
 *
 * @param WP_Query $query The WP_Query instance.
 */
function eri_scaffold_modify_page_for_posts_query( $query ) {
    if ( ! $query->is_main_query() ) {
		return;
	}

	if ( is_category() || is_tag() ) {
		$query->set( 'posts_per_page', 12 );
	} elseif ( is_home() ) {
		$blog_page_id = get_option( 'page_for_posts' );
		
		if ( empty( $blog_page_id ) ) {
			return;
		}

		$blog_page_content = get_the_content( null, false, $blog_page_id );
		
		if ( empty( $blog_page_content ) ) {
			return;
		}

		$blog_page_blocks = parse_blocks( $blog_page_content );

		if ( empty( $blog_page_blocks ) ) {
			return;
		}
	}
}
add_action( 'pre_get_posts', 'eri_scaffold_modify_page_for_posts_query' );
