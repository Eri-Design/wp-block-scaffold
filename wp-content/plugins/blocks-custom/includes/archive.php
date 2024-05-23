<?php
/**
 * Adds class indicating cat children (called by: wp_list_categories).
 *
 * @return string Returns the dropdown onChange redirection script.
 */
function eri_scaffold_archives_add_has_children_class($css_classes, $category, $depth, $args) {
    $children = get_term_children( $category->term_id, 'archive-category' );
    if ( count( $children ) ) {
        $css_classes[] = 'cat-item-has-children';
    }
    return $css_classes;
}
add_filter( "category_css_class", "eri_scaffold_archives_add_has_children_class", 10, 4 );

/**
 * Gets parent category slugs for Archive post
 *
 * @param integer $post_id Archive post ID
 *
 * @return array Returns array of parent category slugs
 */
function erI_scaffold_get_parent_category_slugs( $post_id ) {
    $return = array();

    $terms = get_the_terms( get_the_ID(), 'archive-category' );
    if ( ! is_wp_error( $terms ) && false !== $terms ) {
        foreach ( $terms as $term ) {
            if ( $term->parent !== 0 ) {
                $parent_term = get_term( $term->parent, 'archive-category' );
                if ( ! is_wp_error( $parent_term ) && $parent_term ) {
                   $return[] = $parent_term->slug;
                }
            }
        }
    }
    return $return;
}
