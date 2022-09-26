<?php
/**
 * Plugin Name: WC Checkout File Type Field
 * Description: This plugin will add a Custom WooCommerce File Type Field on the Checkout Page
 * Plugin URI: https://vicodemedia.com
 * Author: Victor Rusu
 * Version: 1.1
**/

//* Don't access this file directly
defined( 'ABSPATH' ) or die();

/**
 * Register the "book" custom post type
 */
// function vicode_setup_field() {
//     // register_post_type( 'book', ['public' => true ] );
//     // return "Testing";
// } 
// add_action( 'init', 'vicode_setup_field' );
 
// // small changes

// /**
//  * Activate the plugin.
//  */
// function vicode_activate() { 
//     // Trigger our function that registers the custom post type plugin.
//     vicode_setup_field(); 
// }
// register_activation_hook( __FILE__, 'vicode_activate' );


// /**
//  * Deactivation hook.
//  */
// function vicode_deactivate() {
//     // Unregister the post type, so the rules are no longer in memory.
//     // unregister_post_type( 'book' );
// }
// register_deactivation_hook( __FILE__, 'vicode_deactivate' );

/**

* Add custom field to the checkout page

*/

add_action('woocommerce_after_order_notes', 'custom_checkout_field');

function custom_checkout_field($checkout)
{
    echo '<div id="custom_checkout_field"><h2>' . __('New Heading') . '</h2>';
    
    woocommerce_form_field('custom_field_name', array(
        'type'          => 'text',
        'class'         => array('my-field-class form-row-wide'),
        'label'         => __('Custom Additional Field') ,
        'placeholder'   => __('New Custom Field') ,
    ),
    $checkout->get_value('custom_field_name'));
    echo '</div>';
}