<?php
/**
 * Plugin Name: Adning Woocommerce Buy and Sell Add-On 
 * Plugin URI: http://adning.com
 * Description: Adning Add-On to sell advertisement spots on your website using Woocommerce.
 * Version: 1.0.0
 * Author: Tunafish
 * Author URI: http://tunasite.com
 * Requires at least: 4.6
 * Tested up to: 5.0.3
 *
 * Text Domain: adn
 * Domain Path: /localization/
 *
 */
// Exit if accessed directly
if ( ! defined( "ABSPATH" ) ) exit;
// Current plugin version
if ( ! defined( "ADNI_WOO_VERSION" ) ) define( "ADNI_WOO_VERSION", "1.0.0" );


if ( ! class_exists( "Adning_Woocommerce" ) ) : 
    
class Adning_Woocommerce { 
    
    /**
     * @var The single instance of the class
     */
    protected static $_instance = null;
    
    /**
     * Adning_Woocommerce Instance
     *
     * Ensures only one instance of AAdning_Woocommerce is loaded or can be loaded.
     * @return Adning_Woocommerce - Main instance
     */
    public static function instance() { 
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function __construct() {
        
        // Define constants
        self::define_constants();
        
		 // Load Classes ---------------------------------------------------
		 spl_autoload_register( array( __CLASS__, 'autoload' ) );
        
		 new ADNI_WOO_Init();
        
		 // actions --------------------------------------------------------
		 add_action( 'plugins_loaded', array(__CLASS__, 'load_textdomain') );
    }
    
    private static function define_constants() {
		//define( 'ADNI_WOO_ENVATO_ID', 269693 );
		define( 'ADNI_WOO_FILE', __FILE__ );
		define( 'ADNI_WOO_BASENAME', plugin_basename( dirname( __FILE__ ) ));
		define( "ADNI_WOO_URL", plugin_dir_url( __FILE__ ) );
		define( "ADNI_WOO_DIR", plugin_dir_path( __FILE__ ) );
		define( "ADNI_WOO_ASSETS_URL", ADNI_WOO_URL. 'assets' );
		define( "ADNI_WOO_ASSETS_DIR", ADNI_WOO_DIR. 'assets' );
		define( 'ADNI_WOO_INC_URL', ADNI_WOO_URL. 'include' );
		define( 'ADNI_WOO_INC_DIR', ADNI_WOO_DIR. 'include' );
		define( "ADNI_WOO_CLASSES_URL", ADNI_WOO_INC_URL. '/classes' );
		define( "ADNI_WOO_CLASSES_DIR", ADNI_WOO_INC_DIR. '/classes' );
		define( 'ADNI_WOO_TPL_URL', ADNI_WOO_INC_URL. '/templates' );
		define( 'ADNI_WOO_TPL_DIR', ADNI_WOO_INC_DIR. '/templates' );
		define( 'ADNI_WOO_AJAXURL', admin_url( 'admin-ajax.php' ) );

		$upload = wp_upload_dir();
		define( 'ADNI_WOO_UPLOAD_FOLDER', 'adning/');
		define( 'ADNI_WOO_UPLOAD_DIR', $upload['basedir'].'/'.ADNI_WOO_UPLOAD_FOLDER);
		define( 'ADNI_WOO_UPLOAD_SRC', $upload['baseurl'].'/'.ADNI_WOO_UPLOAD_FOLDER);
		
    }
	
	public static function load_textdomain() {
		load_plugin_textdomain( 'adn', false, plugin_basename( dirname( __FILE__ ) ) . '/localization' );
	}
	
	
	
	/**
	 * Autoload classes
	 *
	 * @param string $class
	 */
	public static function autoload( $class ) 
	{
		// Not a adning class
		if ( 0 !== strncmp( 'ADNI_WOO_', $class, 3 ) ) 
		{
			return;
		}

		$dirs = array(
			ADNI_WOO_CLASSES_DIR
		);

		foreach ( $dirs as $dir ) 
		{
			if ( file_exists( $file = "$dir/$class.php" ) ) 
			{
				require_once $file;
				return;
			}
		}
	}
    

}
    
endif;

/**
 * Returns the main instance of Adning_Woocommerce to prevent the need to use globals.
 * @return Adning_Woocommerce
 */
Adning_Woocommerce::instance();
?>