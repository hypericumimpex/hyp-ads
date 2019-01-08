<?php 
/**
 * Plugin Name: HYP Ads
 * Plugin URI: https://github.com/hypericumimpex/hyp-ads/
 * Description: Ads Component
 * Version: 1.1.0
 * Author: Romeo C.
 * Author URI: https://github.com/hypericumimpex/
 * Requires at least: 4.6.1
 * Tested up to: 4.8.3
 *
 * Text Domain: adn
 * Domain Path: /localization/
 *
 */
// Exit if accessed directly
if ( ! defined( "ABSPATH" ) ) exit;
// Current plugin version
if ( ! defined( "ADNI_VERSION" ) ) define( "ADNI_VERSION", "1.1.0" );


if ( ! class_exists( "ADNI_Adning" ) ) : 
    
class ADNI_Adning { 
    
    /**
     * @var The single instance of the class
     */
    protected static $_instance = null;
    
    /**
     * ADNI_Adning Instance
     *
     * Ensures only one instance of ADNI_Adning is loaded or can be loaded.
     * @return ADNI_Adning - Main instance
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
        
		 new ADNI_Init();
        
		 // actions --------------------------------------------------------
		 add_action( 'plugins_loaded', array(__CLASS__, 'load_textdomain') );
    }
    
    private static function define_constants() {
		define( 'ADNI_ENVATO_ID', 269693 );
		define( 'ADNI_FILE', __FILE__ );
		define( 'ADNI_BASENAME', plugin_basename( dirname( __FILE__ ) ));
		define( "ADNI_URL", plugin_dir_url( __FILE__ ) );
		define( "ADNI_DIR", plugin_dir_path( __FILE__ ) );
		define( "ADNI_ASSETS_URL", ADNI_URL. 'assets' );
		define( "ADNI_ASSETS_DIR", ADNI_DIR. 'assets' );
		define( 'ADNI_INC_URL', ADNI_URL. 'include' );
		define( 'ADNI_INC_DIR', ADNI_DIR. 'include' );
		define( "ADNI_CLASSES_URL", ADNI_INC_URL. '/classes' );
		define( "ADNI_CLASSES_DIR", ADNI_INC_DIR. '/classes' );
		define( 'ADNI_TPL_URL', ADNI_INC_URL. '/templates' );
		define( 'ADNI_TPL_DIR', ADNI_INC_DIR. '/templates' );
		define( 'ADNI_AJAXURL', admin_url( 'admin-ajax.php' ) );
		define( 'ADNI_GDPR_COOKIE', '_mjs__ning_gdpr_approve' );

		$upload = wp_upload_dir();
		define( 'ADNI_UPLOAD_FOLDER', 'adning/');
		define( 'ADNI_UPLOAD_DIR', $upload['basedir'].'/'.ADNI_UPLOAD_FOLDER);
		define( 'ADNI_UPLOAD_SRC', $upload['baseurl'].'/'.ADNI_UPLOAD_FOLDER);
		
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
		if ( 0 !== strncmp( 'ADNI_', $class, 3 ) ) 
		{
			return;
		}

		$dirs = array(
			ADNI_CLASSES_DIR
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
 * Returns the main instance of ADNI_Adning to prevent the need to use globals.
 * @return ADNI_Adning
 */
ADNI_Adning::instance();