<?php
/**
 * Additional functions for posts.
 *
 * @package EriScaffoldTheme
 */

/**
 * Set the default block template for posts.
 *
 * @param array  $args        The post type registration arguments.
 * @param string $post_type   The name of the post type.
 *
 * @return array The modified post type registration arguments.
 */
function eri_scaffold_set_default_block_template_for_posts( $args, $post_type ) {
    // if ( 'post' === $post_type ) {
    //     $args['template'] = array(
    //         array( 'core/pattern', array( 'slug' => 'eri-scaffold/single-story-hero' ) ),
    //         array( 'core/pattern', array( 'slug' => 'eri-scaffold/social-sharing' ) ),
    //         array( 'core/pattern', array( 'slug' => 'eri-scaffold/single-story-content' ) ),
    //         array( 'core/pattern', array( 'slug' => 'eri-scaffold/single-story-sticky-media' ) ),
    //     );
    // }

    return $args;
}
add_filter( 'register_post_type_args', 'eri_scaffold_set_default_block_template_for_posts', 10, 2 );

/**
 * Hide the uncategorized category on the frontend.
 *
 * @param array         $terms      Array of found terms.
 * @param array|null    $taxonomies An array of taxonomies if known.
 * @param array         $args       An array of get_terms() arguments.
 * @param WP_Term_Query $term_query The WP_Term_Query object.
 *
 * @return array The modified terms array.
 */
function eri_scaffold_hide_uncategorized_category( $terms, $taxonomies, $args, $term_query ) {
    if ( ! is_admin() && in_array( 'category', $taxonomies, true ) ) {
        $terms = array_filter( $terms, function( $term ) {
            if ( ! empty( $term->name ) && 'Uncategorized' === $term->name ) {
                return false;
            }

            return true;
        } );
    }

    return $terms;
}
add_filter( 'get_terms', 'eri_scaffold_hide_uncategorized_category', 10, 4 );
