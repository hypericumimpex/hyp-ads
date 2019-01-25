<?php
// Exit if accessed directly
if ( ! defined( "ABSPATH" ) ) exit;
if ( ! class_exists( 'ADNI_WOO_Init' ) ) :

class ADNI_WOO_Init {
	
	public function __construct() 
	{
		// Run this on activation.
		register_activation_hook( ADNI_WOO_FILE, array( __CLASS__, 'install' ) );
        register_deactivation_hook(ADNI_WOO_FILE, array( __CLASS__, 'deactivate'));
        
        // Load Classes ---------------------------------------------------
        new ADNI_WOO_Main();
        new ADNI_WOO_Templates();
        new ADNI_WOO_Add_To_Cart();

        // Actions --------------------------------------------------------
        add_action( 'init', array( __CLASS__, 'init_method') );
        add_action( 'admin_init', array( __CLASS__, 'check_for_plugin_updates') );

        // Filters --------------------------------------------------------
    }


    /**
	 * Install Adning
	 */
	public static function install() 
	{
        // Check required plugins ( Adning, Woocommerce )
        self::adning_is_active();
        ADNI_Sell::check_if_table_exists( 'adning_sell' );

        $adni_woo_product = ADNI_Multi::get_option('_adning_woo_product', 0);
		
		if( empty($adni_woo_product) )
		{
			$product_id = ADNI_WOO_Main::create_product( array('post_title' => 'Advertising AD Spot') );
			ADNI_Multi::update_option( '_adning_woo_product', $product_id );
		}
    }

    /**
	 * Deactivate Adning
	 */
	public static function deactivate()
	{

    }


    /**
     * Check if main Adning plugin is active
     * https://codex.wordpress.org/Function_Reference/deactivate_plugins
     * https://wordpress.stackexchange.com/questions/127818/how-to-make-a-plugin-require-another-plugin
     */
    public static function adning_is_active()
    {
        if( is_admin() && current_user_can( 'activate_plugins' ) ) 
        {
            if( !class_exists( 'ADNI_Init' ) || !class_exists('WC_Product') )
            {
                self::return_error();
                //deactivate_plugins( plugin_basename( __FILE__ ) );
                //wp_die( __('Sorry, but Adning Woocommerce requires the Main Adning plugin and Woocommerce to be installed and activated.','adn') );
            }
            if( class_exists( 'ADNI_Init' ) )
            {
                $activation = ADNI_Multi::get_option('adning_activation', array());
                if( empty($activation) )
                {
                    self::return_error(sprintf(__('Sorry, Adning Woocommerce requires the Main Adning plugin to be activated. %s','adn'), '<a href="admin.php?page=adning-updates">'.__('Activate Adning','adn').'</a>'));
                }
            }
        }
    }



    public static function return_error($text = '')
    {
        $text = !empty($text) ? $text : __('Sorry, but Adning Woocommerce requires the Main Adning plugin and Woocommerce to be installed and activated.','adn');
        deactivate_plugins( plugin_basename( __FILE__ ) );
        wp_die( $text );
    }
    



    /**
	 * Init
	 */
	public static function init_method()
	{

    }



    /*
	 * Plugin auto update
	 *
	 * @access public
	 * @return null
	*/
	public static function check_for_plugin_updates()
	{
		// Only works if Adning is activated - http://adning.com
		//if (class_exists('ADNI_Multi')) {
			//set_site_transient('update_plugins', null); // Just for testing to see if the available plugin update gets shown. IF THIS IS ON ACTUALL PLUGIN UPDATES MAY NOT WORK: WP error: Plugin update failed.
			
			//$activation = ADNI_Multi::get_option('adning_activation', array());
			//$license_key = !empty($activation) ? $activation['license-key'] : '';

			require( ADNI_WOO_INC_DIR.'/classes/ADNI_WOO_PLU_Auto_Plugin_Updater.php');
			$api_url = 'http://tunasite.com/updates/?plu-plugin=ajax-handler';
			// current plugin version | remote url | Plugin Slug (plugin_directory/plugin_file.php) | users envato license key (default: '') | envato item ID (default: '')
			new ADNI_WOO_PLU_Auto_Plugin_Updater(ADNI_WOO_VERSION, $api_url, ADNI_WOO_BASENAME.'/'.ADNI_WOO_BASENAME.'.php'); // $license_key, ADNI_ENVATO_ID
		//}
	}
}
endif;
?>