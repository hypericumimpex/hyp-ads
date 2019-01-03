<?php
// Exit if accessed directly
if ( ! defined( "ABSPATH" ) ) exit;
if ( ! class_exists( 'ADNI_Multi' ) ) :

class ADNI_Multi {

    /*
	 * Check if the plugin is network activated
	 *
	 * @access public
	 * @return bool
	*/
	public static function is_network_activated()
	{
		$active = 0;
		
		if( is_multisite() )
		{
			if( !function_exists( 'is_plugin_active_for_network' ) )
			{
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
				// Makes sure the plugin is defined before trying to use it
			}
			 
			if( is_plugin_active_for_network( 'adning/adning.php' ) ) 
			{
				$active = 1;
			}
		}
		
		return $active;
    }
    


    /*
	 * Load site option - get_option() - for multisite installations.
	 * ADNI_multi::get_option();
	 *
	 * @access public
	 * @return array/string
	*/
	public static function get_option( $name, $value = '' )
	{
		global $wpdb;
		
		if( self::is_network_activated() )
		{
			$option = get_site_option($name, $value);
		}
		else
		{
			$option = get_option($name, $value);
		}
		
		return $option;
    }
    


    /*
	 * Update option - update_option() - for multisite installations.
	 * ADNI_multi::update_option();
	 *
	 * @access public
	 * @return null
	*/
	public static function update_option( $name, $value = '' )
	{
		global $wpdb;
		
		update_option($name, $value);
		
		if( self::is_network_activated() && is_main_site() )
		{
			update_site_option($name, $value);
		}
	}
	

	/*
	 * Get Post Meta - get_post_meta() - for multisite installations.
	 * ADNI_multi::get_post_meta();
	 *
	 * @access public
	 * @return null
	*/
	public static function get_post_meta( $id, $name, $value = '' )
	{
		self::wpmu_load_from_main_start();
			$value = get_post_meta($id, $name, $value);
		self::wpmu_load_from_main_stop();

		$value = !empty($value) && array_key_exists(0, $value) ? $value[0] : $value;

		return $value;
	}



    /*
	 * Run shortcodes - do_shortcode() - for multisite installations.
	 *
	 * @access public
	 * @return string
	*/
	public static function do_shortcode( $shortcode )
	{	
		self::wpmu_load_from_main_start();
		    $value = do_shortcode( $shortcode );
		self::wpmu_load_from_main_stop();
		
		return $value;
	}
    


    /*
	 * MULTISITE get data from main site using set_blog_id() or switch_to_blog()
	 *
	 * @access public
	 * @return null
	*/
	public static function wpmu_load_from_main_start()
	{	
		if( self::is_network_activated() && !is_main_site() )
		{
			switch_to_blog( BLOG_ID_CURRENT_SITE );
		}
	}
	
	
	/*
	 * MULTISITE get data from main site using set_blog_id() or switch_to_blog()
	 *
	 * @access public
	 * @return null
	*/
	public static function wpmu_load_from_main_stop()
	{	
		if( self::is_network_activated() && is_main_site() )
		{
			restore_current_blog();	
		}
	}
}
endif;
?>