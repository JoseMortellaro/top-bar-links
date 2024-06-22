<?php
/**
 *  File required by options.php that includes all the functions needed for admin ajax requests
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_action("wp_ajax_eos_quil_save_settings", "eos_quil_save_settings");
//Save plugn options
function eos_quil_save_settings(){
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : false;
	if (
		false === $nonce
		|| ! wp_verify_nonce( sanitize_key( $nonce ),'eos_quil_setts' ) //check for intentions
		|| !current_user_can( apply_filters( 'eos_quil_settings_capability','manage_options' ) )
	) {
	   echo 0;
	   die();
	   exit;
	}
	if( isset( $_POST['role'] ) && !empty( $_POST['role'] ) ){
		$opts = get_site_option( 'eos_quil' );
		if( !$opts || !is_array( $opts ) ){
			$opts = array();
		}
		$opts['who_can'] = sanitize_text_field( $_POST['role'] );
		update_site_option( 'eos_quil',$opts );
	}
	echo 1;
	die();
	exit;
}