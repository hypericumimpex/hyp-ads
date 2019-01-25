<?php
// Exit if accessed directly
if ( ! defined( "ABSPATH" ) ) exit;
if ( ! class_exists( 'ADNI_Filters' ) ) :

class ADNI_Filters {

    static $post_count = 0;
	
	public function __construct() 
	{
        // Actions --------------------------------------------------------
        add_action( 'wp_head', array( __CLASS__, 'inject_header' ), 20 );
        add_action( 'adning_head', array( __CLASS__, 'debug_marker' ), 2 );
        add_action( 'wp_footer', array( __CLASS__, 'inject_footer' ), 20 );
        add_action( 'loop_start', array( __CLASS__, 'loop_start') );
        
        // Filters --------------------------------------------------------
        add_filter( 'the_content', array(__CLASS__, 'content_filter'));
    }




    /**
     * Check to see if ads disabled.
     * 
     * return (bool)
     */
    public static function disabled_ads( $settings = array())
    {
        $disabled = false;

        if( empty($settings) || !is_array($settings) )
        {
            $set_arr = ADNI_Main::settings();
            $settings = $set_arr['settings'];
        }

        // Disable All
        if( $settings['disable']['all_ads'] )
            return true; // disable ads
        
        // Logged-in Users
        if( is_user_logged_in() ) 
        {
            $user = wp_get_current_user();
            $role = ( array ) $user->roles;
            if( in_array($settings['disable']['user_role_ads'], $role))
            {
                return true; // disable ads
            }
        }

        

        // GDPR Content cookie
        if( !empty( $settings['gdpr']['disable_till_approved']))
        {
            if( !empty($settings['gdpr']['cookie_name']) || !empty($settings['gdpr']['show_cookie_message']))
            {
                $cookie_name = !empty($settings['gdpr']['show_cookie_message']) ? ADNI_GDPR_COOKIE : $settings['gdpr']['cookie_name'];
                $cookie_value = !empty($settings['gdpr']['show_cookie_message']) ? 1 : $settings['gdpr']['cookie_value'];

                if( !isset($_COOKIE[$cookie_name]) )
                    return true; // disable ads
                
                if( $_COOKIE[$cookie_name] != $cookie_value )
                    return true; // disable ads
            }
        }



        // Check if we can find a current post type. If so make sure ads have to show for this post type
        $post_type = get_post_type();
        if( $post_type && !in_array($post_type, $settings['positioning']['post_types']) )
            return true; // disable ads
        

        return $disabled;
    }





    /**
     * SHOW or HIDE Banner / Adzone
     * 
     */
    public static function show_hide( $ad )
    {
        global $post;
        //echo '<pre>'.print_r($ad).'</pre>';
       
        // Show post always when loaded from admin
        if( is_admin() )
            return $ad;

        if( self::disabled_ads() )
            return;
        
        $args = $ad['args'];

        // Campaigns
        if( !empty($args['campaigns']) )
        {
            foreach( $args['campaigns'] as $campaign )
            {
                if( !self::check_campaign( $campaign ) )
                    return;
            }
        }
        
        $display = array_key_exists('display_filter',$args) ? $args['display_filter'] : array();

        // AD display filter
        if( !empty($display) )
        {  
            if( $args['status'] !== 'active')
                return; 
            
            // HOME PAGE
            if( array_key_exists('homepage', $display) )
            {
                if( !$display['homepage'] && is_front_page() ) // is_home() doesn't work for static pages.
                    return;
            }
            
            
            // NON-SINGULAR. GLOBAL settings (should be under self::disabled_ads() ) however, 
            // we need to make sure ad doesn't have to show on the home page so we need to check that first.
            // That's why we add this here instead of under self::disabled_ads()
            if( !is_singular() )
            {
                $set_arr = ADNI_Main::settings();
                $settings = $set_arr['settings'];
                if( $settings['disable']['non_singular_ads'] )
                    return;
            }
                
        

            // COUNTRY FILTER
            if( !empty($display['countries']) )
            {
                if( array_key_exists('ids', $display['countries']))
                {
                    $show = array_key_exists('show_hide', $display['countries']) ? $display['countries']['show_hide'] : 0;
                    $visitor_country = self::get_country( ADNI_Main::get_visitor_ip() );
                    //$visitor_country = 'xx';
                    //$display['countries']['ids'] = array('xx');
                    //echo '<pre>'.print_r($display['countries']['ids'],true).'</pre>';
                    //echo $show.' -- '.$visitor_country.' -- '.in_array($visitor_country, $display['countries']['ids']);

                    if( !in_array($visitor_country, $display['countries']['ids']) && $show || in_array($visitor_country, $display['countries']['ids']) && !$show ){
                        return;
                    }
                }
            }

        
            // DEVICE FILTER
            if( empty($display['show_desktop']) && self::user_device() === 'desktop' )
                return;

            if( empty($display['show_tablet']) && self::user_device() === 'tablet' )
                return;
            
            if( empty($display['show_mobile']) && self::user_device() === 'mobile' )
                return;
            

            // CONTENT FILTER
            // Make sure a post ID is defined. If not we don't filter.
            if( !is_object($post) || !array_key_exists('ID',$post) )
                return $ad;

            if( array_key_exists('post_types', $display) && !empty($display['post_types']) )
            {
                $post_type = get_post_type($post->ID);
                
                if( array_key_exists($post_type, $display['post_types']))
                {
                    $post_arr = $display['post_types'][$post_type];
                    $show = array_key_exists('show_hide', $post_arr) ? $post_arr['show_hide'] : 0;
                    if( !empty($post_arr['ids']))
                    {
                        if( !in_array($post->ID, $post_arr['ids']) && $show || in_array($post->ID, $post_arr['ids']) && !$show ){
                            return;
                        }
                    }
                    else
                    {
                        if( !$show )
                            return;
                    }
                    

                    // Taxonomies
                    $taxonomy_names = get_post_taxonomies($post->ID);
                    if(!empty($taxonomy_names))
                    {
                        $tax_arr = array_key_exists('taxonomies', $post_arr) ? $post_arr['taxonomies'] : array();

                        foreach($taxonomy_names as $taxonomy)
                        {
                            $tax_arr = array_key_exists($taxonomy, $tax_arr) ? $tax_arr[$taxonomy] : array();
                            $show = array_key_exists('show_hide', $tax_arr) ? $tax_arr['show_hide'] : 0;
                            $ids = array_key_exists('ids', $tax_arr) ? $tax_arr['ids'] : array();
                            
                            if(!empty($ids))
                            {
                                $term_list = wp_get_post_terms($post->ID, $taxonomy, array("fields" => "ids"));
                                $match = array_intersect($term_list, $ids);
                                
                                if( empty($match) && $show || !empty($match) && !$show ){
                                    return;
                                }
                            }
                            else
                            {
                                // Always return the AD. Even when "hide" is selected but no taxonomies are selected.
                                return $ad;
                                /*if( !$show ) // && in_array($ad['post']->ID, ADNI_Main::auto_positioning())
                                {
                                    return;
                                }
                                else
                                {
                                    return $ad;
                                }*/
                            }
                        }
                    }
                    
                }
                else
                {
                    //if( !$show )
                    return;
                }
            }
            
        }

        return $ad;
    }







    public static function inject_header()
    {
        $set_arr = ADNI_Main::settings();
        $settings = $set_arr['settings'];

        /*
		 * Action: 'adning_head' - Allow other plugins to output inside the Adning section in the head section.
		*/
        do_action( 'adning_head' );
        
        echo stripslashes($settings['placement_area_head']);
        echo '<style>'.stripslashes($settings['custom_css']).'</style>';

        echo '<!-- / '."Adning. -->\n\n";
    }





    public static function inject_footer()
    {
        $h = '';
        $set_arr = ADNI_Main::settings();
        $settings = $set_arr['settings'];

        $h.= stripslashes($settings['placement_area_body']);

        // GDPR Cookie message
        if( $settings['gdpr']['show_cookie_message'])
        {
            $h.= ADNI_Templates::gdpr_approve_modal_tpl(array(
                'text' => $settings['gdpr']['cookie_message_text'],
                'page_url' => $settings['gdpr']['cookie_message_page_url']
            ));
        }


        if( self::disabled_ads($settings) )
        {
            echo $h;
            return;
        }

        // Check for banners
        $auto_pos = ADNI_Main::auto_positioning();
        if( !empty($auto_pos) )
        {
            foreach($auto_pos as $key => $arr)
            {
                $pos = key($arr);
                if($pos === 'popup')
                {
                    $b = ADNI_Multi::get_post_meta($key, '_adning_args', array());
                    
                    if( self::show_hide(array('args' => $b)) )
                    {
                        $css = '';
                        $css.= !empty($arr[$pos]['shadow_color']) ? '"box-shadow":"'.$arr[$pos]['shadow_color'].' 0px 5px 20px 0px"' : '"box-shadow":"none"';
                        
                        $h.= '<div id="mdl-elmt-'.$key.'"><div class="mdl_content">'.ADNI_Multi::do_shortcode('[adning id="'.$key.'"]').'</div></div>';
                        $h.= '<script>';
                            $h.= 'if( jQuery("#mdl-elmt-'.$key.'").find(".mdl_content").html() !== "" ){';
                                $h.= 'jQuery("#mdl-elmt-'.$key.'").modalJS({';
                                    $h.= 'width:"'.$arr[$pos]['width'].'",';
                                    $h.= 'height:"'.$arr[$pos]['height'].'",';
                                    $h.= !empty( $arr[$pos]['bg_color'] ) ? 'bg_color:"'.$arr[$pos]['bg_color'].'",' : '';
                                    $h.= !empty( $arr[$pos]['overlay_color'] ) ? 'overlay_color:"'.$arr[$pos]['overlay_color'].'",' : '';
                                    $h.= !empty($css) ? 'css:{'.$css.'},' : '';
                                    $h.= !empty($arr[$pos]['cookie_value']) ? 'cookie: {"expires":"'.$arr[$pos]['cookie_value'].'","type":"'.$arr[$pos]['cookie_type'].'"}' : '';
                                    $h.= !empty( $arr[$pos]['custom_json'] ) ? stripslashes($arr[$pos]['custom_json']) : '';
                                $h.= '});';
                            $h.= '}';
                        $h.= '</script>';
                    }
                }

                if($pos === 'cornerpeel')
                {
                    $b = ADNI_Multi::get_post_meta($key, '_adning_args', array());
                    
                    if( self::show_hide(array('args' => $b)) )
                    {
                        $h.= '<div id="crp-elmt-'.$key.'">'.ADNI_Multi::do_shortcode('[adning id="'.$key.'"]').'</div>';
                        $h.= '<script>jQuery("#crp-elmt-'.$key.'").cornerPeel({cornerAnimate:0});</script>';
                    }
                }

                if($pos === 'bg_takeover')
                {
                    $b = ADNI_Multi::get_post_meta($key, '_adning_args', array());
                    
                    if( self::show_hide(array('args' => $b)) )
                    {
                        $bg_container = !empty($b['bg_takeover_bg_container']) ? $b['bg_takeover_bg_container'] : 'body';
                        // URLs
                        $top_url = $b['banner_link_masking'] ? ADNI_Main::link_masking(array('id' => $key, 'bg_ad' => 'top' )) : $b['bg_takeover_top_skin_url'];
                        $left_url = $b['banner_link_masking'] ? ADNI_Main::link_masking(array('id' => $key, 'bg_ad' => 'left' )) : $b['bg_takeover_left_skin_url'];
                        $right_url = $b['banner_link_masking'] ? ADNI_Main::link_masking(array('id' => $key, 'bg_ad' => 'right' )) : $b['bg_takeover_right_skin_url'];


                        $h.= '<script>';
                            $h.= "jQuery('".$bg_container."').bgTakeover({
                                bg_image: '".$b['bg_takeover_src']."',
                                bg_color: '".$b['bg_takeover_bg_color']."',
                                content_bg_color: '".$b['bg_takeover_content_bg_color']."',
                                bg_pos: '".$b['bg_takeover_position']."',
                                top_skin: '".$b['bg_takeover_top_skin']."',
                                container: '".$b['bg_takeover_content_container']."',
                                click_url: {
                                    'top': '".$top_url."',
                                    'left': '".$left_url."',
                                    'right': '".$right_url."'
                                }
                            });";
                        $h.= '</script>';
                        /*
                        click_url: {
                            'top': '".$b['bg_takeover_top_skin_url']."',
                            'left': '".$b['bg_takeover_left_skin_url']."',
                            'right': '".$b['bg_takeover_right_skin_url']."'
                        }
                        */
                    }
                }

                if($pos === 'js_inject')
                {
                    $b = ADNI_Multi::get_post_meta($key, '_adning_args', array());
                    
                    if( self::show_hide(array('args' => $b)) )
                    {
                        if( !empty($arr[$pos]['element']))
                        {
                            $h.= '<div id="inject-elmt-'.$key.'">'.ADNI_Multi::do_shortcode('[adning id="'.$key.'"]').'</div>';
                            $h.= '<script>';
                                $h.= 'var ins_where = "'.$arr[$pos]['where'].'";';
                                $h.= 'var ins_element = "'.$arr[$pos]['element'].'";';

                                // Make sure element exists.
                                $h.= "jQuery(document).ready(function($){";
                                    $h.= 'if( jQuery(ins_element).length ){';
                                        $h.= 'jQuery("#inject-elmt-'.$key.'").modalJS({';
                                            $h.= 'type: "inline",';
                                            $h.= 'insert: {"target":ins_element,"where":ins_where}';
                                        $h.= '});';
                                    $h.= '}else{
                                        jQuery("#inject-elmt-'.$key.'").remove();
                                    }';
                                $h.= '});';

                                /*$h.= "jQuery(document).ready(function($){
                                    if( ins_element !== '' ){
                                        var where = ins_where !== '' ? ins_where : 'after';
                                        if( where === 'before'){
                                            $(ins_element).before( $('#inject-elmt-'+$key) );
                                        }else{
                                            $(ins_element).after( $('#inject-elmt-'+$key) );
                                        }
                                    }
                                });";*/
                            $h.= '</script>';
                        }
                    }
                }
            }
        }

        echo $h;
    }




    

    



    public static function content_filter( $content )
    {
        $set_arr = ADNI_Main::settings();
        $settings = $set_arr['settings'];

        $auto_pos = ADNI_Main::auto_positioning();
        if( empty($auto_pos) )
            return $content;
        
        if( !is_singular() && $settings['disable']['non_singular_ads'] )
            return $content;
        
        $above_content = '';
        $below_content = '';

        foreach($auto_pos as $key => $arr)
        {
            $pos = key($arr);
            if($pos === 'above_content')
            {
                //$above_content.= '[adning id="'.$key.'" no_iframe="1"]';
                $above_content.= ADNI_Shortcodes::sc_adning(array('id' => $key));
            }
            if($pos === 'inside_content')
            {
                if ( is_singular() && !is_admin() )  // is_single() is not for pages!
                {
                    //$after_x_p = array_key_exists('after_x_p', $arr[$pos]) ? $arr[$pos]['after_x_p'] : 2;
                    //$after_x_p = !empty($arr[$pos]['after_x_p']) ? $arr[$pos]['after_x_p'] : 2;
                    if( !empty($arr[$pos]['after_x_p']) )
                    {
                        $repeat = array_key_exists('after_x_p_repeat', $arr[$pos]) ? $arr[$pos]['after_x_p_repeat'] : 0;
                        $content = self::insert_after_paragraph( '[adning id="'.$key.'" no_iframe="1"]', $arr[$pos]['after_x_p'], $repeat, $content );
                    }
                }
            } 
            if($pos === 'below_content')
            {
                $below_content.= '[adning id="'.$key.'" no_iframe="1"]';
            }
        }
        return $above_content.$content.$below_content;
    }



    // INSERT AD AFTER X PARAGRAPHS
    public static function insert_after_paragraph( $insertion, $after_x_p, $repeat, $content ) 
    {
        $closing_p = '</p>';
        $paragraphs = explode( $closing_p, $content );
        if (count($paragraphs) >= $insertion) 
        {
            $p = 1;
            foreach ($paragraphs as $index => $paragraph) 
            {
                if ( trim( $paragraph ) ) {
                    $paragraphs[$index] .= $closing_p;
                }
    
                if ( $after_x_p == $p ) {
                    $paragraphs[$index] .= $insertion;
                    $p = $repeat ? 0 : $p;
                }

                $p++;
            }
    
            return implode( '', $paragraphs );
        }
    
        return $content;
    }





    /**
     * WP LOOP
     */
    public static function loop_start($query)
    {
        if( is_singular() || is_admin() )
            return;

        add_action( 'the_post', array(__CLASS__, 'post_in_loop') );
        add_action( 'loop_end', array(__CLASS__, 'loop_end') );
    }
    public static function post_in_loop($post_object)
    {
        $auto_pos = ADNI_Main::auto_positioning();
        if( empty($auto_pos) ) // || is_single() || is_admin()
            return;

        foreach($auto_pos as $key => $arr)
        {
            $pos = key($arr);
            if($pos === 'inside_content')
            {
                if( !empty($arr[$pos]['after_x_post']) )
                {
                    $repeat = array_key_exists('after_x_post_repeat', $arr[$pos]) ? $arr[$pos]['after_x_post_repeat'] : 0;
                    if( self::$post_count == $arr[$pos]['after_x_post'] )
                    {
                        echo ADNI_Multi::do_shortcode('[adning id="'.$key.'"]');
                        self::$post_count = $repeat ? 0 : self::$post_count;
                    }
                }
            }
        }

        self::$post_count++;
    }
    public static function loop_end()
    {
        remove_action( 'the_post', 'post_in_loop' );   
    }




    /**
	 * Return the user device
	 *
	 */
    public static function user_device()
    {
        if( !self::device(array('is' => 'is_tablet')) && !self::device(array('is' => 'is_mobile')) )
            return 'desktop';
        
        if( self::device(array('is' => 'is_tablet')) )
            return 'tablet';
        
        if( self::device(array('is' => 'is_mobile')) )
            return 'mobile';
    }



    /**
	 * FUNCTION TO CHECK USER DEVICE
	 *
	 */
	public static function device($args = array())
	{
		$detect = new ADNI_Mobile_Detect();
		
		$defaults = array(
			'is' => 'is_mobile'
		);
		$args = wp_parse_args( $args, $defaults );
		
		if( $args['is'] == 'is_mobile')
		{
			return apply_filters( 'is_mobile', $detect->isMobile(), $detect );
		}
		elseif( $args['is'] == 'is_tablet')
		{
			return apply_filters( 'is_tablet', $detect->isTablet(), $detect );	
		}
		elseif( $args['is'] == 'is_iphone')
		{
			return apply_filters( 'is_iphone', $detect->isIphone(), $detect );	
		}
		elseif( $args['is'] == 'is_ipad')
		{
			return apply_filters( 'is_ipad', $detect->isIpad(), $detect );
		}
		elseif( $args['is'] == 'is_ipod')
		{
			return apply_filters( 'is_ipod', $detect->is( 'iPod' ), $detect );
		}
		elseif( $args['is'] == 'is_ios')
		{
			return apply_filters( 'is_ios', $detect->isiOS(), $detect );	
		}
		elseif( $args['is'] == 'is_android')
		{
			return apply_filters( 'is_android', $detect->isAndroidOS(), $detect );	
		}
		elseif( $args['is'] == 'is_blackberry')
		{
			return apply_filters( 'is_blackberry', $detect->isBlackBerry(), $detect );	
		}
		elseif( $args['is'] == 'is_windows_mobile')
		{
			return apply_filters(
				'is_windows_mobile',
				( $detect->is( 'WindowsMobileOS' ) || $detect->is( 'WindowsPhoneOS' ) ),
				$detect
			);
		}
		elseif( $args['is'] == 'is_motorola')
		{
			return apply_filters( 'is_motorola', $detect->is( 'Motorola' ), $detect );	
		}
		elseif( $args['is'] == 'is_samsung')
		{
			return apply_filters( 'is_samsung', $detect->is( 'Samsung' ), $detect );	
		}
		elseif( $args['is'] == 'is_sony_ericsson')
		{
			return apply_filters( 'is_sony_ericsson', $detect->is( 'Sony' ), $detect );	
		}
    }
    





    /**
	 * Get Country by IP Address
	 *
	 * Maxmind
	 * http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz
     * 
     * usage: ADNI_Filters::get_country( $ip );
	 */
	public static function get_country( $ip )
	{
		$ip_num = (float) sprintf( '%u', bindec( self::dtr_pton( $ip )));
		$country_cd = 'xx';
			
		if( file_exists( ADNI_INC_DIR.'/extensions/maxmind/GeoIP.dat' ) && ( $handle = fopen( ADNI_INC_DIR.'/extensions/maxmind/GeoIP.dat', 'rb' ))) 
		{
			$country_codes = array('','ap','eu','ad','ae','af','ag','ai','al','am','cw','ao','aq','ar','as','at','au','aw','az','ba','bb','bd','be','bf','bg','bh','bi','bj','bm','bn','bo','br','bs','bt','bv','bw','by','bz','ca','cc','cd','cf','cg','ch','ci','ck','cl','cm','cn','co','cr','cu','cv','cx','cy','cz','de','dj','dk','dm','do','dz','ec','ee','eg','eh','er','es','et','fi','fj','fk','fm','fo','fr','sx','ga','gb','gd','ge','gf','gh','gi','gl','gm','gn','gp','gq','gr','gs','gt','gu','gw','gy','hk','hm','hn','hr','ht','hu','id','ie','il','in','io','iq','ir','is','it','jm','jo','jp','ke','kg','kh','ki','km','kn','kp','kr','kw','ky','kz','la','lb','lc','li','lk','lr','ls','lt','lu','lv','ly','ma','mc','md','mg','mh','mk','ml','mm','mn','mo','mp','mq','mr','ms','mt','mu','mv','mw','mx','my','mz','na','nc','ne','nf','ng','ni','nl','no','np','nr','nu','nz','om','pa','pe','pf','pg','ph','pk','pl','pm','pn','pr','ps','pt','pw','py','qa','re','ro','ru','rw','sa','sb','sc','sd','se','sg','sh','si','sj','sk','sl','sm','sn','so','sr','st','sv','sy','sz','tc','td','tf','tg','th','tj','tk','tm','tn','to','tl','tr','tt','tv','tw','tz','ua','ug','um','us','uy','uz','va','vc','ve','vg','vi','vn','vu','wf','ws','ye','yt','rs','za','zm','me','zw','a1','a2','o1','ax','gg','im','je','bl','mf','bq','ss','o1');
				
			$offset = 0;
			for($depth = 31; $depth >= 0; --$depth) 
			{
				if (fseek($handle, 6 * $offset, SEEK_SET) != 0)
				{
					break;
				}
				$buf = fread($handle, 6);
				$cd = array(0,0);
				for($i = 0; $i < 2; ++$i) 
				{
					for($j = 0; $j < 3; ++$j) 
					{
						$cd[$i] += ord(substr($buf, 3 * $i + $j, 1)) << ($j * 8);
					}
				}
		
				if( $ip_num & ( 1 << $depth )) 
				{
					if($cd[1] >= 16776960 && !empty($country_codes[$cd[1] - 16776960])) 
					{
						$country_cd = $country_codes[$cd[1] - 16776960];
						break;
					}
					$offset = $cd[1];
				} 
				else 
				{
					if($cd[0] >= 16776960 && !empty($country_codes[$cd[0] - 16776960])) 
					{
						$country_cd = $country_codes[$cd[0] - 16776960];
						break;
					}
					$offset = $cd[0];
				}
			}
			fclose($handle);
		}
		
		return strtoupper($country_cd);
    }

    public static function dtr_pton( $ip )
	{
		if( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) 
		{
			$unpacked = unpack( 'A4', inet_pton( $ip ) );
		}
		elseif( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) && defined( 'AF_INET6' )) 
		{
			$unpacked = unpack( 'A16', inet_pton( $ip ) );
		}

		$binary_ip = '';
		if( !empty( $unpacked )) 
		{
			$unpacked = str_split( $unpacked[ 1 ] );
			foreach( $unpacked as $char ) 
			{
				$binary_ip .= str_pad( decbin( ord( $char ) ), 8, '0', STR_PAD_LEFT );
			}
		}
		return $binary_ip;
	}
    





    /*
	 * Outputs or returns the debug marker.
	 *
	 * @param bool $echo Whether or not to echo the debug marker.
	 *
	 * @return string
	*/
	public static function debug_marker( $echo = true ) 
	{
		$marker = '<!-- Ads on this site are served by Adning v' . ADNI_VERSION . ' - adning.com -->';
		if ( $echo === false ) {
			return $marker;
		}
		else {
			echo "\n${marker}\n";
		}
    }
    



    /**
     * Check campaign status (active or not)
     * 
     * return: (bool) true:false;
     */
    public static function check_campaign( $id = 0 )
    {
        $active = true;
        $campaign_post = ADNI_CPT::load_post($id, array('post_type' => ADNI_CPT::$campaign_cpt, 'filter' => 0));
        
        if( !empty($campaign_post))
        {        
            $c = $campaign_post['args'];
            // echo '<pre>'.print_r($c, true).'</pre>';

            // Months
            if( !empty($c['display_filter']['months']) )
            {
                $show = array_key_exists('show_hide', $c['display_filter']['months']) ? $c['display_filter']['months']['show_hide'] : 0;
                $ids = array_key_exists('ids', $c['display_filter']['months']) ? $c['display_filter']['months']['ids'] : array();
                $today = date( 'M', current_time( 'timestamp' ) ); //date_i18n( 'D', current_time( 'timestamp' ) );
                $today = strtolower($today);

                if( !empty($ids))
                {
                    if( !in_array($today, $ids) && $show || in_array($today, $ids) && !$show ){
                        return;
                    }
                }
            }

            // Days
            if( !empty($c['display_filter']['days']) )
            {
                $show = array_key_exists('show_hide', $c['display_filter']['days']) ? $c['display_filter']['days']['show_hide'] : 0;
                $ids = array_key_exists('ids', $c['display_filter']['days']) ? $c['display_filter']['days']['ids'] : array();
                $today = date( 'j', current_time( 'timestamp' ) ); //date_i18n( 'D', current_time( 'timestamp' ) );
                $today = strtolower($today);

                if( !empty($ids))
                {
                    if( !in_array($today, $ids) && $show || in_array($today, $ids) && !$show ){
                        return;
                    }
                }
            }

            // Weekdays
            if( !empty($c['display_filter']['weekdays']) )
            {
                $show = array_key_exists('show_hide', $c['display_filter']['weekdays']) ? $c['display_filter']['weekdays']['show_hide'] : 0;
                $ids = array_key_exists('ids', $c['display_filter']['weekdays']) ? $c['display_filter']['weekdays']['ids'] : array();
                $today = date( 'D', current_time( 'timestamp' ) ); //date_i18n( 'D', current_time( 'timestamp' ) );
                $today = strtolower($today);

                if( !empty($ids))
                {
                    if( !in_array($today, $ids) && $show || in_array($today, $ids) && !$show ){
                        return;
                    }
                }
            }

            // Time
            if( !empty($c['display_filter']['time']) )
            {
                $show = array_key_exists('show_hide', $c['display_filter']['time']) ? $c['display_filter']['time']['show_hide'] : 0;
                $ids = array_key_exists('ids', $c['display_filter']['time']) ? $c['display_filter']['time']['ids'] : array();
                $today = date( 'G', current_time( 'timestamp' ) ); //date_i18n( 'D', current_time( 'timestamp' ) );
                $today = strtolower($today);

                if( !empty($ids))
                {
                    if( !in_array($today, $ids) && $show || in_array($today, $ids) && !$show ){
                        return;
                    }
                }
            }

            // Years
            if( !empty($c['display_filter']['years']) )
            {
                $show = array_key_exists('show_hide', $c['display_filter']['years']) ? $c['display_filter']['years']['show_hide'] : 0;
                $ids = array_key_exists('ids', $c['display_filter']['years']) ? $c['display_filter']['years']['ids'] : array();
                $today = date( 'Y', current_time( 'timestamp' ) ); //date_i18n( 'D', current_time( 'timestamp' ) );
                $today = strtolower($today);

                if( !empty($ids))
                {
                    if( !in_array($today, $ids) && $show || in_array($today, $ids) && !$show ){
                        return;
                    }
                }
            }

            // COUNTRY FILTER
            if( !empty($c['display_filter']['countries']) )
            {
                if( array_key_exists('ids', $c['display_filter']['countries']))
                {
                    $show = array_key_exists('show_hide', $c['display_filter']['countries']) ? $c['display_filter']['countries']['show_hide'] : 0;
                    $visitor_country = ADNI_Filters::get_country( ADNI_Main::get_visitor_ip() );
                    //$visitor_country = 'xx';
                    //$display['countries']['ids'] = array('xx');

                    if( !in_array($visitor_country, $c['display_filter']['countries']['ids']) && $show || in_array($visitor_country, $c['display_filter']['countries']['ids']) && !$show ){
                        return;
                    }
                }
            }
        }

        return $active;
    }

}

endif;
?>