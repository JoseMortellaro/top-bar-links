<?php
/*
Plugin Name: Top Bar Links
Description: It adds quick custom links to the admin top bar
Author: Jose Mortellaro
Author URI: https://josemortellaro.com
Text Domain: eos-quil
Domain Path: /languages/
Version: 1.0.5
*/
/*  This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if( is_admin() ){
	define( 'EOS_QUICK_LINKS_VERSION','1.0.5' );
	define( 'EOS_QUICK_LINKS_URL',untrailingslashit( plugins_url( '', __FILE__ ) ) );
	define( 'EOS_QUICK_LINKS_DIR',untrailingslashit( dirname( __FILE__ ) ) );
	define( 'EOS_QUICK_LINKS_BASE_NAME',untrailingslashit( plugin_basename( __FILE__ ) ) );
	require_once EOS_QUICK_LINKS_DIR.'/admin/ql-admin.php';
	if( defined( 'DOING_AJAX' ) && DOING_AJAX ){
		require_once EOS_QUICK_LINKS_DIR.'/admin/ql-ajax.php';
	}	
	add_action( 'after_setup_theme', 'eos_quil_menu_location' );
}
else{
	add_filter( 'wp_nav_menu_args','eos_quil_exclude_on_frontend',10,2 );
}

//Add admin top bar location
function eos_quil_menu_location(){
	register_nav_menus( array(
		 'eos_quil_top_bar' => esc_html__( 'Admin Top Bar, it will be displayed on the admin top bar','eos-quil' ),
	) );
}

//Prevent Top Bar Links called on front end
function eos_quil_exclude_on_frontend( $args ){
	if( isset( $args['theme_location'] ) && ( '' === $args['theme_location'] || 'eos_quil_top_bar' === $args['theme_location'] ) ){
		$locations = get_nav_menu_locations();
		if( $locations ){
			if( isset( $locations['eos_quil_top_bar'] ) ){
				unset( $locations['eos_quil_top_bar'] );
			}
			$keys = array_keys( $locations ); 
			$args['theme_location'] = isset( $keys[0] ) ? $keys[0] : '';
		}
	}
	return $args;
}