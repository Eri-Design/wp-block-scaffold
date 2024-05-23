<?php
/**
 * Functions and hooks for custom post types.
 */

add_action( 'init', 'eri_scaffold_register_post_types' );

/**
 * Register custom post types.
 *
 * @return void
 */
function eri_scaffold_register_post_types() {
    global $wp_post_types;

    // Get the post object
    $post_object = $wp_post_types['post'];

    // Update the labels
    $post_object->labels->name = 'Stories';
    $post_object->labels->singular_name = 'Story';
    $post_object->labels->add_new = 'Add Story';
    $post_object->labels->add_new_item = 'Add Story';
    $post_object->labels->edit_item = 'Edit Story';
    $post_object->labels->new_item = 'Story';
    $post_object->labels->view_item = 'View Story';
    $post_object->labels->search_items = 'Search Stories';
    $post_object->labels->not_found = 'No stories found';
    $post_object->labels->not_found_in_trash = 'No stories found in Trash';
    $post_object->labels->all_items = 'All Stories';
    $post_object->labels->menu_name = 'Stories';
    $post_object->labels->name_admin_bar = 'Story';
}