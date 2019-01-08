<?php
// Exit if accessed directly
if ( ! defined( "ABSPATH" ) ) exit;
if ( ! class_exists( 'ADNI_Main' ) ) :

class ADNI_Main {

	public static $IP = false;


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
			'plugin_version' => 0, // The current plugin version to detect updates.
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
			'positioning' => array(
				'post_types' => array('post', 'page') // double arrays do not work with custom parse_args so we added a fix for it below...
			),
			'custom_css' => '',
			'placement_area_head' => '',
			'placement_area_body' => '',
			'adsense_pubid' => '',
			'uninstall_remove_data' => 0,
			'adblock_detect' => 0,
			'adblock_message' => __('You are using AD Blocker!','adn')
		);
		
		$_adning_settings = ADNI_Multi::get_option('_adning_settings', array());
		$settings = self::parse_args( $_adning_settings, $default_settings );
		$settings['plugin_version'] = empty($settings['plugin_version']) ? ADNI_VERSION : $settings['plugin_version'];
		

		// Fix for positioning post_types when post or page is not checked but parse args adds it back anyway.
		if( array_key_exists('positioning', $_adning_settings) )
		{
			if( array_key_exists('post_types', $_adning_settings['positioning']) )
			{
				$settings['positioning']['post_types'] = $_adning_settings['positioning']['post_types'];
			}
		}
		
		
		$default_roles = array(
			'access_role' => 'subscriber',
			'admin_role' => 'administrator',
			'create_banner_role' => 'editor',
			'create_adzone_role' => 'editor',
			'create_campaign_role' => 'editor'
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
	 * Get all roles with specific capability
	 */
	public static function get_roles_with_cap($cap = '')
	{
		//$cap = ADNI_Main::ADNI_capability($set_arr['roles']['create_campaign_role']);
		$roles_with_cap = array();

		if( !empty($cap))
		{
			$editable_roles = array_reverse( get_editable_roles() );

			foreach($editable_roles as $key => $role)
			{
				// echo '<pre>'.print_r($role['capabilities'],true).'</pre>';
				if( array_key_exists($cap, $role['capabilities']) )
				{
					$roles_with_cap[] = $key;
				}
			}
		}

		return $roles_with_cap;
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
	



	/*
	 * Get Visitor IP
	 *
	 * @access public
	 * @return IP
	*/
	public static function get_visitor_ip() 
	{
		// Check to see if we've already retrieved the IP address and if so return the last result.
		if( self::$IP !== false ) { return self::$IP; }
		
		// Check if cronjob is running
		$sapi_type = php_sapi_name();
		if(substr($sapi_type, 0, 3) == 'cli') { return self::$IP; }
	
		// By default we use the remote address the server has.
		$temp_ip = $_SERVER['REMOTE_ADDR'];
	
		// Check to see if any of the HTTP headers are set to identify the remote user.
		// These often give better results as they can identify the remote user even through firewalls etc, 
		// but are sometimes used in SQL injection attacks.
		if (getenv('HTTP_CLIENT_IP')) {
			$temp_ip = getenv('HTTP_CLIENT_IP');
		} elseif (getenv('HTTP_X_FORWARDED_FOR')) {
			$temp_ip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif (getenv('HTTP_X_FORWARDED')) {
			$temp_ip = getenv('HTTP_X_FORWARDED');
		} elseif (getenv('HTTP_FORWARDED_FOR')) {
			$temp_ip = getenv('HTTP_FORWARDED_FOR');
		} elseif (getenv('HTTP_FORWARDED')) {
			$temp_ip = getenv('HTTP_FORWARDED');
		} 

		// Trim off any port values that exist.
		if( strstr( $temp_ip, ':' ) !== FALSE ) {
			$temp_a = explode(':', $temp_ip);
			$temp_ip = $temp_a[0];
		}
		
		// Check to make sure the http header is actually an IP address and not some kind of SQL injection attack.
		$long = ip2long($temp_ip);
	
		// ip2long returns either -1 or FALSE if it is not a valid IP address depending on the PHP version, so check for both.
		if($long == -1 || $long === FALSE) {
			// If the headers are invalid, use the server variable which should be good always.
			$temp_ip = $_SERVER['REMOTE_ADDR'];
		}

		// If the ip address is blank, use 127.0.0.1 (aka localhost).
		if( $temp_ip == '' ) { $temp_ip = '127.0.0.1'; }
		
		self::$IP = $temp_ip;
		
		return self::$IP;
	}




	/*
	 * Get all countries
	 *
	 * @access public
	 * @return null
	*/
	public static function get_countries() 
	{	
		$countries = array(
			'AF' => 'Afghanistan',
			'AX' => 'Aland Islands',
			'AL' => 'Albania',
			'DZ' => 'Algeria',
			'AS' => 'American Samoa',
			'AD' => 'Andorra',
			'AO' => 'Angola',
			'AI' => 'Anguilla',
			'AQ' => 'Antarctica',
			'AG' => 'Antigua And Barbuda',
			'AR' => 'Argentina',
			'AM' => 'Armenia',
			'AW' => 'Aruba',
			'AU' => 'Australia',
			'AT' => 'Austria',
			'AZ' => 'Azerbaijan',
			'BS' => 'Bahamas',
			'BH' => 'Bahrain',
			'BD' => 'Bangladesh',
			'BB' => 'Barbados',
			'BY' => 'Belarus',
			'BE' => 'Belgium',
			'BZ' => 'Belize',
			'BJ' => 'Benin',
			'BM' => 'Bermuda',
			'BT' => 'Bhutan',
			'BO' => 'Bolivia',
			'BA' => 'Bosnia And Herzegovina',
			'BW' => 'Botswana',
			'BV' => 'Bouvet Island',
			'BR' => 'Brazil',
			'IO' => 'British Indian Ocean Territory',
			'BN' => 'Brunei Darussalam',
			'BG' => 'Bulgaria',
			'BF' => 'Burkina Faso',
			'BI' => 'Burundi',
			'KH' => 'Cambodia',
			'CM' => 'Cameroon',
			'CA' => 'Canada',
			'CV' => 'Cape Verde',
			'KY' => 'Cayman Islands',
			'CF' => 'Central African Republic',
			'TD' => 'Chad',
			'CL' => 'Chile',
			'CN' => 'China',
			'CX' => 'Christmas Island',
			'CC' => 'Cocos (Keeling) Islands',
			'CO' => 'Colombia',
			'KM' => 'Comoros',
			'CG' => 'Congo',
			'CD' => 'Congo, Democratic Republic',
			'CK' => 'Cook Islands',
			'CR' => 'Costa Rica',
			'CI' => 'Cote D\'Ivoire',
			'HR' => 'Croatia',
			'CU' => 'Cuba',
			'CY' => 'Cyprus',
			'CZ' => 'Czech Republic',
			'DK' => 'Denmark',
			'DJ' => 'Djibouti',
			'DM' => 'Dominica',
			'DO' => 'Dominican Republic',
			'EC' => 'Ecuador',
			'EG' => 'Egypt',
			'SV' => 'El Salvador',
			'GQ' => 'Equatorial Guinea',
			'ER' => 'Eritrea',
			'EE' => 'Estonia',
			'ET' => 'Ethiopia',
			'FK' => 'Falkland Islands (Malvinas)',
			'FO' => 'Faroe Islands',
			'FJ' => 'Fiji',
			'FI' => 'Finland',
			'FR' => 'France',
			'GF' => 'French Guiana',
			'PF' => 'French Polynesia',
			'TF' => 'French Southern Territories',
			'GA' => 'Gabon',
			'GM' => 'Gambia',
			'GE' => 'Georgia',
			'DE' => 'Germany',
			'GH' => 'Ghana',
			'GI' => 'Gibraltar',
			'GR' => 'Greece',
			'GL' => 'Greenland',
			'GD' => 'Grenada',
			'GP' => 'Guadeloupe',
			'GU' => 'Guam',
			'GT' => 'Guatemala',
			'GG' => 'Guernsey',
			'GN' => 'Guinea',
			'GW' => 'Guinea-Bissau',
			'GY' => 'Guyana',
			'HT' => 'Haiti',
			'HM' => 'Heard Island & Mcdonald Islands',
			'VA' => 'Holy See (Vatican City State)',
			'HN' => 'Honduras',
			'HK' => 'Hong Kong',
			'HU' => 'Hungary',
			'IS' => 'Iceland',
			'IN' => 'India',
			'ID' => 'Indonesia',
			'IR' => 'Iran, Islamic Republic Of',
			'IQ' => 'Iraq',
			'IE' => 'Ireland',
			'IM' => 'Isle Of Man',
			'IL' => 'Israel',
			'IT' => 'Italy',
			'JM' => 'Jamaica',
			'JP' => 'Japan',
			'JE' => 'Jersey',
			'JO' => 'Jordan',
			'KZ' => 'Kazakhstan',
			'KE' => 'Kenya',
			'KI' => 'Kiribati',
			'KR' => 'Korea',
			'KW' => 'Kuwait',
			'KG' => 'Kyrgyzstan',
			'LA' => 'Lao People\'s Democratic Republic',
			'LV' => 'Latvia',
			'LB' => 'Lebanon',
			'LS' => 'Lesotho',
			'LR' => 'Liberia',
			'LY' => 'Libyan Arab Jamahiriya',
			'LI' => 'Liechtenstein',
			'LT' => 'Lithuania',
			'LU' => 'Luxembourg',
			'MO' => 'Macao',
			'MK' => 'Macedonia',
			'MG' => 'Madagascar',
			'MW' => 'Malawi',
			'MY' => 'Malaysia',
			'MV' => 'Maldives',
			'ML' => 'Mali',
			'MT' => 'Malta',
			'MH' => 'Marshall Islands',
			'MQ' => 'Martinique',
			'MR' => 'Mauritania',
			'MU' => 'Mauritius',
			'YT' => 'Mayotte',
			'MX' => 'Mexico',
			'FM' => 'Micronesia, Federated States Of',
			'MD' => 'Moldova',
			'MC' => 'Monaco',
			'MN' => 'Mongolia',
			'ME' => 'Montenegro',
			'MS' => 'Montserrat',
			'MA' => 'Morocco',
			'MZ' => 'Mozambique',
			'MM' => 'Myanmar',
			'NA' => 'Namibia',
			'NR' => 'Nauru',
			'NP' => 'Nepal',
			'NL' => 'Netherlands',
			'AN' => 'Netherlands Antilles',
			'NC' => 'New Caledonia',
			'NZ' => 'New Zealand',
			'NI' => 'Nicaragua',
			'NE' => 'Niger',
			'NG' => 'Nigeria',
			'NU' => 'Niue',
			'NF' => 'Norfolk Island',
			'MP' => 'Northern Mariana Islands',
			'NO' => 'Norway',
			'OM' => 'Oman',
			'PK' => 'Pakistan',
			'PW' => 'Palau',
			'PS' => 'Palestinian Territory, Occupied',
			'PA' => 'Panama',
			'PG' => 'Papua New Guinea',
			'PY' => 'Paraguay',
			'PE' => 'Peru',
			'PH' => 'Philippines',
			'PN' => 'Pitcairn',
			'PL' => 'Poland',
			'PT' => 'Portugal',
			'PR' => 'Puerto Rico',
			'QA' => 'Qatar',
			'RE' => 'Reunion',
			'RO' => 'Romania',
			'RU' => 'Russian Federation',
			'RW' => 'Rwanda',
			'BL' => 'Saint Barthelemy',
			'SH' => 'Saint Helena',
			'KN' => 'Saint Kitts And Nevis',
			'LC' => 'Saint Lucia',
			'MF' => 'Saint Martin',
			'PM' => 'Saint Pierre And Miquelon',
			'VC' => 'Saint Vincent And Grenadines',
			'WS' => 'Samoa',
			'SM' => 'San Marino',
			'ST' => 'Sao Tome And Principe',
			'SA' => 'Saudi Arabia',
			'SN' => 'Senegal',
			'RS' => 'Serbia',
			'SC' => 'Seychelles',
			'SL' => 'Sierra Leone',
			'SG' => 'Singapore',
			'SK' => 'Slovakia',
			'SI' => 'Slovenia',
			'SB' => 'Solomon Islands',
			'SO' => 'Somalia',
			'ZA' => 'South Africa',
			'GS' => 'South Georgia And Sandwich Isl.',
			'ES' => 'Spain',
			'LK' => 'Sri Lanka',
			'SD' => 'Sudan',
			'SR' => 'Suriname',
			'SJ' => 'Svalbard And Jan Mayen',
			'SZ' => 'Swaziland',
			'SE' => 'Sweden',
			'CH' => 'Switzerland',
			'SY' => 'Syrian Arab Republic',
			'TW' => 'Taiwan',
			'TJ' => 'Tajikistan',
			'TZ' => 'Tanzania',
			'TH' => 'Thailand',
			'TL' => 'Timor-Leste',
			'TG' => 'Togo',
			'TK' => 'Tokelau',
			'TO' => 'Tonga',
			'TT' => 'Trinidad And Tobago',
			'TN' => 'Tunisia',
			'TR' => 'Turkey',
			'TM' => 'Turkmenistan',
			'TC' => 'Turks And Caicos Islands',
			'TV' => 'Tuvalu',
			'UG' => 'Uganda',
			'UA' => 'Ukraine',
			'AE' => 'United Arab Emirates',
			'GB' => 'United Kingdom',
			'US' => 'United States',
			'UM' => 'United States Outlying Islands',
			'UY' => 'Uruguay',
			'UZ' => 'Uzbekistan',
			'VU' => 'Vanuatu',
			'VE' => 'Venezuela',
			'VN' => 'Viet Nam',
			'VG' => 'Virgin Islands, British',
			'VI' => 'Virgin Islands, U.S.',
			'WF' => 'Wallis And Futuna',
			'EH' => 'Western Sahara',
			'YE' => 'Yemen',
			'ZM' => 'Zambia',
			'ZW' => 'Zimbabwe',
		);
		
		return $countries;
	}

}

endif;
?>