<?php
/*
*
*	File including all the functionality for the back-end
*
*
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$plugin_base_name = EOS_QUICK_LINKS_BASE_NAME;

//Enque scripts and styles
add_action( 'admin_enqueue_scripts','eos_quil_enqueue_scripts_styles' );
function eos_quil_enqueue_scripts_styles(){
	$opts = get_site_option( 'eos_quil' );
	if( 
		( 
			isset( $_GET['menu'] ) &&
			( 
				( !isset( $_GET['action'] ) && isset( $_GET['menu'] ) && isset( $opts['last_top_bar_menu'] ) && absint( $_GET['menu'] ) === $opts['last_top_bar_menu'] )
				|| ( isset( $_GET['menu'] ) && isset( $_GET['eos_quil'] ) && wp_verify_nonce( sanitize_key( $_GET['eos_quil'] ),'eos_quil' ) )
			)
		)
		|| ( isset( $_GET['page'] ) && 'eos_quil_settings_page' === $_GET['page'] )
	){
		wp_enqueue_script( 'eos-quick-links',EOS_QUICK_LINKS_URL.'/assets/js/quick-links-admin.js',array( 'jquery' ),EOS_QUICK_LINKS_VERSION );
		wp_enqueue_style( 'eos-quick-links',EOS_QUICK_LINKS_URL.'/assets/css/quick-links-admin.css',array(),EOS_QUICK_LINKS_VERSION );
	}	
}


add_action( 'admin_menu', 'eos_quil_settings_page' );
//Add plugin main settings page
function eos_quil_settings_page() {
	add_menu_page(
		esc_html__( 'Top bar links', 'eos-quil' ),
		esc_html__( 'Top Bar Links','eos-quil' ),
		apply_filters( 'eos_quil_settings_capability','manage_options' ),
		'eos_quil_settings_page',
		'eos_quil_settings_output_page',
		'dashicons-admin-links',
		60
	);
	$settings_url = eos_quil_get_settings_url();
	$title = strpos( $settings_url,'eos_quil' ) > 0 ? __( 'Create Top Bar Menu','eos-quil' ) : __( 'Edit Top Bar Menu','eos-quil' );	
	add_submenu_page(
		'eos_quil_settings_page',
		esc_html( $title ),
		esc_html( $title ),
		apply_filters( 'eos_quil_settings_capability','manage_options' ),
		esc_url( $settings_url ),
		'',
		10
	);
}

//Output settings page
function eos_quil_settings_output_page(){
	require EOS_QUICK_LINKS_DIR.'/admin/templates/pages/ql-settings.php';
}
//Return URL according the precence of the top bar menu
function eos_quil_get_settings_url(){
	$opts = get_site_option( 'eos_quil' );
	$menu_exists = isset( $opts['last_top_bar_menu'] ) && absint( $opts['last_top_bar_menu'] ) > 0;
	if( $menu_exists ){
		$url = add_query_arg( 
			'menu',
			absint( $opts['last_top_bar_menu'] ),
			admin_url( 'nav-menus.php' ) 
		);
	}
	else{ 
		$url = add_query_arg( 
			'eos_quil',
			wp_create_nonce( 'eos_quil' ),
			admin_url( 'nav-menus.php?action=edit&menu=0' ) 
		);
	}
	return $url;
}

add_filter( "plugin_action_links_$plugin_base_name", 'eos_quil_plugin_add_settings_link' );
//It adds a settings link to the action links in the plugins page
function eos_quil_plugin_add_settings_link( $links ) {
    $settings_link = '<a class="eos-quil-setts" href="'.add_query_arg( 'page','eos_quil_settings_page',admin_url() ).'">' . esc_html__( 'Settings','eos-dp' ). '</a>';
    array_push( $links, $settings_link );
  	return $links;
}
	
add_action( 'admin_bar_menu','eos_quick_links_admin_menu',40 );
// Add custom links to the admin top  bar
function eos_quick_links_admin_menu( $wp_admin_bar ){
	$current_user = wp_get_current_user();
	$roles = $current_user->roles;
	$opts = get_site_option( 'eos_quil' );
	$who_can = isset( $opts['who_can'] ) ? $opts['who_can'] : apply_filters( 'eos_quil_who_can_see','administrator' );
	$current_caps = array();
	$can_role = get_role( $who_can );
	$can_caps = $can_role->capabilities;
	foreach( $roles as $role_slug ){
		$role = get_role( $role_slug );
		foreach( $role->capabilities as $key => $v ){
			if( strpos( $key,'/' ) > 0 ){
				$keyArr = explode( '/',$key );
				$key = $keyArr[0];
			}
			$current_caps[$key] = $v;
		}
	}
	$current_caps = array_keys( array_filter( $current_caps,'strlen' ) );
	$can_caps = array_keys( array_filter( $can_caps,'strlen' ) );
	$user_can = true;
	foreach( $can_caps as $cap ){
		$capArr = explode( '/',$cap );
		$cap = $capArr[0];			
		if( !in_array( $cap,$current_caps ) ){
			$user_can = false;
			break;
		}
	}
	if( !$user_can ) return;
	$menu = false;
    $locations = get_nav_menu_locations();
    if ( $locations && isset( $locations['eos_quil_top_bar'] ) ) {
        $menu = wp_get_nav_menu_object( $locations['eos_quil_top_bar'] );
    }
	if( !$menu ){
		$wp_admin_bar->add_menu( array(
			'id'    => 'eos-quil-0',
			'title' => esc_html__( 'Add Menu','eos-quil' ),
			'href' => esc_url( eos_quil_get_settings_url() ),
			'meta' => array( 'title' => esc_html__( 'Add Top Bar Menu','eos-quil' ) )
		));		
		return $wp_admin_bar;
	}
	$items = wp_get_nav_menu_items( $menu );
	$wp_admin_bar->add_menu( array(
		'id'    => 'eos-quil-0',
		'title' => esc_html( $menu->name ),
	));
	foreach( $items as $item ){
		$target = isset( $item->target ) && '' !== $item->target ? $item->target : '_self';
		$title = isset( $item->attr_title ) ? $item->attr_title : '';
		$class = isset( $item->classes ) && is_array( $item->classes ) ? implode( ' ',$item->classes ) : '';
		$args = array(
			'parent' => 'eos-quil-'.esc_attr( $item->menu_item_parent ),
			'id'     => 'eos-quil-'.esc_attr( $item->ID ),
			'title'  => esc_html( $item->title ),
			'href' => esc_url( $item->url ),
			'meta' => array( 'target' => esc_attr( $target ),'title' => esc_attr( $title ),'class' => esc_attr( $class ) )
		);
		$wp_admin_bar->add_node( $args );
	}
	return $wp_admin_bar;
}

add_action( 'wp_create_nav_menu','eos_quil_create_nav_menu_actions' );
//Actions fired on menu creation
function eos_quil_create_nav_menu_actions( $menu_id ){
	if( !isset( $_POST['_wp_http_referer'] ) ) return;
	$args = explode( 'eos_quil=',$_POST['_wp_http_referer'] );
	if( !isset( $args[1] ) ) return;
	$args = explode( '&',$args[1] );
	if( wp_verify_nonce( sanitize_key( $args[0] ),'eos_quil' ) ){
		$opts = get_site_option( 'eos_quil' );
		if( !$opts || !is_array( $opts ) ){
			$opts = array();
		}
		$locations = get_theme_mod('nav_menu_locations');
		//set the menu to the top bar location and save into database
		$locations['eos_quil_top_bar'] = sanitize_text_field( $menu_id );
		set_theme_mod( 'nav_menu_locations', $locations );
		$opts['last_top_bar_menu'] = absint( $menu_id );
		update_site_option( 'eos_quil',$opts );
	}
}

add_action( 'wp_delete_nav_menu','eos_quil_delete_nav_menu_actions' );
//Actions fired on menu deletion
function eos_quil_delete_nav_menu_actions( $menu_id ){
	$opts = get_site_option( 'eos_quil' );
	if( !$opts || !is_array( $opts ) ){
		$opts = array();
	}	
	if( isset( $opts['last_top_bar_menu'] ) ){
		if( $menu_id == $opts['last_top_bar_menu'] ){
			unset( $opts['last_top_bar_menu'] );
			update_site_option( 'eos_quil',$opts );
			wp_safe_redirect( add_query_arg( 'page','eos_quil_settings_page',admin_url( 'admin.php' ) ) );
		}
	}
}

//Return top bar menu
function eos_quil_get_top_bar_menu(){
	$menu = false;
	$locations = get_nav_menu_locations();
	if ( $locations && isset( $locations['eos_quil_top_bar'] ) ) {
		$menu = wp_get_nav_menu_object( $locations['eos_quil_top_bar'] );
	}
	return $menu;
}		

//Display the save button and related messages
function eos_quil_save_button(){
	$page = isset( $_GET['page'] ) ? $_GET['page'] : '';
	?>
	<div class="eos-quil-btn-wrp" style="margin-top:40px">
		<input type="submit" name="submit" class="eos-quil-save-<?php echo esc_attr( $page ); ?> button button-primary submit-dp-opts" value="<?php esc_html_e( 'Save all changes','eos-quil' ); ?>"  />
		<?php eos_quil_ajax_loader_img(); ?>
		<div>
			<div class="eos-hidden eos-quil-opts-msg notice notice-success eos-quil-opts-msg_success msg_response" style="padding:10px;margin:10px;">
				<span><?php esc_html_e( 'Options saved.','eos-quil' ); ?></span>
			</div>
			<div class="eos-quil-opts-msg_failed eos-quil-opts-msg notice notice-error eos-hidden msg_response" style="padding:10px;margin:10px;">
				<span><?php esc_html_e( 'Something went wrong, maybe you need to refresh the page and try again, but you will lose all your changes','eos-quil' ); ?></span>
			</div>
			<div class="eos-quil-opts-msg_warning eos-quil-opts-msg notice notice-warning eos-hidden msg_response" style="padding:10px;margin:10px;">
				<span></span>
			</div>
		</div>
	</div>
<?php
}

//Display ajax loader
function eos_quil_ajax_loader_img(){
	?>
	<img alt="<?php esc_attr_e( 'Ajax loader','eos-quil' ); ?>" class="ajax-loader-img eos-not-visible" width="30" height="30" src="<?php echo EOS_QUICK_LINKS_URL; ?>/assets/img/ajax-loader.gif" />
	<?php
}