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

// add_action('woocommerce_after_order_notes', 'custom_checkout_field');

// function custom_checkout_field($checkout){
//     echo '<div id="custom_checkout_field"><h2>' . __('New Heading') . '</h2>';

//     woocommerce_form_field('custom_field_name', array(
//         'type'          => 'file',
//         'class'         => array('my-field-class form-row-wide'),
//         'label'         => __('Custom Additional Field'),
//         'placeholder'   => __('New Custom Field'),
//         'required' => true
//     ),
//     $checkout->get_value('custom_field_name'));
//     echo '</div>';
// }


add_action( 'woocommerce_after_order_notes', 'bbloomer_add_woo_account_registration_fields' );
  
function bbloomer_add_woo_account_registration_fields() { ?>
<p class="form-row validate-required" id="image" data-priority="">
    <label for="image" class="">Image (JPG, PNG, PDF)<abbr class="required" title="required">*</abbr></label>
    <span class="woocommerce-input-wrapper">
        <input type='file' name='custom_field_name' accept='image/*,.pdf' required>
    </span>
</p>
<?php }


// Checkout Process
// add_action('woocommerce_checkout_process', 'customised_checkout_field_process');

// function customised_checkout_field_process(){
//     // Show an error message if the field is not set.
//     if (!$_POST['custom_field_name']) 
//         wc_add_notice(__('Please choose a file!') , 'error');
// }

// --------------
// 2. Validate new field
 
add_filter( 'woocommerce_checkout_process', 'bbloomer_validate_woo_account_registration_fields');
  
function bbloomer_validate_woo_account_registration_fields() {
    if ( isset( $_POST['custom_field_name'] ) && empty( $_POST['custom_field_name'] ) ) {
        // $errors->add( 'image_error', __( 'Please provide a valid image', 'woocommerce' ) );
        wc_add_notice(__('Please choose a file!') , 'error');
    }
    return $errors;
}


/**
* Update the value given in custom field
*/

add_action('woocommerce_checkout_update_order_meta', 'custom_checkout_field_update_order_meta');

function custom_checkout_field_update_order_meta($order_id){
    // if (!empty($_POST['custom_field_name'])) 
    if(@$_POST['custom_field_name']){
        
            $file_name = $_FILES['fileToUpload']['name'];
            $file_temp = $_FILES['fileToUpload']['tmp_name'];

            $upload_dir = wp_upload_dir();
            $image_data = file_get_contents( $file_temp );
            $filename = basename( $file_name );
            $filetype = wp_check_filetype($file_name);
            $filename = time().'.'.$filetype['ext'];

            if ( wp_mkdir_p( $upload_dir['path'] ) ) {
              $file = $upload_dir['path'] . '/' . $filename;
            }
            else {
              $file = $upload_dir['basedir'] . '/' . $filename;
            }

            file_put_contents( $file, $image_data );
            $wp_filetype = wp_check_filetype( $filename, null );
            $attachment = array(
              'post_mime_type' => $wp_filetype['type'],
              'post_title' => sanitize_file_name( $filename ),
              'post_content' => '',
              'post_status' => 'inherit'
            );

            $attach_id = wp_insert_attachment( $attachment, $file );
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
            wp_update_attachment_metadata( $attach_id, $attach_data );

            // echo $attach_id;

            update_post_meta($order_id, 'vicode_checkout_file',sanitize_text_field($attach_id));
    }
}