<?php
/*
*
*	File for the plugin main settings
*
*
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$opts = get_site_option( 'eos_quil' );
$who_can = isset( $opts['who_can'] ) ? $opts['who_can'] : 'administrator';
global $wp_roles;
$roles = $wp_roles->roles;
?>
<section id="eos-quil-section">
	<?php wp_nonce_field( 'eos_quil_setts','eos_quil_setts' ); ?>
	<div id="eos-quil-roles" class="eos-quil-sec">
		<h2><?php esc_html_e( 'Minimum role to see the custom Top Bar Menu','eos-quil' ); ?></h2>
		<select id="eos-quil-roles">
		<?php
		foreach( $roles as $role => $arr ){
			?>
			<option value="<?php echo esc_attr( $role ); ?>"<?php echo $who_can === $role ? ' selected' : ''; ?>><?php echo esc_html( $arr['name'] ); ?></option>
			<?php
		}
		?>
		</select>
	</div>
	<div class="eos-quil-sec">
		<?php 
		$menu = eos_quil_get_top_bar_menu();	
		if( $menu ){
			?>
			<h2><?php printf( esc_html__( 'Actual Top Bar Menu: %s','eos-quil' ),$menu->name ); ?></h2>
			<?php 
		}
		?>
		<div id="eos-quil-actions">
		<?php 
		if( $menu && isset( $opts['last_top_bar_menu'] )){
			$delete_url = wp_nonce_url(
				add_query_arg(
					array(
						'action' => 'delete',
						'menu' => absint( $opts['last_top_bar_menu'] ),
					),
					admin_url( 'nav-menus.php' )
				),
				'delete-nav_menu-'.esc_attr( $opts['last_top_bar_menu'] )
			);
			$edit_url = add_query_arg( 
				'menu',
				absint( $opts['last_top_bar_menu'] ),
				admin_url( 'nav-menus.php' ) 
			);
		?>
			<a href="<?php echo esc_url( $edit_url ); ?>" id="eos-quil-edit-menu" class="button"><?php esc_html_e( 'Edit Menu','eos-quil' ); ?></a>
			<a href="<?php echo esc_url( $delete_url ); ?>" id="eos-quil-edit-menu" class="button"><?php esc_html_e( 'Delete Menu','eos-quil' ); ?></a>
		<?php
		}else{ 
			$new_url = add_query_arg( 
				'eos_quil',
				wp_create_nonce( 'eos_quil' ),
				admin_url( 'nav-menus.php?action=edit&menu=0' ) 
			);	
		?>
			<a href="<?php echo esc_url ( $new_url ); ?>" id="eos-quil-add-menu" class="button"><?php esc_html_e( 'Create Admin Top Bar Menu','eos-quil' ); ?></a>
		<?php } ?>
		</div>
	</div>
	<div class="eos-quil-sec">
		<?php eos_quil_save_button(); ?>
	</div>
</section>