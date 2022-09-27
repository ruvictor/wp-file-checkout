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
			<label for="vicode_file" class="vicode_class"><a>Select an image</a></label>
			<div id="vicode_filelist"></div>
		</div>
	<?php
}

/**
 * Validation
**/
add_action('woocommerce_checkout_process', 'customised_checkout_field_process');

function customised_checkout_field_process(){
    // Show an error message if the field is not set.
    if (!$_POST['vicode_file_field']) 
        wc_add_notice(__('<strong>Billing Image</strong> is a required field.') , 'error');
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
		// $path = $upload_dir[ 'path' ] . '/' . basename( $_FILES[ 'vicode_file' ][ 'name' ] );

		$file_name = $_FILES['vicode_file']['name'];
		$file_temp = $_FILES['vicode_file']['tmp_name'];

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

		echo $attach_id;
	}
	die;
}


/**
 * Insert the File URL in Order Meta
**/
add_action( 'woocommerce_checkout_update_order_meta', 'vicode_save_what_we_added' );

function vicode_save_what_we_added( $order_id ){
	if( ! empty(intval($_POST[ 'vicode_file_field' ]))) {
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
		echo '<p class="form-field form-field-wide wc-customer-user">' . wp_get_attachment_image( $file, 'medium' ) . '</p>';
	}
}