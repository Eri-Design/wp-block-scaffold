<?php
/**
 * Functions and hooks for custom meta fields.
 */

/**
 * Register custom meta fields.
 */
function eri_scaffold_register_meta_fields() {
    // Example registering of meta field.
    // register_post_meta( 'post', '_external_link', array(
    //     'show_in_rest' => true,
    //     'single'       => true,
    //     'type'         => 'string',
    // ) );
}
add_action( 'init', 'eri_scaffold_register_meta_fields' );

/**
 * Add fields to REST API endpoints with custom callbacks.
 */
add_action( 'rest_api_init', function() {
    // Example registering of field for REST API.
    // register_rest_field( 'post',
    //     'external_link',
    //     array(
    //         'get_callback'    => 'example_add_external_link',
    //         'schema'          => null,
    //     )
    // );
} );
