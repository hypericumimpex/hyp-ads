<?php
// Exit if accessed directly
if ( ! defined( "ABSPATH" ) ) exit;
if ( ! class_exists( 'ADNI_Main' ) ) :

class ADNI_Main {



	/*
	 * parse_args - Array Merge
	 * ADNI_Main::parse_args( $args, $default );
	 *
	 * @access public
	 * @return array
	*/
	public static function parse_args($args, $default)
	{
		if(!is_array($args)) return $default;
		if(!is_array($default)) return $args;

		$is_multi = self::array_is_multi($default);
		$is_multi = !$is_multi ? self::array_is_multi($args) : $is_multi;

		return $is_multi ? self::array_merge_recursive_distinct($default, $args) : array_merge( $default, $args );
		//return array_merge( $default, $args );
	}

	// Check if array is multidimentional
	public static function array_is_multi($a) 
	{
		if(empty($a)) return false;
		foreach ($a as $v) 
		{
			if (is_array($v)) return true;
		}
		return false;
	}
	
	
	/*
	 * Load Settings
	 *
	 * @access public
	 * @return array
	*/
	public static function settings()
	{
		$default_settings = array(
			'debug' => 0,
			'disable' => array(
				'all_ads' => 0,
				'non_singular_ads' => 0,
				'user_role_ads' => ''
			),
			'gdpr' => array(
				'disable_till_approved' => 0,
				'cookie_name' => '',
				'cookie_value' => '',
				'show_cookie_message' => 0,
				'cookie_message_page_url' => '',
				'cookie_message_text' => __('We use cookies to offer you a better browsing experience. If you continue to use this site, you consent to our use of cookies.','adn'),
				'cookie_message_approve_btn' => __('I Accept Cookies','adn')
			),
			'custom_css' => '',
			'placement_area_head' => '',
			'placement_area_body' => '',
			'adsense_pubid' => ''
		);
		
		//$settings = wp_parse_args(ADNI_Multi::get_option('_adning_settings', array()), $default_settings);
		$settings = self::parse_args( ADNI_Multi::get_option('_adning_settings', array()), $default_settings );
		
		$default_roles = array(
			'access_role' => 'subscriber',
			'admin_role' => 'administrator',
			'create_banner_role' => 'editor',
			'create_adzone_role' => 'editor'
		);
		//$roles = wp_parse_args(get_option('_adning_roles', array()), $default_roles);
		$roles = wp_parse_args(ADNI_Multi::get_option('_adning_roles', array()), $default_roles);
		
		return array(
			'settings' => $settings,
			'roles' => $roles
		);	
	}



	/** 
	 * Merge multidimentional arrays.
	 * https://gist.github.com/ptz0n/1646171
	 * 
	 * ADN_Main::array_merge_recursive_distinct( $defaults, $args );
	*/
	public static function array_merge_recursive_distinct()
	{
		$arrays = func_get_args();
		$base = array_shift($arrays);
		foreach($arrays as $array) {
			reset($base);
			while(list($key, $value) = @each($array)) {
				if(is_array($value) && @is_array($base[$key])) {
					$base[$key] = self::array_merge_recursive_distinct($base[$key], $value);
				}
				else {
					$base[$key] = $value;
				}
			}
		}
		return $base;
	}
	
	
	
	/*
	 * Link Masking
	 *
	 * @access public
	 * @return url
	*/
	public static function link_masking($id)
	{
		return get_bloginfo('url').'?_dnlink='.$id;
	}


	
	/*
	 * Auto Positioning array
	 * Array containing banner / Adzone ids that are linked to an auto positioning spot.
	 *
	 * @access public
	 * @return array
	*/
	public static function auto_positioning($args = array())
	{
		//return get_option('_adning_auto_positioning', $args);
		return ADNI_Multi::get_option('_adning_auto_positioning', $args);
	}

	
	
	
	/*
	 * Common Banner Sizes
	 *
	 * @access public
	 * @return array
	*/
	public static function banner_sizes()
	{
		return array(
			array('size' => '125x125', 'name' => __('square button','adn')),
			array('size' => '300x250', 'name' => __('medium rectangle','adn')),
			array('size' => '728x90', 'name' => __('leaderboard','adn')),
			array('size' => '728x300', 'name' => __('pop-under','adn')),
			array('size' => '234x60', 'name' => __('half banner','adn')),
			array('size' => '468x60', 'name' => __('full banner','adn')),
			array('size' => '120x600', 'name' => __('skyscraper','adn')),
			array('size' => '160x600', 'name' => __('wide skyscraper','adn')),
			array('size' => '240x600', 'name' => __('extra wide skyscraper','adn')),
			array('size' => '300x600', 'name' => __('half page ad','adn')),
			array('size' => '120x240', 'name' => __('vertical banner','adn')),
			array('size' => '240x400', 'name' => __('vertical rectangle','adn'))
		);
	}
	
	
	
	/*
	 * List All Capabilities for specific User or Role in WordPress
	 *
	 * @access public
	 * @return array
	*/
	public static function capabilities($args = array())
	{
		$defaults = array(
		 'id' => get_current_user_id(),
		 'role' => '', 
		);
		$args = wp_parse_args($args, $defaults);
		
		// Capabilities for specific role
		if( !empty($args['role']) )
		{
			return get_role( $args['role'] )->capabilities;
		}
		// Capabilities for specific user id
		else
		{
			$data = get_userdata( $args['id'] );
			if( is_object( $data)) 
			{
				return $data->allcaps;
				//echo '<pre>' . print_r( $data->allcaps, true ) . '</pre>';
			}	
		}
	}
	
	
	
	
	/*
	 * ADNING Capabilities for specific Role
	 *
	 * @access public
	 * @return string
	*/
	public static function ADNI_capability( $role = 'administrator' )
	{
		$caps = self::capabilities(array('role' => $role));
		//echo '<pre>'.print_r($caps,true).'</pre>';
		$check_for = array('activate_plugins','edit_others_posts','publish_posts','edit_posts','read');
		
		if(!empty($caps))
		{
			foreach($check_for as $cap)
			{
				if(array_key_exists($cap, $caps))
				{
					return $cap;
				}
			}
		}
		
		return 'activate_plugins';
	}
	
	
	/**
	 * Create data-* attributes
	 * data-key="value"
	*/
	public static function create_data_attributes($args = array())
	{
		$h = '';
		if(!empty($args))
		{
			foreach ($args as $key => $value) 
			{
				$h.= ' data-'.$key.'="'.$value.'"';
			}
		}

		return $h;
	}


	public static function dropdown_roles( $selected = '' ) 
	{
		$r = '';
		$editable_roles = array_reverse( get_editable_roles() );
		
		foreach ( $editable_roles as $role => $details ) 
		{
			$name = translate_user_role($details['name'] );
			// preselect specified role
			if ( $selected == $role ){
				$r .= "\n\t<option selected='selected' value='" . esc_attr( $role ) . "'>$name</option>";
			} else {
				$r .= "\n\t<option value='" . esc_attr( $role ) . "'>$name</option>";
			}
		}

		return $r;
	}
	


}

endif;
?>