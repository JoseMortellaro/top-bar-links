<?php
if( !defined( 'WP_UNINSTALL_PLUGIN') ){
    die;
}
delete_site_option( 'eos_quil' );
$locations = get_nav_menu_locations();
if ( $locations && is_array( $locations ) && isset( $locations['eos_quil_top_bar'] ) ) {
	$menu = wp_get_nav_menu_object( $locations['eos_quil_top_bar'] );
	if( $menu && is_object( $menu ) && isset( $menu->name ) ){
		wp_delete_nav_menu( $menu->name );
	}
}