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
			'display_filter_load_posts'
		);


		foreach($_dn_ajax_actions as $ajax_action)
		{
			add_action( 'wp_ajax_' . $ajax_action, array(__CLASS__, str_replace( '-', '_', $ajax_action )));
			add_action( 'wp_ajax_nopriv_' . $ajax_action, array(__CLASS__, str_replace( '-', '_', $ajax_action )));
		}
    }
    


    public static function adblocker_detected()
    {
		$set_arr = ADNI_Main::settings();
		$settings = $set_arr['settings'];

		if( $settings['adblock_detect'] )
		{
			echo json_encode(array('alert' => $settings['adblock_message']));
		}
        exit;
	}
	



	public static function display_filter_load_posts()
	{
		global $wpdb;

		$search = $_POST['search'];
		$post_type = $_POST['post_type'];
		$h = '';
		$response = array();
		$all_posts = $wpdb->get_results( "SELECT ID, post_title FROM ".$wpdb->prefix."posts WHERE post_type = '".$post_type."' AND post_status = 'publish' AND post_title LIKE '%".$search."%'" );
		
		//$h.= '<li class="active-result" data-option-array-index="0"></li>';
		foreach($all_posts as $i => $post)
		{
			$response[] = array("id" => $post->ID, "name" => $post->post_title);
			//$selected = !empty($posts) && is_array($posts) ? in_array($post->ID, $posts) ? 'selected' : '' : '';
			//$h.= '<option value="'.$post->ID.'" '.$selected.'>'.$post->post_title.' - (ID:'.$post->ID.')</option>';
			//$h1.= '<option value="'.$post->ID.'" '.$selected.'>'.$post->post_title.' - (ID:'.$post->ID.')</option>';
			//$h.= '<li class="active-result" data-option-array-index="'.$post->ID.'">'.$post->post_title.' - (ID:'.$post->ID.')</li>';
		}

		//echo $h.$post_type;
		echo json_encode($response);
		//echo json_encode(array('h1' => $h1, 'h' => $h));
		exit;
	}

}
endif;