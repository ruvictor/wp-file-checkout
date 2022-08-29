<?php
/**
 * Plugin Name: WC Checkout File Filed Plugin
 * Description: This plugin will add a Custom WooCommerce File Field on the Checkout Page
 * Plugin URI: https://vicodemedia.com
 * Author: Victor Rusu
 * Version: 1
**/

//* Don't access this file directly
defined( 'ABSPATH' ) or die();

/**
 * Register the "book" custom post type
 */
function vicode_setup_field() {
    // register_post_type( 'book', ['public' => true ] );
    // return "Testing";
} 
add_action( 'init', 'vicode_setup_field' );
 
 
/**
 * Activate the plugin.
 */
function vicode_activate() { 
    // Trigger our function that registers the custom post type plugin.
    vicode_setup_field(); 
}
register_activation_hook( __FILE__, 'vicode_activate' );


/**
 * Deactivation hook.
 */
function vicode_deactivate() {
    // Unregister the post type, so the rules are no longer in memory.
    // unregister_post_type( 'book' );
}
register_deactivation_hook( __FILE__, 'vicode_deactivate' );