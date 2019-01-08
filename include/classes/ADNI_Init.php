<?php
// Exit if accessed directly
if ( ! defined( "ABSPATH" ) ) exit;
if ( ! class_exists( 'ADNI_Init' ) ) :

class ADNI_Init {
	
	public function __construct() 
	{
		// Run this on activation.
		register_activation_hook( ADNI_FILE, array( __CLASS__, 'install' ) );
		register_deactivation_hook(ADNI_FILE, array( __CLASS__, 'deactivate'));
		
		// Load Classes -----------------------------------------------
		new ADNI_CPT();
		new ADNI_Main();
		new ADNI_Templates();
		new ADNI_Filters();
		new ADNI_Shortcodes();
		new ADNI_API();
		new ADNI_Uploader();
		new ADNI_Ajax();
		new ADNI_Multi();
		new ADNI_Frontend();
		new ADNI_Updates();
		//new ADNI_Sell();
		
		
		// Load Extensions -----------------------------------------------
		require_once(ADNI_INC_DIR. '/extensions/spr_columns/SPR_Columns.php');
		new SPR_Columns();
		
		
		// Actions --------------------------------------------------------
		add_action( 'parse_request', array(__CLASS__, 'handle_api_requests'), 0);
		add_action( 'wp_loaded', array(__CLASS__, 'define_variables') );
		add_action( 'wp_loaded', array(__CLASS__, 'register_scripts') );
		add_action( is_admin() ? 'admin_enqueue_scripts' : 'wp_enqueue_scripts' , array(__CLASS__, 'enqueue_scripts'));
		add_action( 'admin_menu', array(__CLASS__, 'register_admin_menu'));
		add_action( 'parent_file', array( __CLASS__, 'menu_highlight' ) );
		add_action( 'admin_init', array( __CLASS__, 'check_for_plugin_updates') );
		
		// Banner click ---------------------------------------------------
		add_action( 'wp', array( __CLASS__, 'banner_click_action' ), 4);	
	}
	
	
	
	
	/**
	 * Install Adning
	 */
	public static function install() 
	{	
		$set = ADNI_Main::settings();
		$_adning_settings = ADNI_Multi::get_option('_adning_settings', array());
		$settings = ADNI_Main::parse_args( $_adning_settings, $set['settings'] );
		$settings = ADNI_Multi::update_option('_adning_settings', $settings);

		ADNI_CPT::add_custom_caps(array('role' => $settings['roles']['create_banner_role'], 'cpt' => ADNI_CPT::$banner_cpt));
		ADNI_CPT::add_custom_caps(array('role' => $settings['roles']['create_adzone_role'], 'cpt' => ADNI_CPT::$adzone_cpt));
		ADNI_CPT::add_custom_caps(array('role' => $settings['roles']['create_campaign_role'], 'cpt' => ADNI_CPT::$campaign_cpt));
	}
	


	/**
	 * Deactivate Adning
	 */
	public static function deactivate()
	{
		// Deregister Adning License
		$activation = ADNI_Multi::get_option('adning_activation', array());
		if( !empty($activation))
		{
			$resp = ADNI_Activate::deregister(array('license-key' => $activation['license-key']));
			return $resp['msg'];
		}
	}
	
	
	
	
	/**
	 * Define vars
	 */
	public static function define_variables()
	{
		$settings = ADNI_Main::settings();
		define( "ADNI_ACCESS_ROLE", ADNI_Main::ADNI_capability($settings['roles']['access_role']) );
		define( "ADNI_ADMIN_ROLE", ADNI_Main::ADNI_capability($settings['roles']['admin_role']) );
		define( "ADNI_BANNERS_ROLE", ADNI_Main::ADNI_capability($settings['roles']['create_banner_role']) );
		define( "ADNI_ADZONES_ROLE", ADNI_Main::ADNI_capability($settings['roles']['create_adzone_role']) );
		define( "ADNI_CAMPAIGNS_ROLE", ADNI_Main::ADNI_capability($settings['roles']['create_campaign_role']) );
	}
	
	
	
	/*
	 * Banner Click action
	 *
	 * @access public
	 * @return null
	*/
	public static function banner_click_action()
	{
		// Banner Click
		if( isset( $_GET['_dnlink'] ) && !empty( $_GET['_dnlink'] ) )
		{
			add_filter('strack_track_page_view', 0);
			$banner_id = is_numeric($_GET['_dnlink']) ? $_GET['_dnlink'] : base64_decode($_GET['_dnlink']);
			$banner = ADNI_CPT::load_post($banner_id);
			
			// Filter -------------------------------------------------------
			apply_filters('adning_save_stats', array(
				'type' => 'click',
				'banner_id' => $banner_id
			));
			
			header('Location: '. $banner['args']['banner_url']);
			exit;
		}
	}



	
	
	/*
	 * API Requests
	 *
	 * @access public
	 * @return null
	*/
	public static function handle_api_requests()
	{
		
	}
	
	
	
	/*
	 * Add New role Capabilities.
	 *
	 * @access public
	 * @return null
	*/
	public static function add_role_caps() 
	{
		// Add the roles you'd like to administer the custom post types
		$roles = array('adning_manage_banners','editor','administrator');
		
		// Loop through each role and assign capabilities
		foreach($roles as $the_role) 
		{ 
			$role = get_role($the_role);
			
			$role->add_cap( 'read' );
			$role->add_cap( 'read_psp_project');
			$role->add_cap( 'read_private_psp_projects' );
			$role->add_cap( 'edit_psp_project' );
			$role->add_cap( 'edit_psp_projects' );
			$role->add_cap( 'edit_others_psp_projects' );
			$role->add_cap( 'edit_published_psp_projects' );
			$role->add_cap( 'publish_psp_projects' );
			$role->add_cap( 'delete_others_psp_projects' );
			$role->add_cap( 'delete_private_psp_projects' );
			$role->add_cap( 'delete_published_psp_projects' );
		}
	}
	
	
	
	/*
	 * register_scripts
	 *
	 * @access public
	 * @return null
	*/
	public static function register_scripts()
	{
		$var_array = array(
			//'debug' => IMC_DEBUG,
			'ajaxurl' => ADNI_AJAXURL
		);

		// Scripts
		wp_register_script( '_ning_global', ADNI_ASSETS_URL.'/dist/_ning.bundle.js', array( 'jquery' ), ADNI_VERSION, true );
		wp_localize_script( '_ning_global', '_adn_', $var_array );
		wp_register_script( '_ning_admin_global', ADNI_ASSETS_URL.'/dist/_ning_admin.bundle.js', array( 'jquery' ), ADNI_VERSION, true );
		/*wp_register_script( '_ning_global', ADNI_ASSETS_URL.'/dev/js/_ning.js', array( 'jquery' ), ADNI_VERSION, true );
		wp_localize_script( '_ning_global', '_adn_', $var_array );
		wp_register_script( '_ning_admin_global', ADNI_ASSETS_URL.'/dev/js/_ning_admin.js', array( 'jquery' ), ADNI_VERSION, true );
		wp_register_script( '_ning_jquery_plugins', ADNI_ASSETS_URL.'/dev/js/jQuery.adnplugins.js', array( 'jquery' ), ADNI_VERSION, true );
		//wp_register_script( '_ning_jssor', ADNI_ASSETS_URL.'/js/jssor.slider-22.2.16.min.js', array( 'jquery' ), ADNI_VERSION, true );
		wp_register_script( '_ning_jssor', ADNI_ASSETS_URL.'/dev/js/jssor.slider.min.js', array( 'jquery' ), ADNI_VERSION, true );
		*/

		//wp_register_script('_ning_tooltipster', ADNI_INC_URL.'/widgets/tooltipster/tooltipster.bundle.min.js', array('jquery'), ADNI_VERSION, true);
		//wp_register_script( '_ning_chosen', ADNI_INC_URL.'/widgets/chosen/chosen.jquery.min.js', array( 'jquery' ), ADNI_VERSION, true );
		//wp_register_script( '_ning_chosen_sort', ADNI_INC_URL.'/widgets/chosen/jquery-chosen-sortable.min.js', array( 'jquery' ), ADNI_VERSION, true );
		
		// styles
		wp_register_style( '_ning_css', ADNI_ASSETS_URL. '/dist/_ning.bundle.js.css', false, ADNI_VERSION, "all" );
		wp_register_style( '_ning_admin_css', ADNI_ASSETS_URL. '/dist/_ning_admin.bundle.js.css', false, ADNI_VERSION, "all" );
		/*wp_register_style( '_ning_css', ADNI_ASSETS_URL. '/dev/css/_ning.css', false, ADNI_VERSION, "all" );
		wp_register_style( '_ning_admin_css', ADNI_ASSETS_URL. '/dev/css/_ning_admin.css', false, ADNI_VERSION, "all" );
		*/


		//wp_register_style( '_ning_chosen_css', ADNI_INC_URL.'/widgets/chosen/chosen.css', false, ADNI_VERSION, "all" );
		//wp_register_style('_ning_checkbox', ADNI_INC_URL.'/widgets/checkbox/checkbox.css', false, ADNI_VERSION, "all");

		ADNI_Uploader::enqueue_scripts(array('upload_folder' => 'path'));
	}
	
	
	/*
	 * enqueue_scripts
	 *
	 * @access public
	 * @return null
	*/
	public static function enqueue_scripts()
	{
		// Scripts
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');

		// Ad Block detection
		wp_enqueue_script('adning_dummy_advertising', ADNI_ASSETS_URL . '/dev/js/advertising.js');
		
		// styles
		wp_enqueue_style( '_ning_font_awesome_css', ADNI_ASSETS_URL.'/fonts/font-awesome/css/font-awesome.min.css', false, ADNI_VERSION, 'all');

		self::enqueue(
			array(
				'files' => array(
					array('file' => '_ning_css', 'type' => 'style'),
					array('file' => '_ning_global', 'type' => 'script')
				)
			)
		);
		
		// Extentions
		SPR_Columns::enqueue_scripts(array('inc_url' => ADNI_INC_URL, 'version' => ADNI_VERSION));
	}
	
	
	
	/*
	 * insert (enqueue) scripts & styles
	 *
	 * example:
	 * array( array('file' => '_ning_css', type => 'style | script'))
	 *
	 * @access public
	 * @return enqueues scripts and styles when neccesery
	*/
	public static function enqueue($args = array())
	{
		$default = array(
			'files' => array()
		);
		$args = wp_parse_args( $args, $default );
		
		if(!empty($args['files']))
		{
			foreach($args['files'] as $file)
			{
				if( $file['type'] == 'style')
				{
					wp_enqueue_style( $file['file'] );
				}
				else
				{
					wp_enqueue_script( $file['file'] );
				}
			}
		}
	}
	
	
	
	/* ----------------------------------------------------------------
	 * Add Admin Menu
	 * ---------------------------------------------------------------- */
	public static function register_admin_menu()
	{	
		if( 
			isset( $_GET['page'] ) && $_GET['page'] == 'adning' || 
			isset( $_GET['page'] ) && $_GET['page'] == 'ADNI_banners' || 
			isset( $_GET['page'] ) && $_GET['page'] == 'adning-settings' || 
			isset( $_GET['page'] ) && $_GET['page'] == 'adning-role-manager' ||
			isset( $_GET['page'] ) && $_GET['page'] == 'adning-updates' 
			//isset( $_GET['post_type'] ) && $_GET['post_type'] == 'ADNI_single_banner' 
			//isset($_GET['post']) && get_post_type($_GET['post']) == 'vidana' 
		)
		{
			self::enqueue(
				array(
					'files' => array(
						array('file' => '_ning_css', 'type' => 'style'),
						array('file' => '_ning_admin_css', 'type' => 'style'),
						array('file' => '_ning_global', 'type' => 'script'),
						array('file' => '_ning_admin_global', 'type' => 'script')
					)
				)
			);
			
			// Load media
			if( function_exists('wp_enqueue_media') )
			{
				wp_enqueue_media();
			}
		}

		
		// Check if plugin needs update
		ADNI_Updates::needs_update();
		
		
		// Create menu
		add_menu_page(
			__('ADning', 'adn'), 
			__('ADning', 'adn'), 
			ADNI_ACCESS_ROLE,  
			'adning', 
			array( __CLASS__, 'dashboard_template'),
			ADNI_ASSETS_URL.'/images/logo_20.png',
			20 
		);
		
		add_submenu_page('adning', 'ADning', 'ADning', ADNI_ACCESS_ROLE, 'adning', array( __CLASS__, "dashboard_template"));
		add_submenu_page("adning", __('General Settings', 'adn'), __('General Settings', 'adn'), ADNI_ADMIN_ROLE, "adning-settings", array( __CLASS__, "settings_template"));
		add_submenu_page("adning", __('Role Manager', 'adn'), __('Role Manager', 'adn'), ADNI_ADMIN_ROLE, "adning-role-manager", array( __CLASS__, "role_manager_template"));
		add_submenu_page("adning", __('Product License', 'adn'), __('Product License', 'adn'), ADNI_ADMIN_ROLE, "adning-updates", array( __CLASS__, "updates_template"));
		
		
		if( current_user_can(ADNI_ACCESS_ROLE))
		{
			add_filter( 'custom_menu_order', array(__CLASS__, 'submenu_order') );
		}
	}
	
	// MENU FUNCTIONS -------------------------------------------------------
	public static function dashboard_template()
	{
		include( ADNI_TPL_DIR .'/pages.php');
	}
	public static function settings_template()
	{
		include( ADNI_TPL_DIR .'/settings.php');
	}
	public static function role_manager_template()
	{
		include( ADNI_TPL_DIR .'/role_manager.php');
	}
	public static function updates_template()
	{
		include( ADNI_TPL_DIR .'/updates.php');
	}
	
	
	
	public static function submenu_order( $menu_ord ) 
	{
		global $submenu;
			
		// Enable the next line to see all menu orders
		//echo '<pre>'.print_r($submenu['edit.php?post_type=advertising'],true).'</pre>';
		//echo '<pre>'.print_r($submenu['wp-pro-advertising'],true).'</pre>';
		if( isset($submenu['adning']))
		{
			$arr = array();
			$itms = count($submenu['adning']);
			//echo '<pre>'.print_r($submenu['adning'],true).'</pre>';
			
			if( array_key_exists(3, $submenu['adning']) && $submenu['adning'][3][0] === 'ADning' ){ $arr[] = $submenu['adning'][3]; }
			$arr[] = $submenu['adning'][0];
			$arr[] = $submenu['adning'][1];
			$arr[] = $submenu['adning'][2];
			
			
			// Allow (3rd party) add-ons to add item to menu
			if( $itms > 4 )
			{
				for($m = 4; $m < $itms; $m++)
				{
					$arr[] = $submenu['adning'][$m];
				}
			}
			
			$submenu['adning'] = $arr;
		}
	
		return $menu_ord;
	}
		
	
	
	/*
	 * Highlights the correct top level admin menu item for post types.
	*/
	public static function menu_highlight() 
	{
		global $menu, $submenu, $parent_file, $submenu_file, $self, $post_type, $taxonomy;
		
		//$post_type = isset($_GET['page']) && in_array( $_GET['page'], array(ADNI_CPT::$banner_cpt)) ? ADNI_CPT::$banner_cpt : $post_type;
		if( isset( $post_type ) ) 
		{
			if ( in_array( $post_type, array(ADNI_CPT::$banner_cpt, ADNI_CPT::$adzone_cpt) ) ) {
				$submenu_file = 'edit.php?post_type=' . esc_attr( $post_type );
				$parent_file  = 'adning';
			}
		}
		elseif(isset($_GET['page']) && $_GET['page'] === 'adning' )
		{
			//if( in_array( $_GET['page'], array(ADNI_CPT::$banner_cpt)) )
			if(isset($_GET['view']))
			{
				$submenu_file = 'edit.php?post_type=adni_'.$_GET['view'].'s';
				$parent_file  = 'adning';
			}
		}
	}




	/*
	 * Plugin auto update
	 *
	 * @access public
	 * @return null
	*/
	public static function check_for_plugin_updates()
	{
		//set_site_transient('update_plugins', null); // Just for testing to see if the available plugin update gets shown. IF THIS IS ON ACTUALL PLUGIN UPDATES MAY NOT WORK: WP error: Plugin update failed.
		//$activation = get_option('adning_activation', array());
		$activation = ADNI_Multi::get_option('adning_activation', array());
		$license_key = !empty($activation) ? $activation['license-key'] : '';

		require( ADNI_CLASSES_DIR.'/ADNING_PLU_Auto_Plugin_Updater.php');
		$api_url = 'http://tunasite.com/updates/?plu-plugin=ajax-handler';
		// current plugin version | remote url | Plugin Slug (plugin_directory/plugin_file.php) | users envato license key (default: '') | envato item ID (default: '')
		new ADNING_PLU_Auto_Plugin_Updater(ADNI_VERSION, $api_url, ADNI_BASENAME.'/'.ADNI_BASENAME.'.php', $license_key, ADNI_ENVATO_ID);
	}
	
}

endif;
?>