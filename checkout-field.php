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
 * Importing our css and js files
**/
add_action('wp_enqueue_scripts', 'vicode_callback_for_setting_up_scripts');
function vicode_callback_for_setting_up_scripts() {
    wp_register_style( 'vicode_css', plugins_url('css/style.css',__FILE__ ));
    wp_enqueue_style( 'vicode_css' );
    wp_enqueue_script( 'vicode_js', plugins_url('js/script.js',__FILE__ ), array( 'jquery' ) );
}


/**
 * Display File Field on the Checkout Page
**/
add_action( 'woocommerce_after_checkout_billing_form', 'vicode_file_upload_field' );

function vicode_file_upload_field() {
	?>
		<div class="form-row form-row-wide">
			<input type="file" id="vicode_file" name="vicode_file" />
			<input type="hidden" name="vicode_file_field" />
			<label for="vicode_file" class="vicode_class"><a>Select a cool image</a></label>
			<div id="vicode_filelist"></div>
		</div>
	<?php
}


/**
 * Processing data on the server side
**/
// Fires authenticated Ajax actions for logged-in users
add_action( 'wp_ajax_vicodeupload', 'vicode_file_upload' );
// Fires non-authenticated Ajax actions for logged-out users
add_action( 'wp_ajax_nopriv_vicodeupload', 'vicode_file_upload' );

function vicode_file_upload(){

	$upload_dir = wp_upload_dir();

	if ( isset( $_FILES[ 'vicode_file' ] ) ) {
		$path = $upload_dir[ 'path' ] . '/' . basename( $_FILES[ 'vicode_file' ][ 'name' ] );

		if( move_uploaded_file( $_FILES[ 'vicode_file' ][ 'tmp_name' ], $path ) ) {
			echo $upload_dir[ 'url' ] . '/' . basename( $_FILES[ 'vicode_file' ][ 'name' ] );
		}
	}
	die;
}


/**
 * Insert the File URL in Order Meta
**/
add_action( 'woocommerce_checkout_update_order_meta', 'vicode_save_what_we_added' );

function vicode_save_what_we_added( $order_id ){
	if( ! empty( $_POST[ 'vicode_file_field' ] ) ) {
		update_post_meta( $order_id, 'vicode_file_field', sanitize_text_field( $_POST[ 'vicode_file_field' ] ) );
	}
}

/**
 * display the image on edit order dashboard
**/
add_action( 'woocommerce_admin_order_data_after_order_details', 'vicode_order_meta_general' );

function vicode_order_meta_general( $order ){

	$file = get_post_meta( $order->get_id(), 'vicode_file_field', true );
	if( $file ) {
		echo '<img src="' . esc_url( $file ) . '" style="width:100%;margin:20px 0 0;" />';
	}

}