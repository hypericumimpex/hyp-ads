<?php
/*
 * AJAX REQUEST FUNCTIONS
 *
 * http://codex.wordpress.org/AJAX_in_Plugins
 * For not logged-in users use: add_action('wp_ajax_nopriv_my_action', 'my_action_callback');
*/
// Exit if accessed directly
if( !defined("ADNI_VERSION")) exit;
if ( ! class_exists( 'ADNI_Ajax' ) ) :

class ADNI_Ajax {

	public function __construct() {
		
		$_dn_ajax_actions = array(
			'adblocker_detected', 
		);


		foreach($_dn_ajax_actions as $ajax_action)
		{
			add_action( 'wp_ajax_' . $ajax_action, array(__CLASS__, str_replace( '-', '_', $ajax_action )));
			add_action( 'wp_ajax_nopriv_' . $ajax_action, array(__CLASS__, str_replace( '-', '_', $ajax_action )));
		}
    }
    


    public static function adblocker_detected()
    {
        echo json_encode(array('alert' => 'You are using AD Blocker!'));
        exit;
    }

}
endif;