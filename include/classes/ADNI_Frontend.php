<?php
// Exit if accessed directly
if ( ! defined( "ABSPATH" ) ) exit;
if ( ! class_exists( 'ADNI_Frontend' ) ) :

class ADNI_Frontend {
	
	public function __construct() 
	{
        // Fronten AD Manager
		add_action( 'wp', array( __CLASS__, 'frontend_ad_manager' ), 4);
    }


    /*
	 * Frontend AD Manager
	 *
	 * @access public
	 * @return null
	*/
	public static function frontend_ad_manager()
	{
		if( isset( $_GET['_ning_front'] ) && !empty( $_GET['_ning_front'] ) )
		{
			require_once(ADNI_TPL_DIR.'/frontend_manager/index.php');
			
			exit;
		}
	}
}
endif;
?>